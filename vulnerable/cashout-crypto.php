<?php
// CASHOUT & CRYPTO EXCHANGE - Multiple Vulnerabilities
// Simulated Bitcoin/Crypto exchange with withdrawal system
session_start();
include("config-vulnerable.php");
include("gamification.php");
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
$exploit_result = null;

// VULNERABILITY: Business Logic - Negative amount withdrawal
if(isset($_POST['withdraw_ngn'])) {
    $amount = $_POST['amount'];
    $bank_account = $_POST['bank_account'];
    
    // No validation on amount!
    if($amount > $user['balance']) {
        $message = "<div class='alert alert-danger'>Insufficient balance!</div>";
    } else {
        // VULNERABILITY: Can withdraw negative amounts to ADD money
        $new_balance = $user['balance'] - $amount;
        $update = "UPDATE users SET balance='".$new_balance."' WHERE id='".$user['id']."'";
        mysqli_query($con, $update);
        
        if($amount < 0) {
            $exploit_result = award_exploit($con, $user['id'], 'Business Logic - Negative Price');
        }
        
        $message = "<div class='alert alert-success'>Withdrawal of ₦".number_format($amount, 2)." processed to account: ".$bank_account."</div>";
        $user['balance'] = $new_balance;
    }
}

// VULNERABILITY: Rate manipulation via parameter tampering
$btc_rate = isset($_POST['btc_rate']) ? $_POST['btc_rate'] : 65000000; // ₦65M per BTC
$eth_rate = isset($_POST['eth_rate']) ? $_POST['eth_rate'] : 8500000;  // ₦8.5M per ETH

if(isset($_POST['buy_crypto'])) {
    $crypto_type = $_POST['crypto_type'];
    $ngn_amount = $_POST['ngn_amount'];
    $rate = $_POST['rate']; // VULNERABILITY: Rate from user input!
    
    if($ngn_amount > $user['balance']) {
        $message = "<div class='alert alert-danger'>Insufficient NGN balance!</div>";
    } else {
        // VULNERABILITY: Using user-supplied rate
        $crypto_amount = $ngn_amount / $rate;
        
        // Deduct NGN
        $new_balance = $user['balance'] - $ngn_amount;
        $update = "UPDATE users SET balance='".$new_balance."' WHERE id='".$user['id']."'";
        mysqli_query($con, $update);
        
        $message = "<div class='alert alert-success'>Purchased ".number_format($crypto_amount, 8)." ".$crypto_type." for ₦".number_format($ngn_amount, 2)."</div>";
        $user['balance'] = $new_balance;
    }
}

// VULNERABILITY: No validation on crypto address format
if(isset($_POST['send_crypto'])) {
    $crypto_address = $_POST['crypto_address'];
    $crypto_amount = $_POST['crypto_amount'];
    $crypto_type = $_POST['crypto_type_send'];
    
    // VULNERABILITY: XSS in crypto address display
    // VULNERABILITY: Command injection possible if address used in system command
    $message = "<div class='alert alert-info'>Sent ".$crypto_amount." ".$crypto_type." to address: ".$crypto_address."</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cashout & Crypto Exchange</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .warning-banner { background: #dc3545; color: white; padding: 10px; text-align: center; font-weight: bold; }
        .sidebar { background: #343a40; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; display: block; padding: 10px; text-decoration: none; margin-bottom: 5px; }
        .sidebar a:hover { background: #495057; }
        .content { padding: 30px; }
        .balance-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; }
        .crypto-card { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; }
        .rate-display { font-size: 24px; font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="warning-banner">WARNING: VULNERABLE VERSION - FOR TRAINING ONLY WARNING:</div>
    
    <?php echo display_exploit_notification($exploit_result); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 style="color: white;">Medview+ HMS</h4>
                <hr style="background: white;">
                <p style="color: white;">Welcome, <?php echo $user['fullname']; ?></p>
                
                <a href="dashboard-vulnerable.php">Dashboard</a>
                <a href="book-appointment-vulnerable.php">Book Appointment</a>
                <a href="mass-assignment-vulnerable.php">Make Payment</a>
                <a href="cashout-crypto.php" class="active">MONEY: Cashout & Crypto</a>
                <a href="profile-vulnerable.php">My Profile</a>
                <a href="user-login-sqli.php">Logout</a>
            </div>

            <div class="col-md-10 content">
                <h2>MONEY: Cashout & Cryptocurrency Exchange</h2>
                <?php echo $message; ?>

                <!-- Balance Display -->
                <div class="balance-card">
                    <h4>Your Balance</h4>
                    <h1 class="rate-display">₦<?php echo number_format($user['balance'], 2); ?> NGN</h1>
                    <p>Available for withdrawal or crypto purchase</p>
                </div>

                <div class="alert alert-warning">
                    <h5>TARGET: Vulnerabilities in This Page:</h5>
                    <ul>
                        <li><strong>Negative Withdrawal:</strong> Enter -500 in amount field</li>
                        <li><strong>Rate Manipulation:</strong> Modify rate parameter in crypto purchase</li>
                        <li><strong>No Address Validation:</strong> Any crypto address accepted</li>
                        <li><strong>XSS:</strong> Crypto address field not sanitized</li>
                        <li><strong>Integer Overflow:</strong> Large amounts cause issues</li>
                    </ul>
                </div>

                <div class="row">
                    <!-- Traditional Bank Withdrawal -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5>BANK: Bank Withdrawal (Traditional)</h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <div class="form-group">
                                        <label>Amount (NGN):</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Enter amount" required>
                                        <small class="text-muted">Try: -500 to exploit negative pricing</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Bank Account Number:</label>
                                        <input type="text" name="bank_account" class="form-control" placeholder="0123456789" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Bank Name:</label>
                                        <select name="bank_name" class="form-control">
                                            <option>Access Bank</option>
                                            <option>GTBank</option>
                                            <option>First Bank</option>
                                            <option>UBA</option>
                                            <option>Zenith Bank</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="withdraw_ngn" class="btn btn-primary btn-block">
                                        Withdraw to Bank
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Crypto Purchase -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h5>₿ Buy Cryptocurrency</h5>
                            </div>
                            <div class="card-body">
                                <div class="crypto-card">
                                    <div class="d-flex justify-content-between">
                                        <span>Bitcoin (BTC)</span>
                                        <span class="rate-display">₦<?php echo number_format($btc_rate); ?></span>
                                    </div>
                                </div>
                                <div class="crypto-card">
                                    <div class="d-flex justify-content-between">
                                        <span>Ethereum (ETH)</span>
                                        <span class="rate-display">₦<?php echo number_format($eth_rate); ?></span>
                                    </div>
                                </div>

                                <form method="post">
                                    <div class="form-group">
                                        <label>Cryptocurrency:</label>
                                        <select name="crypto_type" class="form-control" onchange="updateRate(this)">
                                            <option value="BTC">Bitcoin (BTC)</option>
                                            <option value="ETH">Ethereum (ETH)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount in NGN:</label>
                                        <input type="number" step="0.01" name="ngn_amount" class="form-control" placeholder="Amount to convert" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Exchange Rate:</label>
                                        <input type="number" name="rate" id="rate" class="form-control" value="<?php echo $btc_rate; ?>" required>
                                        <small class="text-danger">WARNING: Vulnerable: Change this to manipulate rate!</small>
                                    </div>
                                    <button type="submit" name="buy_crypto" class="btn btn-warning btn-block">
                                        Purchase Crypto
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Crypto Send -->
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5>SEND: Send Cryptocurrency</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="post">
                                    <div class="form-group">
                                        <label>Cryptocurrency:</label>
                                        <select name="crypto_type_send" class="form-control">
                                            <option value="BTC">Bitcoin (BTC)</option>
                                            <option value="ETH">Ethereum (ETH)</option>
                                            <option value="USDT">Tether (USDT)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Crypto Address:</label>
                                        <input type="text" name="crypto_address" class="form-control" placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa" required>
                                        <small class="text-danger">WARNING: No validation - XSS possible!</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Amount:</label>
                                        <input type="number" step="0.00000001" name="crypto_amount" class="form-control" placeholder="0.00000000" required>
                                    </div>
                                    <button type="submit" name="send_crypto" class="btn btn-success btn-block">
                                        Send Cryptocurrency
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6>TIP: Simulated Crypto Transfer</h6>
                                    <p>This simulates sending cryptocurrency to an external wallet address.</p>
                                    <p><strong>Vulnerabilities:</strong></p>
                                    <ul class="small">
                                        <li>No address format validation</li>
                                        <li>XSS via address display</li>
                                        <li>No balance check for crypto</li>
                                        <li>No transaction confirmation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exploit Guide -->
                <div class="card mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5>TARGET: Exploitation Examples</h5>
                    </div>
                    <div class="card-body">
                        <h6>1. Negative Withdrawal (Get Free Money):</h6>
                        <pre>Amount: -500
Bank Account: 0123456789
Result: ₦500 added to your balance instead of deducted!</pre>

                        <h6>2. Rate Manipulation (Buy Crypto Cheap):</h6>
                        <pre>Use browser DevTools to change rate input:
Original Rate: ₦65,000,000 per BTC
Modified Rate: ₦1 per BTC
Buy Amount: ₦1,000
Result: Get 1000 BTC instead of 0.000015 BTC!</pre>

                        <h6>3. XSS via Crypto Address:</h6>
                        <pre>Crypto Address: &lt;script&gt;alert('XSS')&lt;/script&gt;
Result: XSS executes when address is displayed</pre>

                        <h6>4. Integer Overflow:</h6>
                        <pre>Amount: 999999999999999
Result: Integer overflow causes calculation errors</pre>

                        <h6>FIX: How to Fix:</h6>
                        <pre>// Validate amount
if($amount <= 0) die("Amount must be positive");

// Use server-side rates only
$btc_rate = 65000000; // Never from user input!

// Validate crypto addresses
if(!preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $address)) {
    die("Invalid Bitcoin address");
}

// Check balances
if($amount > $balance) die("Insufficient funds");</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateRate(select) {
        const rates = {
            'BTC': <?php echo $btc_rate; ?>,
            'ETH': <?php echo $eth_rate; ?>
        };
        document.getElementById('rate').value = rates[select.value];
    }
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
