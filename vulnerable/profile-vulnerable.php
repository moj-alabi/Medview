<?php
// VULNERABLE PROFILE MANAGEMENT
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

$message = "";

// Handle profile update
if(isset($_POST['update'])) {
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    
    // VULNERABILITY: SQL Injection - no prepared statements
    $update_query = "UPDATE users SET 
                     fullname='".$fullname."', 
                     address='".$address."', 
                     city='".$city."', 
                     gender='".$gender."' 
                     WHERE id='".$user['id']."'";
    
    if(mysqli_query($con, $update_query)) {
        // VULNERABILITY: XSS in success message
        $message = "<div class='alert alert-success'>Profile updated! Welcome ".$fullname."</div>";
        // Refresh user data
        $result = mysqli_query($con, $query);
        $user = mysqli_fetch_array($result);
    } else {
        $message = "<div class='alert alert-danger'>Error: ".mysqli_error($con)."</div>";
    }
}

// Handle password change
if(isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    
    // VULNERABILITY: Storing password in MD5 (weak hashing)
    $hashed = md5($new_password);
    $pwd_query = "UPDATE users SET password='".$hashed."' WHERE id='".$user['id']."'";
    
    if(mysqli_query($con, $pwd_query)) {
        $message = "<div class='alert alert-success'>Password changed successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile - Vulnerable HMS</title>
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
                <h2>My Profile</h2>
                <?php echo $message; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- VULNERABILITY: No CSRF protection -->
                                <form method="post">
                                    <div class="form-group">
                                        <label>Full Name:</label>
                                        <!-- VULNERABILITY: XSS - displaying user input without encoding -->
                                        <input type="text" name="fullname" class="form-control" 
                                               value="<?php echo $user['fullname']; ?>" required>
                                        <small class="text-muted">Try XSS: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code></small>
                                    </div>

                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input type="email" class="form-control" 
                                               value="<?php echo $user['email']; ?>" readonly>
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Address:</label>
                                        <textarea name="address" class="form-control" rows="3"><?php echo $user['address']; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>City:</label>
                                        <input type="text" name="city" class="form-control" 
                                               value="<?php echo $user['city']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Gender:</label>
                                        <select name="gender" class="form-control">
                                            <option value="Male" <?php if($user['gender']=='Male') echo 'selected'; ?>>Male</option>
                                            <option value="Female" <?php if($user['gender']=='Female') echo 'selected'; ?>>Female</option>
                                            <option value="Other" <?php if($user['gender']=='Other') echo 'selected'; ?>>Other</option>
                                        </select>
                                    </div>

                                    <button type="submit" name="update" class="btn btn-primary btn-block">
                                        Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h5>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <!-- VULNERABILITY: No CSRF, no current password verification -->
                                <form method="post">
                                    <div class="alert alert-warning">
                                        <strong>Vulnerability:</strong> No current password verification required!
                                    </div>

                                    <div class="form-group">
                                        <label>New Password:</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                        <small class="text-muted">Will be stored as MD5 (weak!)</small>
                                    </div>

                                    <button type="submit" name="change_password" class="btn btn-warning btn-block">
                                        Change Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Profile picture upload with vulnerability -->
                        <div class="card mt-3">
                            <div class="card-header bg-danger text-white">
                                <h5>Profile Picture</h5>
                            </div>
                            <div class="card-body">
                                <p>Upload your profile picture:</p>
                                <a href="file-upload.php" class="btn btn-danger">
                                    Upload Picture (Vulnerable)
                                </a>
                                <small class="d-block mt-2 text-muted">
                                    File upload has no validation!
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug information -->
                <div class="card mt-4 border-warning">
                    <div class="card-header bg-warning">
                        <h5>🐛 Debug Info (Information Disclosure)</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php
                        echo "User ID: ".$user['id']."\n";
                        echo "Email: ".$user['email']."\n";
                        echo "Session ID: ".session_id()."\n";
                        echo "Password Hash: ".$user['password']." (MD5)\n";
                        echo "IP Address: ".$_SERVER['REMOTE_ADDR']."\n";
                        ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
