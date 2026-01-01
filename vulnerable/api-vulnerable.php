<?php
// VULNERABLE API ENDPOINTS - Multiple API Security Issues
// WARNING: FOR TRAINING PURPOSES ONLY
// Hidden Hint: No authentication, no rate limiting, excessive data exposure

// VULNERABILITY: CORS Misconfiguration
// Hidden Hint: Allow any origin to access API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: *');

// VULNERABILITY: No authentication required
// Hidden Hint: Public API with no API keys or tokens

include("config-vulnerable.php");
header('Content-Type: application/json');

$response = [];

// API endpoint routing
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
$method = $_SERVER['REQUEST_METHOD'];

switch($endpoint) {
    case 'users':
        // VULNERABILITY: Excessive Data Exposure
        // Hidden Hint: Returns all user data including passwords
        if($method == 'GET') {
            $query = "SELECT * FROM users";
            if(isset($_GET['id'])) {
                // VULNERABILITY: SQL Injection in API
                $query .= " WHERE id='".$_GET['id']."'";
            }
            $result = mysqli_query($con, $query);
            $users = [];
            while($row = mysqli_fetch_assoc($result)) {
                // Hidden Hint: Password hashes exposed
                $users[] = $row;
            }
            $response = ['success' => true, 'data' => $users];
        }
        // VULNERABILITY: Mass Assignment in API
        // Hidden Hint: Can set admin role via API
        elseif($method == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            extract($data); // Vulnerable!
            $role = isset($role) ? $role : 'user';
            $is_admin = isset($is_admin) ? $is_admin : 0;
            // No validation - can create admin users
        }
        break;
        
    case 'appointments':
        // VULNERABILITY: BOLA (Broken Object Level Authorization)
        // Hidden Hint: No authorization check, can access any appointment
        if($method == 'GET') {
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $query = "SELECT * FROM appointments WHERE id='".$id."'";
            $result = mysqli_query($con, $query);
            $response = ['success' => true, 'data' => mysqli_fetch_assoc($result)];
        }
        // VULNERABILITY: Can modify any appointment
        elseif($method == 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            // No ownership check!
            $query = "UPDATE appointments SET status='cancelled' WHERE id='".$id."'";
            mysqli_query($con, $query);
            $response = ['success' => true, 'message' => 'Appointment cancelled'];
        }
        break;
        
    case 'search':
        // VULNERABILITY: No rate limiting
        // Hidden Hint: Can be used for enumeration attacks
        $term = isset($_GET['q']) ? $_GET['q'] : '';
        // SQL Injection here too
        $query = "SELECT * FROM users WHERE fullname LIKE '%".$term."%'";
        $result = mysqli_query($con, $query);
        $results = [];
        while($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
        $response = ['success' => true, 'data' => $results, 'count' => count($results)];
        break;
        
    case 'batch':
        // VULNERABILITY: Batch Requests without limits
        // Hidden Hint: Can send 1000s of requests in single batch
        $requests = json_decode(file_get_contents('php://input'), true);
        $responses = [];
        foreach($requests['requests'] as $req) {
            // Process unlimited requests - DoS risk
            $responses[] = ['status' => 'processed'];
        }
        $response = ['success' => true, 'responses' => $responses];
        break;
        
    case 'admin':
        // VULNERABILITY: Broken Function Level Authorization
        // Hidden Hint: Admin endpoints accessible without admin check
        if($method == 'DELETE') {
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            // No admin check!
            $query = "DELETE FROM users WHERE id='".$id."'";
            mysqli_query($con, $query);
            $response = ['success' => true, 'message' => 'User deleted'];
        }
        break;
        
    case 'debug':
        // VULNERABILITY: Information Disclosure
        // Hidden Hint: Debug endpoint exposes system info
        $response = [
            'success' => true,
            'server_info' => $_SERVER,
            'php_version' => phpversion(),
            'db_host' => DB_SERVER,
            'db_name' => DB_NAME,
            'db_user' => DB_USER,
            'loaded_extensions' => get_loaded_extensions()
        ];
        break;
        
    default:
        // VULNERABILITY: Improper Asset Management
        // Hidden Hint: No API versioning, deprecated endpoints still active
        $response = [
            'error' => 'Unknown endpoint',
            'hint' => 'Try: users, appointments, search, batch, admin, debug'
        ];
}

// VULNERABILITY: Verbose error messages
if(mysqli_error($con)) {
    $response['sql_error'] = mysqli_error($con);
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
