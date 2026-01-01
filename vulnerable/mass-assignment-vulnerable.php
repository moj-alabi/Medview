<?php
// MASS ASSIGNMENT VULNERABILITY - Integrated in Payment System
// WARNING: FOR TRAINING PURPOSES ONLY
// Hidden Hint: POST parameters can override any object property

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

// VULNERABILITY: Mass Assignment
// Hidden Hint: All POST parameters are assigned to variables without filtering
if(isset($_POST['pay'])) {
    // Extract ALL POST variables into current scope
    // Hidden Hint: Attacker can add extra parameters like is_admin, role, price_override
    extract($_POST); // EXTREMELY DANGEROUS!
    
    // Normal intended flow
    $appointment_id = isset($appointment_id) ? $appointment_id : 0;
    $payment_amount = isset($payment_amount) ? $payment_amount : 100;
    
    // VULNERABILITY: User can override these by adding POST parameters
    // Hidden Hint: Send discount=100 to make price $0
    $discount = isset($discount) ? $discount : 0;
    $is_premium = isset($is_premium) ? $is_premium : 0;
    $admin_approved = isset($admin_approved) ? $admin_approved : 0;
    
    // Calculate final price
    $final_price = $payment_amount - $discount;
    
    // VULNERABILITY: Negative prices possible
    // Hidden Hint: Send payment_amount=-100 to receive money
    if($final_price < 0) {
        $message = "<div class='alert alert-warning'>Negative price detected: $".$final_price."</div>";
    }
    
    // VULNERABILITY: Can set admin_approved without authorization
    // Hidden Hint: POST admin_approved=1 to bypass approval
    $status = $admin_approved ? 'approved' : 'pending';
    
    // Insert payment record with vulnerable mass-assigned values
    $insert = "INSERT INTO payments (user_id, appointment_id, amount, discount, is_premium, status) 
               VALUES ('".$user['id']."', '".$appointment_id."', '".$payment_amount."', '".$discount."', '".$is_premium."', '".$status."')";
    
    if(mysqli_query($con, $insert)) {
        $message = "<div class='alert alert-success'>Payment processed! Amount: $".$final_price." | Status: ".$status."</div>";
    }
}

// Get appointments needing payment
$apt_query = "SELECT a.*, d.doctorName, d.docFees FROM appointments a 
              JOIN doctors d ON a.doctorId=d.id 
              WHERE a.userId='".$user['id']."' AND a.status='pending'";
$appointments = mysqli_query($con, $apt_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment - Mass Assignment Vulnerability</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
                
                <a href="dashboard-vulnerable.php">Dashboard</a>
                <a href="book-appointment-vulnerable.php">Book Appointment</a>
                <a href="appointment-history-vulnerable.php">Appointments</a>
                <a href="mass-assignment-vulnerable.php">Make Payment</a>
                <a href="profile-vulnerable.php">My Profile</a>
                <a href="user-login-sqli.php">Logout</a>
            </div>

            <div class="col-md-10 content">
                <h2>Appointment Payment</h2>
                <?php echo $message; ?>

                <div class="alert alert-danger">
                    <h5>🎯 Vulnerability: Mass Assignment / Parameter Pollution</h5>
                    <p><strong>How to Exploit:</strong></p>
                    <ul>
                        <li>Add <code>discount=1000</code> to POST request</li>
                        <li>Add <code>payment_amount=-100</code> for negative price</li>
                        <li>Add <code>admin_approved=1</code> to auto-approve</li>
                        <li>Add <code>is_premium=1</code> for premium status</li>
                    </ul>
                    <p><small>Use browser DevTools or Burp Suite to modify POST data</small></p>
                </div>

                <div class="row">
                    <?php while($apt = mysqli_fetch_array($appointments)): ?>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5>Appointment #<?php echo $apt['id']; ?></h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Doctor:</strong> <?php echo $apt['doctorName']; ?></p>
                                <p><strong>Date:</strong> <?php echo $apt['appointmentDate']; ?></p>
                                <p><strong>Time:</strong> <?php echo $apt['appointmentTime']; ?></p>
                                <p><strong>Fee:</strong> $<?php echo $apt['docFees']; ?></p>

                                <!-- VULNERABILITY: Form with no CSRF token -->
                                <form method="post">
                                    <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                    <input type="hidden" name="payment_amount" value="<?php echo $apt['docFees']; ?>">
                                    
                                    <div class="form-group">
                                        <label>Payment Amount:</label>
                                        <input type="number" class="form-control" name="payment_amount_display" 
                                               value="<?php echo $apt['docFees']; ?>" readonly>
                                    </div>

                                    <button type="submit" name="pay" class="btn btn-success btn-block">
                                        Pay $<?php echo $apt['docFees']; ?>
                                    </button>
                                    
                                    <small class="text-muted d-block mt-2">
                                        Hidden Hint: Check POST parameters with browser DevTools
                                    </small>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5>💡 Mass Assignment Explained</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>What is Mass Assignment?</strong></p>
                        <p>When an application automatically binds HTTP parameters to program variables/objects without filtering, attackers can modify unintended fields.</p>

                        <h6>Exploitation with curl:</h6>
                        <pre>curl -X POST http://localhost/Medview/vulnerable/mass-assignment-vulnerable.php \
  -d "pay=1" \
  -d "appointment_id=1" \
  -d "payment_amount=-100" \
  -d "discount=1000" \
  -d "admin_approved=1" \
  -d "is_premium=1"</pre>

                        <h6>Real-World Examples:</h6>
                        <ul>
                            <li><strong>GitHub (2012):</strong> Mass assignment to gain repo access</li>
                            <li><strong>Ruby on Rails:</strong> Common issue with attr_accessible</li>
                            <li><strong>E-commerce:</strong> Modify prices, discounts, quantities</li>
                        </ul>

                        <h6>🛡️ How to Fix:</h6>
                        <pre>// Instead of: extract($_POST);
// Use whitelist approach:
$allowed = ['appointment_id', 'payment_amount'];
foreach($_POST as $key => $value) {
    if(in_array($key, $allowed)) {
        $$key = $value;
    }
}

// Or use explicit assignment:
$appointment_id = $_POST['appointment_id'];
$payment_amount = $_POST['payment_amount'];
// Never assign admin_approved, is_premium from user input</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
