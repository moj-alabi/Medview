<?php
// OPEN REDIRECT VULNERABILITY
// WARNING: FOR TRAINING PURPOSES ONLY
// Hidden Hint: redirect parameter not validated, can redirect to any URL

// VULNERABILITY: Open Redirect
// Hidden Hint: ?redirect=http://evil.com redirects without validation
if(isset($_GET['redirect'])) {
    $redirect_url = $_GET['redirect'];
    // No validation or whitelist - DANGEROUS!
    // Hidden Hint: Can be used for phishing attacks
    header("Location: " . $redirect_url);
    exit();
}

// VULNERABILITY: Also in logout
if(isset($_GET['logout'])) {
    session_start();
    session_destroy();
    $next = isset($_GET['next']) ? $_GET['next'] : 'user-login-sqli.php';
    // Hidden Hint: next parameter allows arbitrary redirects
    header("Location: " . $next);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Open Redirect Demo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h4>🎯 Open Redirect Vulnerability</h4>
            <p><strong>Try these URLs:</strong></p>
            <ul>
                <li><a href="?redirect=http://evil.com">Redirect to evil.com</a></li>
                <li><a href="?redirect=https://google.com">Redirect to Google</a></li>
                <li><a href="?redirect=javascript:alert('XSS')">JavaScript protocol</a></li>
                <li><a href="?logout=1&next=http://phishing-site.com">Logout redirect</a></li>
            </ul>
            
            <h6 class="mt-3">Real Attack Scenario:</h6>
            <code>https://medview.com/vulnerable/open-redirect.php?redirect=http://fake-medview.com/login</code>
            <p class="mt-2 small">Victim sees trusted domain, clicks link, gets redirected to phishing site</p>
            
            <h6 class="mt-3">🛡️ Fix:</h6>
            <pre class="bg-light p-2">// Whitelist allowed domains
$allowed = ['medview.com', 'trusted-partner.com'];
$parsed = parse_url($_GET['redirect']);
if(!in_array($parsed['host'], $allowed)) {
    die("Redirect not allowed");
}</pre>
        </div>
    </div>
</body>
</html>
