<?php
// VULNERABLE MEDICAL HISTORY
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
$query = "SELECT * FROM users WHERE email='".$email."'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_array($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medical History - Vulnerable HMS</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .warning-banner { background: #dc3545; color: white; padding: 10px; text-align: center; font-weight: bold; }
        .sidebar { background: #343a40; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; display: block; padding: 10px; text-decoration: none; margin-bottom: 5px; }
        .sidebar a:hover { background: #495057; }
        .content { padding: 30px; }
    </style>
</head>
<body>
    <div class="warning-banner">⚠️ VULNERABLE VERSION - FOR TRAINING ONLY ⚠️</div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 style="color: white;">Medview+ HMS</h4>
                <hr style="background: white;">
                <p style="color: white;">Welcome, <?php echo $user['fullname']; ?></p>
                
                <a href="dashboard-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">dashboard</i> Dashboard</a>
                <a href="book-appointment-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">event</i> Book Appointment</a>
                <a href="appointment-history-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">history</i> Appointment History</a>
                <a href="medical-history-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">description</i> Medical History</a>
                <a href="profile-vulnerable.php"><i class="material-icons" style="vertical-align: middle;">person</i> My Profile</a>
                <a href="user-login-sqli.php"><i class="material-icons" style="vertical-align: middle;">exit_to_app</i> Logout</a>
            </div>

            <div class="col-md-10 content">
                <h2>Medical History</h2>
                <p class="text-muted">Patient ID: <?php echo $user['id']; ?></p>

                <div class="alert alert-info">
                    <strong>Note:</strong> This is a simplified version. In a complete vulnerable HMS, 
                    this would include more features with IDOR, SQL Injection, and other vulnerabilities.
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Patient Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th width="200">Name:</th>
                                <td><?php echo $user['fullname']; ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo $user['email']; ?></td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td><?php echo $user['gender']; ?></td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td><?php echo $user['address']; ?></td>
                            </tr>
                            <tr>
                                <th>City:</th>
                                <td><?php echo $user['city']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Recent Appointments & Treatments</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // VULNERABILITY: SQL Injection
                        $med_query = "SELECT a.*, d.doctorName FROM appointments a 
                                     JOIN doctors d ON a.doctorId=d.id 
                                     WHERE a.userId='".$user['id']."' AND a.status='approved'
                                     ORDER BY a.appointmentDate DESC LIMIT 10";
                        $medical_records = mysqli_query($con, $med_query);
                        
                        if(mysqli_num_rows($medical_records) > 0) {
                            echo "<table class='table table-striped'>";
                            echo "<thead><tr><th>Date</th><th>Doctor</th><th>Time</th><th>Status</th></tr></thead>";
                            echo "<tbody>";
                            while($record = mysqli_fetch_array($medical_records)) {
                                echo "<tr>";
                                echo "<td>".$record['appointmentDate']."</td>";
                                echo "<td>".$record['doctorName']."</td>";
                                echo "<td>".$record['appointmentTime']."</td>";
                                echo "<td><span class='badge badge-success'>".$record['status']."</span></td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<p class='text-muted'>No medical history found.</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5>Additional Features (In Full Version)</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Prescriptions & Medications</li>
                            <li>Lab Results</li>
                            <li>Vaccination Records</li>
                            <li>Allergy Information</li>
                            <li>Diagnosis History</li>
                        </ul>
                        <p class="text-muted">These features would all include similar vulnerabilities (SQL Injection, XSS, IDOR, etc.)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
