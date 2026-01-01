<?php
// VULNERABLE APPOINTMENT BOOKING
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

$message = "";

// Handle appointment booking
if(isset($_POST['submit'])) {
    $doctorId = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $userId = $user['id'];
    
    // VULNERABILITY: SQL Injection - no prepared statements
    $insert = "INSERT INTO appointments (doctorId, userId, appointmentDate, appointmentTime, status) 
               VALUES ('".$doctorId."', '".$userId."', '".$date."', '".$time."', 'pending')";
    
    if(mysqli_query($con, $insert)) {
        // VULNERABILITY: XSS in success message
        $message = "<div class='alert alert-success'>Appointment booked successfully for ".$date." at ".$time."</div>";
    } else {
        // VULNERABILITY: Detailed error messages
        $message = "<div class='alert alert-danger'>Error: ".mysqli_error($con)."</div>";
    }
}

// Get doctors list
// VULNERABILITY: SQL Injection in doctors query
$specialization = isset($_GET['spec']) ? $_GET['spec'] : '';
if($specialization) {
    $doctors_query = "SELECT * FROM doctors WHERE specialization LIKE '%".$specialization."%'";
} else {
    $doctors_query = "SELECT * FROM doctors";
}
$doctors = mysqli_query($con, $doctors_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Appointment - Vulnerable HMS</title>
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
        .doctor-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .doctor-card:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        ⚠️ VULNERABLE VERSION - FOR TRAINING ONLY ⚠️
    </div>

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
                <h2>Book Appointment</h2>
                
                <?php echo $message; ?>

                <!-- Search Doctors -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Search Doctors by Specialization</h5>
                    </div>
                    <div class="card-body">
                        <!-- VULNERABILITY: No CSRF token -->
                        <form method="get" class="form-inline">
                            <input type="text" name="spec" class="form-control mr-2" 
                                   placeholder="e.g., Cardiology, Neurology"
                                   value="<?php echo isset($_GET['spec']) ? $_GET['spec'] : ''; ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="book-appointment-vulnerable.php" class="btn btn-secondary ml-2">Show All</a>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Try SQL Injection: <code>' OR '1'='1</code>
                        </small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h5>Available Doctors</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                if(mysqli_num_rows($doctors) > 0) {
                                    while($doctor = mysqli_fetch_array($doctors)) {
                                        echo "<div class='doctor-card'>";
                                        echo "<h5>".$doctor['doctorName']."</h5>";
                                        // VULNERABILITY: XSS - not encoding output
                                        echo "<p><strong>Specialization:</strong> ".$doctor['specialization']."</p>";
                                        echo "<p><strong>Consultation Fee:</strong> $".$doctor['docFees']."</p>";
                                        echo "<p><strong>Email:</strong> ".$doctor['docEmail']."</p>";
                                        // VULNERABILITY: Exposing doctor ID directly
                                        echo "<button class='btn btn-sm btn-primary' onclick='selectDoctor(".$doctor['id'].", \"".$doctor['doctorName']."\")'>Select</button>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<p>No doctors found.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h5>Appointment Details</h5>
                            </div>
                            <div class="card-body">
                                <!-- VULNERABILITY: No CSRF protection -->
                                <form method="post">
                                    <div class="form-group">
                                        <label>Selected Doctor:</label>
                                        <input type="text" class="form-control" id="doctorName" readonly>
                                        <input type="hidden" name="doctor" id="doctorId" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Appointment Date:</label>
                                        <!-- VULNERABILITY: No date validation -->
                                        <input type="date" name="date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Appointment Time:</label>
                                        <!-- VULNERABILITY: No time validation -->
                                        <input type="time" name="time" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Your Name:</label>
                                        <!-- VULNERABILITY: Exposing user data -->
                                        <input type="text" class="form-control" 
                                               value="<?php echo $user['fullname']; ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Your Email:</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo $user['email']; ?>" readonly>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-success btn-block">
                                        Book Appointment
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- VULNERABILITY: Information disclosure -->
                        <div class="card mt-3 border-warning">
                            <div class="card-header bg-warning">
                                <small>Debug: User ID = <?php echo $user['id']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <script>
        // VULNERABILITY: XSS in JavaScript
        function selectDoctor(id, name) {
            document.getElementById('doctorId').value = id;
            document.getElementById('doctorName').value = name;
            // User input not sanitized
        }

        // VULNERABILITY: Information disclosure in console
        console.log("User ID: <?php echo $user['id']; ?>");
        console.log("Email: <?php echo $user['email']; ?>");
    </script>
</body>
</html>
