<?php
// BUSINESS LOGIC FLAWS + RACE CONDITION VULNERABILITIES
// Hidden Hint: Multiple logic flaws and race conditions in payment/booking
session_start();
include("config-vulnerable.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['login'])) die("Login required");

$user_query = "SELECT * FROM users WHERE email='".$_SESSION['login']."'";
$user = mysqli_fetch_array(mysqli_query($con, $user_query));
$message = "";

// VULNERABILITY #1: Negative Pricing
// Hidden Hint: Can enter negative amounts to receive money
if(isset($_POST['process_refund'])) {
    $amount = $_POST['amount']; // No validation!
    $balance = $_POST['balance'];
    // BUG: Negative amounts add money instead of deducting
    $new_balance = $balance + $amount; // Should validate $amount > 0
    $message = "Refund processed! New balance: $".$new_balance;
}

// VULNERABILITY #2: Race Condition in Booking
// Hidden Hint: Multiple concurrent requests can book same slot
if(isset($_POST['book_slot'])) {
    $slot_id = $_POST['slot_id'];
    
    // Check availability
    $check = "SELECT * FROM appointment_slots WHERE id='".$slot_id."' AND available=1";
    $result = mysqli_query($con, $check);
    
    if(mysqli_num_rows($result) > 0) {
        // RACE CONDITION: Time gap between check and update
        // Hidden Hint: Send multiple requests simultaneously to book same slot
        sleep(2); // Artificial delay makes race condition easier to exploit
        
        // Update slot as booked
        $update = "UPDATE appointment_slots SET available=0, booked_by='".$user['id']."' WHERE id='".$slot_id."'";
        mysqli_query($con, $update);
        $message = "Slot booked successfully!";
    }
}

// VULNERABILITY #3: Integer Overflow
// Hidden Hint: Large numbers cause integer overflow
if(isset($_POST['calculate'])) {
    $quantity = intval($_POST['quantity']);
    $price = intval($_POST['price']);
    $total = $quantity * $price; // Can overflow with large numbers
    $message = "Total: $".$total;
}

// VULNERABILITY #4: Time Manipulation
// Hidden Hint: Can set appointment dates in past or manipulate timestamps
if(isset($_POST['book_past'])) {
    $date = $_POST['date']; // No date validation!
    // Can book appointments in the past
    $insert = "INSERT INTO appointments (userId, appointmentDate, appointmentTime) 
               VALUES ('".$user['id']."', '".$date."', '00:00:00')";
    mysqli_query($con, $insert);
}

// VULNERABILITY #5: Bypassing Limits
// Hidden Hint: No check on max appointments per user
if(isset($_POST['book_many'])) {
    // Should limit to 5 appointments, but doesn't check
    for($i=0; $i<100; $i++) { // Can book unlimited
        $insert = "INSERT INTO appointments (userId, appointmentDate) VALUES ('".$user['id']."', NOW())";
        mysqli_query($con, $insert);
    }
}

// VULNERABILITY #6: Insufficient Anti-automation
// Hidden Hint: Can script 1000s of bookings without rate limiting
?>
<!DOCTYPE html>
<html>
<head>
    <title>Business Logic & Race Conditions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Payment & Booking System</h2>
        <?php if($message) echo "<div class='alert alert-info'>$message</div>"; ?>
        
        <div class="alert alert-danger">
            <h5>TARGET: Vulnerabilities: Business Logic + Race Conditions</h5>
            <p><strong>Exploit Methods:</strong></p>
            <ul>
                <li><strong>Negative Pricing:</strong> Enter -100 in refund to add money</li>
                <li><strong>Race Condition:</strong> Send multiple concurrent booking requests for same slot</li>
                <li><strong>Integer Overflow:</strong> quantity=2147483647, price=2</li>
                <li><strong>Past Dates:</strong> Book appointments with date=2020-01-01</li>
                <li><strong>Bypass Limits:</strong> Book unlimited appointments</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Refund (Negative Pricing Bug)</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="number" name="amount" placeholder="Amount" class="form-control mb-2">
                            <input type="hidden" name="balance" value="100">
                            <button name="process_refund" class="btn btn-primary">Process Refund</button>
                            <small class="d-block mt-2 text-muted">Try: -1000</small>
                        </form>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Book Slot (Race Condition)</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="number" name="slot_id" value="1" class="form-control mb-2">
                            <button name="book_slot" class="btn btn-warning">Book Slot</button>
                            <small class="d-block mt-2 text-muted">
                                Race condition: Open 2 browser tabs, click simultaneously
                            </small>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">TIP: Exploitation Guide</div>
                    <div class="card-body small">
                        <p><strong>Race Condition Attack:</strong></p>
                        <pre>for i in {1..10}; do
  curl -X POST localhost/vulnerable/business-logic-race.php \
  -d "book_slot=1&slot_id=1" &
done</pre>

                        <p><strong>Business Logic Examples:</strong></p>
                        <ul>
                            <li>Coupon reuse (use same code multiple times)</li>
                            <li>Quantity manipulation (order -5 items)</li>
                            <li>Price tampering (change hidden price field)</li>
                            <li>Workflow bypass (skip payment step)</li>
                        </ul>

                        <p><strong>Real Attacks:</strong></p>
                        <ul>
                            <li>Instagram: Follow user multiple times via race condition</li>
                            <li>PayPal: Negative transfer amounts</li>
                            <li>E-commerce: Race condition in limited stock items</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
