<?php
// VULNERABLE USER LOGIN - SQL INJECTION VULNERABILITY
// WARNING: FOR TRAINING PURPOSES ONLY - DO NOT USE IN PRODUCTION

session_start();
include("config-vulnerable.php");
include("gamification.php");
include("difficulty-manager.php");
include("cloudflare-config.php");

// Vulnerable: Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$exploit_result = null;

// Handle difficulty change
if(isset($_POST['set_difficulty'])) {
    $_SESSION['temp_difficulty'] = $_POST['difficulty_level'];
}

$temp_difficulty = isset($_SESSION['temp_difficulty']) ? $_SESSION['temp_difficulty'] : 'low';

if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Apply difficulty-based protection
    $username_safe = DifficultyManager::sanitizeSQL($username, $con, $temp_difficulty);
    $password_safe = DifficultyManager::sanitizeSQL($password, $con, $temp_difficulty);
    
    // Check Cloudflare WAF first
    $cf_validation = CloudflareWAF::validateRequest($temp_difficulty);
    if(!$cf_validation['allowed']) {
        $_SESSION['errmsg'] = "🛡️ Cloudflare WAF: " . $cf_validation['reason'];
        
        // Log Cloudflare block
        CloudflareWAF::logEvent($con, 0, 'waf_block', [
            'username' => $username,
            'difficulty' => $temp_difficulty,
            'threat_score' => $cf_validation['threat_score'] ?? 0
        ]);
        
        header("location: user-login-sqli.php");
        exit();
    }
    
    // Check simulated WAF (fallback for local testing)
    if(!CloudflareWAF::isCloudflareRequest()) {
        $waf_check = DifficultyManager::detectSQLInjection($username, $temp_difficulty);
        if($waf_check === "WAF_BLOCKED") {
            $_SESSION['errmsg'] = "Suspicious activity detected. Request blocked by simulated WAF.";
            header("location: user-login-sqli.php");
            exit();
        }
    }
    
    // VULNERABILITY #1: SQL INJECTION (difficulty-dependent)
    // Beginner: Direct concatenation | Intermediate: Basic escaping | Advanced: Prepared statements
    $query = "SELECT * FROM users WHERE email='".$username_safe."' AND password='".$password_safe."'";
    
    // Display query for educational purposes
    echo "<!-- DEBUG: SQL Query: ".$query." -->";
    
    $ret = mysqli_query($con, $query);
    
    // VULNERABILITY #2: Detailed error messages
    if(!$ret) {
        die("SQL Error: " . mysqli_error($con) . "<br>Query: " . $query);
    }
    
    $num = mysqli_fetch_array($ret);
    
    if($num > 0) {
        // VULNERABILITY #3: Weak session management
        $_SESSION['login'] = $username;
        $_SESSION['id'] = $num['id'];
        
        // DETECT SQL INJECTION EXPLOIT
        // Check if SQL injection syntax was used
        if(preg_match("/'|--|#|\/\*|\*\/|union|select|or\s+1\s*=\s*1|or\s+'1'\s*=\s*'1'/i", $username) || 
           preg_match("/'|--|#|\/\*|\*\/|union|select|or\s+1\s*=\s*1|or\s+'1'\s*=\s*'1'/i", $_POST['password'])) {
            // SQL Injection detected! Award the exploit
            $exploit_result = award_exploit($con, $num['id'], 'SQL Injection - Login Bypass');
        }
        
        // VULNERABILITY #4: No session regeneration
        // Redirect to dashboard with exploit notification
        if($exploit_result && $exploit_result['first_time']) {
            $_SESSION['exploit_notification'] = $exploit_result;
        }
        header("location: dashboard-vulnerable.php");
        exit();
    } else {
        $_SESSION['errmsg'] = DifficultyManager::getErrorMessage($temp_difficulty, 'login');
        
        // VULNERABILITY #6: Information disclosure (only in low difficulty mode)
        if($temp_difficulty === 'low') {
            echo "<!-- DEBUG: Login failed for user: ".$username." -->";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vulnerable User Login - SQL Injection Training</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .warning-banner {
            background: #ff0000;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .vulnerability-info {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        ⚠️ VULNERABLE VERSION - FOR SECURITY TRAINING ONLY ⚠️
    </div>
    
    <div class="container">
        <?php echo display_exploit_notification($exploit_result); ?>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="vulnerability-info">
                    <h5>🎯 Training Target: SQL Injection</h5>
                    <p><strong>Vulnerabilities Present:</strong></p>
                    <ul>
                        <li>SQL Injection in username field</li>
                        <li>SQL Injection in password field</li>
                        <li>No input sanitization</li>
                        <li>Detailed error messages</li>
                        <li>No CAPTCHA protection</li>
                        <li>Weak session management</li>
                    </ul>
                    <p><strong>Try:</strong> <code>admin' OR '1'='1</code> in username field</p>
                </div>

                <!-- Difficulty Selector -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5>🎯 Select Difficulty Level</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php 
                            $levels = DifficultyManager::getDifficultyLevels();
                            $current = $temp_difficulty;
                            ?>
                            <select name="difficulty_level" class="form-control mb-2" onchange="this.form.submit()">
                                <?php foreach($levels as $key => $level): ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($current === $key) ? 'selected' : ''; ?>>
                                        <?php echo $level['icon']; ?> <?php echo $level['name']; ?> - <?php echo $level['description']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="set_difficulty" value="1">
                        </form>
                        <?php
                        $config = DifficultyManager::getDifficultyConfig($current);
                        $color = $config['color'];
                        ?>
                        <div class="alert alert-<?php echo $color; ?> mt-2 mb-0">
                            <strong><?php echo $config['icon']; ?> Current: <?php echo $config['name']; ?></strong><br>
                            <small>
                                SQL Protection: <?php echo $config['sql_protection'] === false ? 'None' : ucfirst($config['sql_protection']); ?><br>
                                XSS Protection: <?php echo $config['xss_protection'] === false ? 'None' : ucfirst($config['xss_protection']); ?><br>
                                WAF: <?php echo $config['waf'] === false ? 'Disabled' : ucfirst($config['waf']); ?><br>
                                Hints: <?php echo $config['hints_visible'] ? 'Visible' : 'Hidden'; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Vulnerable Patient Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <?php if(isset($_SESSION['errmsg'])): ?>
                                <div class="alert alert-danger">
                                    <?php 
                                    // VULNERABILITY #7: XSS - Not escaping output
                                    echo $_SESSION['errmsg']; 
                                    $_SESSION['errmsg'] = "";
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label>Username/Email:</label>
                                <input type="text" class="form-control" name="username" 
                                       placeholder="Enter username" required>
                                <?php if(DifficultyManager::showHints($temp_difficulty)): ?>
                                <small class="form-text text-muted">
                                    Test SQL Injection here: try <code>admin' OR '1'='1</code>
                                </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" class="form-control" name="password" 
                                       placeholder="Enter password" required>
                                <?php if(DifficultyManager::showHints($temp_difficulty)): ?>
                                <small class="form-text text-muted">
                                    Any value works with SQL injection bypass
                                </small>
                                <?php endif; ?>
                            </div>
                            
                            <!-- VULNERABILITY #8: No CSRF protection -->
                            <!-- VULNERABILITY #9: No CAPTCHA -->
                            <!-- VULNERABILITY #10: No rate limiting -->
                            
                            <button type="submit" name="submit" class="btn btn-primary btn-block">
                                Login (Vulnerable)
                            </button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <a href="../index.html">← Back to Secure Version</a>
                        </div>
                    </div>
                </div>
                
                <?php if(DifficultyManager::showHints($temp_difficulty)): ?>
                <?php echo CloudflareWAF::displayDebugPanel($temp_difficulty); ?>
                <?php endif; ?>
                
                <div class="vulnerability-info mt-3">
                    <h6>💡 Learning Points:</h6>
                    <ol>
                        <li>SQL queries use direct string concatenation</li>
                        <li>User input is not sanitized or escaped</li>
                        <li>No prepared statements or parameterized queries</li>
                        <li>Error messages reveal database structure</li>
                        <li>Authentication can be completely bypassed</li>
                    </ol>
                    <p><strong>How to exploit:</strong></p>
                    <pre>Username: admin' OR '1'='1' -- 
Password: anything</pre>
                    <p>This will make the query: <code>SELECT * FROM users WHERE email='admin' OR '1'='1' -- ' AND password='anything'</code></p>
                    <p>The <code>--</code> comments out the rest, and <code>'1'='1'</code> is always true!</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
