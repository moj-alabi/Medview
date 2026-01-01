<?php
// VULNERABLE PATIENT DASHBOARD
// WARNING: FOR TRAINING PURPOSES ONLY - DO NOT USE IN PRODUCTION

session_start();
include("config-vulnerable.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VULNERABILITY #1: No session validation
// Anyone can access by just setting session variable

// VULNERABILITY #2: SQL Injection in user data retrieval
if(isset($_SESSION['login'])) {
    $email = $_SESSION['login'];
    // Direct concatenation - SQL Injection vulnerability
    $query = "SELECT * FROM users WHERE email='".$email."'";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_array($result);
} else {
    header("location: user-login-sqli.php");
    exit();
}

// VULNERABILITY #3: IDOR - Can view any appointment by changing ID
if(isset($_GET['view_appointment'])) {
    $apt_id = $_GET['view_appointment'];
    // No authorization check - IDOR vulnerability
    $apt_query = "SELECT * FROM appointments WHERE id='".$apt_id."'";
    $apt_result = mysqli_query($con, $apt_query);
}

// VULNERABILITY #4: XSS in displaying user data
// VULNERABILITY #5: No CSRF protection on actions
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Dashboard - Vulnerable HMS</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .warning-banner {
            background: #dc3545;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        .sidebar {
            background: #343a40;
            min-height: 100vh;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 5px;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            padding: 30px;
        }
        .stat-card {
            border-left: 4px solid #007bff;
            padding: 20px;
            background: #f8f9fa;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        ⚠️ VULNERABLE VERSION - FOR TRAINING ONLY ⚠️
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 style="color: white;">Medview+ HMS</h4>
                <hr style="background: white;">
                
                <!-- VULNERABILITY: XSS in username display -->
                <p style="color: white;">Welcome, <?php echo $user['fullname']; ?></p>
                
                <a href="dashboard-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">dashboard</i> Dashboard</a>
                <a href="book-appointment-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">event</i> Book Appointment</a>
                <a href="appointment-history-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">history</i> Appointment History</a>
                <a href="medical-history-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">description</i> Medical History</a>
                <a href="profile-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">person</i> My Profile</a>
                <a href="user-login-sqli.php"><i class="material-icons" style="vertical-align: middle;">exit_to_app</i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <h2>Patient Dashboard</h2>
                <p class="text-muted">
                    Session ID: <?php echo session_id(); ?> 
                    <!-- VULNERABILITY: Session ID exposed -->
                </p>

                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h3>
                                <?php
                                // VULNERABILITY: SQL Injection in count query
                                $count_query = "SELECT COUNT(*) as total FROM appointments WHERE userId='".$user['id']."'";
                                $count_result = mysqli_query($con, $count_query);
                                $count = mysqli_fetch_array($count_result);
                                echo $count['total'];
                                ?>
                            </h3>
                            <p>Total Appointments</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card" style="border-color: #28a745;">
                            <h3>
                                <?php
                                $pending = "SELECT COUNT(*) as total FROM appointments WHERE userId='".$user['id']."' AND status='pending'";
                                $pending_result = mysqli_query($con, $pending);
                                $pending_count = mysqli_fetch_array($pending_result);
                                echo $pending_count['total'];
                                ?>
                            </h3>
                            <p>Pending Appointments</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card" style="border-color: #dc3545;">
                            <h3><?php echo $user['id']; ?></h3>
                            <p>Patient ID</p>
                            <!-- VULNERABILITY: Exposing internal IDs -->
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Recent Appointments</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // VULNERABILITY: SQL Injection
                                $apt_query = "SELECT a.*, d.doctorName FROM appointments a 
                                             JOIN doctors d ON a.doctorId=d.id 
                                             WHERE a.userId='".$user['id']."' 
                                             ORDER BY a.created_at DESC LIMIT 5";
                                $apt_result = mysqli_query($con, $apt_query);
                                
                                while($apt = mysqli_fetch_array($apt_result)) {
                                    echo "<tr>";
                                    echo "<td>".$apt['id']."</td>";
                                    // VULNERABILITY: XSS - Not encoding output
                                    echo "<td>".$apt['doctorName']."</td>";
                                    echo "<td>".$apt['appointmentDate']."</td>";
                                    echo "<td>".$apt['appointmentTime']."</td>";
                                    echo "<td><span class='badge badge-info'>".$apt['status']."</span></td>";
                                    // VULNERABILITY: IDOR - Direct object reference without authorization
                                    echo "<td>
                                            <a href='view-appointment-vulnerable.php?id=".$apt['id']."' class='btn btn-sm btn-primary'>View</a>
                                            <a href='?cancel=".$apt['id']."' class='btn btn-sm btn-danger'>Cancel</a>
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- VULNERABILITY: No CSRF token on cancel action -->
                <?php
                if(isset($_GET['cancel'])) {
                    $cancel_id = $_GET['cancel'];
                    // VULNERABILITY: No authorization check + SQL Injection
                    $cancel_query = "DELETE FROM appointments WHERE id='".$cancel_id."'";
                    if(mysqli_query($con, $cancel_query)) {
                        echo "<div class='alert alert-success mt-3'>Appointment cancelled!</div>";
                        echo "<meta http-equiv='refresh' content='2'>";
                    }
                }
                ?>

                <!-- Debug Info - VULNERABILITY: Information Disclosure -->
                <div class="card mt-4" style="border-color: #ffc107;">
                    <div class="card-header bg-warning">
                        <h5>🐛 Debug Information (Information Disclosure Vulnerability)</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php
                        echo "User Data:\n";
                        print_r($user);
                        echo "\n\nSession Data:\n";
                        print_r($_SESSION);
                        echo "\n\nServer Info:\n";
                        echo "PHP Version: ".phpversion()."\n";
                        echo "Server: ".$_SERVER['SERVER_SOFTWARE']."\n";
                        echo "Document Root: ".$_SERVER['DOCUMENT_ROOT']."\n";
                        ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <!-- VULNERABILITY: JavaScript XSS -->
    <script>
        var username = "<?php echo $user['fullname']; ?>";
        console.log("Logged in as: " + username);
        // User input not escaped in JavaScript context
    </script>
</body>
</html>
