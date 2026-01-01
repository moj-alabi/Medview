<?php
// VULNERABLE APPOINTMENT HISTORY
// WARNING: FOR TRAINING PURPOSES ONLY

session_start();
include("config-vulnerable.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['login'])) {
    header("location: user-login-sqli.php");
    exit();
}

$email = $_SESSION['login'];
// VULNERABILITY: SQL Injection
$query = "SELECT * FROM users WHERE email='".$email."'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_array($result);

// VULNERABILITY: IDOR - Can view any user's appointments by manipulating user_id parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $user['id'];

// VULNERABILITY: SQL Injection in appointments query
$apt_query = "SELECT a.*, d.doctorName, d.specialization FROM appointments a 
              JOIN doctors d ON a.doctorId=d.id 
              WHERE a.userId='".$user_id."' 
              ORDER BY a.appointmentDate DESC, a.appointmentTime DESC";
$appointments = mysqli_query($con, $apt_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Appointment History - Vulnerable HMS</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .warning-banner { background: #dc3545; color: white; padding: 10px; text-align: center; font-weight: bold; }
        .sidebar { background: #343a40; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; display: block; padding: 10px; text-decoration: none; margin-bottom: 5px; }
        .sidebar a:hover { background: #495057; }
        .content { padding: 30px; }
        .status-pending { background: #ffc107; color: black; }
        .status-approved { background: #28a745; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="warning-banner">WARNING: VULNERABLE VERSION - FOR TRAINING ONLY WARNING:</div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 style="color: white;">Medview+ HMS</h4>
                <hr style="background: white;">
                <!-- VULNERABILITY: XSS -->
                <p style="color: white;">Welcome, <?php echo $user['fullname']; ?></p>
                
                <a href="dashboard-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">dashboard</i> Dashboard</a>
                <a href="book-appointment-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">event</i> Book Appointment</a>
                <a href="appointment-history-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">history</i> Appointment History</a>
                <a href="medical-history-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">description</i> Medical History</a>
                <a href="profile-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">person</i> My Profile</a>
                <a href="user-login-sqli.php"><i class="material-icons" style="vertical-align: middle;">exit_to_app</i> Logout</a>
            </div>

            <div class="col-md-10 content">
                <h2>Appointment History</h2>
                <p class="text-muted">Viewing appointments for User ID: <?php echo $user_id; ?></p>

                <!-- VULNERABILITY: IDOR demonstration -->
                <div class="alert alert-warning">
                    <strong>IDOR Vulnerability:</strong> Try changing user_id in URL: 
                    <code>?user_id=1</code>, <code>?user_id=2</code>, etc.
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(mysqli_num_rows($appointments) > 0) {
                                    while($apt = mysqli_fetch_array($appointments)) {
                                        echo "<tr>";
                                        echo "<td>".$apt['id']."</td>";
                                        // VULNERABILITY: XSS - not encoding output
                                        echo "<td>".$apt['doctorName']."</td>";
                                        echo "<td>".$apt['specialization']."</td>";
                                        echo "<td>".$apt['appointmentDate']."</td>";
                                        echo "<td>".$apt['appointmentTime']."</td>";
                                        
                                        $status_class = "status-".$apt['status'];
                                        echo "<td><span class='badge ".$status_class."'>".$apt['status']."</span></td>";
                                        echo "<td>".$apt['created_at']."</td>";
                                        
                                        // VULNERABILITY: No CSRF protection on delete
                                        echo "<td>
                                                <a href='view-appointment-vulnerable.php?id=".$apt['id']."' class='btn btn-sm btn-info'>View</a>
                                                <a href='?delete=".$apt['id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete?\")'>Delete</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center'>No appointments found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- VULNERABILITY: No authorization check before delete + SQL Injection -->
                <?php
                if(isset($_GET['delete'])) {
                    $delete_id = $_GET['delete'];
                    $delete_query = "DELETE FROM appointments WHERE id='".$delete_id."'";
                    if(mysqli_query($con, $delete_query)) {
                        echo "<div class='alert alert-success mt-3'>Appointment deleted!</div>";
                        echo "<meta http-equiv='refresh' content='2'>";
                    } else {
                        echo "<div class='alert alert-danger mt-3'>Error: ".mysqli_error($con)."</div>";
                    }
                }
                ?>

                <!-- Export functionality with vulnerability -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Export Appointments</h5>
                    </div>
                    <div class="card-body">
                        <!-- VULNERABILITY: No CSRF, IDOR possible -->
                        <a href="export-appointments.php?user_id=<?php echo $user_id; ?>&format=csv" class="btn btn-success">
                            <i class="material-icons" style="vertical-align: middle; font-size: 18px;">download</i>
                            Export as CSV
                        </a>
                        <small class="text-muted d-block mt-2">
                            Note: This export function has no authorization check
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
