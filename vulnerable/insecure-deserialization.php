<?php
// VULNERABLE SERIALIZATION - INSECURE DESERIALIZATION
// WARNING: FOR TRAINING PURPOSES ONLY
// Hidden Hint: Unserialize user input can lead to Remote Code Execution

session_start();
include("config-vulnerable.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VULNERABILITY: Insecure Deserialization
// Hidden Hint: PHP unserialize() on untrusted data is dangerous

class UserPreferences {
    public $theme = "light";
    public $language = "en";
    public $notifications = true;
    
    // VULNERABILITY: Magic method allows code execution
    // Hidden Hint: __destruct can be exploited for RCE
    public function __destruct() {
        // Hidden Hint: If attacker controls $theme, can inject commands
        if(isset($this->theme) && strpos($this->theme, '<?php') !== false) {
            eval($this->theme); // Extremely dangerous!
        }
    }
}

class Logger {
    public $logfile = "app.log";
    
    // VULNERABILITY: File operations in destructor
    // Hidden Hint: Can be exploited to write arbitrary files
    public function __destruct() {
        if(isset($this->logfile)) {
            // Hidden Hint: Attacker can control filename and content
            file_put_contents($this->logfile, "Log entry\n", FILE_APPEND);
        }
    }
}

$message = "";
$prefs = null;

if(isset($_POST['save_prefs'])) {
    // VULNERABILITY: Unserialize user input without validation
    // Hidden Hint: Attacker can inject malicious serialized objects
    $serialized = $_POST['preferences'];
    $prefs = unserialize($serialized); // DANGEROUS!
    
    $message = "<div class='alert alert-success'>Preferences saved!</div>";
}

if(isset($_POST['load_prefs'])) {
    $prefs = new UserPreferences();
    $prefs->theme = $_POST['theme'];
    $prefs->language = $_POST['language'];
    $serialized = serialize($prefs);
}

// VULNERABILITY: JWT with weak secret
// Hidden Hint: JWT secret is hardcoded and weak
function createToken($data) {
    $secret = "secret123"; // Hidden Hint: Weak secret, easy to brute force
    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64_encode(json_encode($data));
    $signature = hash_hmac('sha256', "$header.$payload", $secret);
    return "$header.$payload.$signature";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Preferences - Insecure Deserialization</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .warning-banner { background: #ff0000; color: white; padding: 15px; text-align: center; font-weight: bold; margin-bottom: 20px; }
        .code-box { background: #f8f9fa; padding: 15px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="warning-banner">WARNING: VULNERABLE VERSION - FOR SECURITY TRAINING ONLY WARNING:</div>
    
    <div class="container">
        <h2>User Preferences Manager</h2>
        <?php echo $message; ?>

        <div class="alert alert-danger">
            <h5>TARGET: Training Target: Insecure Deserialization & Software Integrity</h5>
            <p><strong>Vulnerabilities Present:</strong></p>
            <ul>
                <li>PHP unserialize() on user input</li>
                <li>Magic methods (__destruct, __wakeup) exploitable</li>
                <li>No input validation on serialized data</li>
                <li>Weak JWT secrets</li>
                <li>No integrity checks on data</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header"><h5>Set Preferences</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label>Theme:</label>
                                <select name="theme" class="form-control">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Language:</label>
                                <select name="language" class="form-control">
                                    <option value="en">English</option>
                                    <option value="ar">Arabic</option>
                                </select>
                            </div>
                            <button type="submit" name="load_prefs" class="btn btn-primary">Generate Serialized Data</button>
                        </form>

                        <?php if(isset($serialized)): ?>
                        <div class="mt-3">
                            <label>Serialized Data:</label>
                            <div class="code-box" style="word-break: break-all;">
                                <?php echo htmlspecialchars($serialized); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-danger text-white"><h5>Load Preferences (Vulnerable)</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label>Serialized Preferences:</label>
                                <textarea name="preferences" class="form-control" rows="4" 
                                          placeholder="Paste serialized data here"></textarea>
                            </div>
                            <button type="submit" name="save_prefs" class="btn btn-danger">
                                Load (Unserialize)
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="alert alert-warning">
                    <strong>Exploitation Examples:</strong>
                    
                    <p><strong>1. Basic Object Injection:</strong></p>
                    <div class="code-box small">
O:15:"UserPreferences":3:{s:5:"theme";s:5:"light";s:8:"language";s:2:"en";s:13:"notifications";b:1;}
                    </div>

                    <p><strong>2. File Write via Logger:</strong></p>
                    <div class="code-box small">
O:6:"Logger":1:{s:7:"logfile";s:10:"shell.php";}
                    </div>

                    <p><strong>3. RCE via __destruct:</strong></p>
                    <div class="code-box small">
O:15:"UserPreferences":1:{s:5:"theme";s:18:"&lt;?php system($_GET['cmd']); ?&gt;";}
                    </div>

                    <p class="small"><strong>Note:</strong> Actual exploitation requires specific PHP magic methods and classes in the application.</p>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-info text-white"><h5>TIP: Understanding the Risk</h5></div>
                    <div class="card-body small">
                        <p><strong>What is Insecure Deserialization?</strong></p>
                        <p>Deserialization converts serialized data back into objects. If untrusted data is deserialized, attackers can:</p>
                        <ul>
                            <li>Execute arbitrary code</li>
                            <li>Inject malicious objects</li>
                            <li>Modify application logic</li>
                            <li>Achieve Remote Code Execution</li>
                        </ul>

                        <p><strong>PHP Magic Methods Exploited:</strong></p>
                        <ul>
                            <li>__destruct() - Called when object destroyed</li>
                            <li>__wakeup() - Called when unserializing</li>
                            <li>__toString() - Called when object used as string</li>
                        </ul>

                        <p><strong>Real Attacks:</strong></p>
                        <ul>
                            <li>2017: Apache Struts - Equifax breach (143M records)</li>
                            <li>Java deserialization exploits (RMI, JMX)</li>
                            <li>Python pickle exploits</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-success text-white"><h5>FIX: How to Fix</h5></div>
            <div class="card-body">
                <pre>
// 1. Never unserialize untrusted data
// Use JSON instead
$data = json_decode($_POST['data'], true);

// 2. If must use serialization, implement signature
function sign($data) {
    $secret = random_bytes(32); // Strong secret
    return hash_hmac('sha256', $data, $secret);
}

function verify($data, $signature) {
    return hash_equals($signature, sign($data));
}

// 3. Use type checking
if(!($obj instanceof ExpectedClass)) {
    die("Invalid object type");
}

// 4. Disable dangerous functions
// In php.ini: disable_functions = unserialize

// 5. Use signed tokens (JWT) with strong secrets
// Use libraries like firebase/php-jwt

// 6. Implement integrity checks
// Use HMAC or digital signatures
</pre>
            </div>
        </div>

        <div class="mt-3">
            <a href="dashboard-vulnerable.php">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
