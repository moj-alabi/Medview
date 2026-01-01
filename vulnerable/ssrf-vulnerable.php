<?php
// VULNERABLE URL FETCHER - SSRF (Server-Side Request Forgery)
// WARNING: FOR TRAINING PURPOSES ONLY
// Hidden Hint: User-controlled URL without validation allows internal network access

session_start();
include("config-vulnerable.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VULNERABILITY: No authentication check
// Hidden Hint: Public endpoint accessible to anyone

$response = "";
$url = "";
$error = "";

if(isset($_POST['fetch'])) {
    $url = $_POST['url'];
    
    // VULNERABILITY #1: Server-Side Request Forgery (SSRF)
    // Hidden Hint: Server will make requests to any URL including internal IPs
    // No URL validation or whitelist
    
    // Hidden Hint: Can access localhost, private IPs, cloud metadata
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            // VULNERABILITY: Follows redirects (can be exploited)
            // Hidden Hint: Attacker can redirect to internal resources
            'follow_location' => 1
        ]
    ]);
    
    // VULNERABILITY #2: No URL scheme validation
    // Hidden Hint: file://, gopher://, dict:// protocols possible
    $response = @file_get_contents($url, false, $context);
    
    if($response === false) {
        // VULNERABILITY: Information disclosure in errors
        // Hidden Hint: Error messages reveal internal network structure
        $error = error_get_last()['message'];
    }
}

// VULNERABILITY #3: XML External Entity (XXE) if XML parsing
// Hidden Hint: If parsing XML from fetched content, XXE possible
if(isset($_POST['parse_xml'])) {
    $xml_content = $_POST['xml_content'];
    
    // VULNERABILITY: XXE - External entities enabled
    // Hidden Hint: Can read local files via XXE
    libxml_disable_entity_loader(false); // Deliberately vulnerable
    $xml = simplexml_load_string($xml_content);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>URL Fetcher - SSRF Training</title>
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
        .response-box {
            background: #f8f9fa;
            padding: 20px;
            border: 1px solid #ddd;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        WARNING: VULNERABLE VERSION - FOR SECURITY TRAINING ONLY WARNING:
    </div>
    
    <div class="container">
        <h2>Health API URL Fetcher</h2>
        <p class="text-muted">Fetch patient data from external health APIs</p>

        <div class="alert alert-danger">
            <h5>TARGET: Training Target: Server-Side Request Forgery (SSRF)</h5>
            <p><strong>Vulnerabilities Present:</strong></p>
            <ul>
                <li>No URL validation or whitelist</li>
                <li>Can access internal network resources</li>
                <li>Can read cloud metadata services</li>
                <li>Follows redirects (redirect-based SSRF)</li>
                <li>No authentication required</li>
                <li>Multiple protocol support (file://, gopher://, etc.)</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Fetch URL</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label>URL to Fetch:</label>
                                <input type="text" name="url" class="form-control" 
                                       placeholder="https://api.example.com/data"
                                       value="<?php echo htmlspecialchars($url); ?>">
                            </div>
                            <button type="submit" name="fetch" class="btn btn-primary btn-block">
                                Fetch URL
                            </button>
                        </form>

                        <hr>

                        <h6>Intended Use Examples:</h6>
                        <ul class="small">
                            <li>https://jsonplaceholder.typicode.com/users/1</li>
                            <li>https://api.github.com/users/octocat</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="alert alert-warning">
                    <strong>SSRF Exploitation Examples:</strong>
                    <pre class="mb-0 small">
<strong>Internal Network Access:</strong>
http://localhost:80
http://127.0.0.1:8080
http://0.0.0.0:3306
http://192.168.1.1

<strong>Cloud Metadata (AWS):</strong>
http://169.254.169.254/latest/meta-data/
http://169.254.169.254/latest/user-data/

<strong>Cloud Metadata (Azure):</strong>
http://169.254.169.254/metadata/instance?api-version=2021-02-01

<strong>Cloud Metadata (GCP):</strong>
http://metadata.google.internal/computeMetadata/v1/

<strong>Local File Access:</strong>
file:///etc/passwd
file:///c:/windows/system.ini

<strong>Port Scanning:</strong>
http://internal-host:22
http://internal-host:3306
http://internal-host:6379

<strong>Protocol Smuggling:</strong>
gopher://localhost:3306/_
dict://localhost:11211/stat
</pre>
                </div>
            </div>
        </div>

        <?php if($response): ?>
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5>Response from: <?php echo htmlspecialchars($url); ?></h5>
            </div>
            <div class="card-body">
                <div class="response-box">
                    <?php echo htmlspecialchars($response); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div class="alert alert-danger mt-4">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5>TIP: Understanding SSRF</h5>
            </div>
            <div class="card-body">
                <p>Server-Side Request Forgery allows an attacker to make the server perform HTTP requests to arbitrary locations.</p>

                <h6>Attack Scenarios:</h6>
                <ul>
                    <li><strong>Internal Network Scanning:</strong> Map internal infrastructure</li>
                    <li><strong>Cloud Metadata Access:</strong> Steal AWS/Azure/GCP credentials</li>
                    <li><strong>Port Scanning:</strong> Identify running services</li>
                    <li><strong>Bypass Firewalls:</strong> Access internal-only services</li>
                    <li><strong>Local File Read:</strong> Using file:// protocol</li>
                    <li><strong>Denial of Service:</strong> Exhaust server resources</li>
                </ul>

                <h6>Real-World Impact:</h6>
                <pre class="bg-light p-3">
<strong>Capital One Breach (2019):</strong>
- Attacker used SSRF to access AWS metadata
- Stole credentials from EC2 instance metadata
- Compromised 100+ million customer records
- $80 million fine

<strong>Cloud Metadata Example:</strong>
http://169.254.169.254/latest/meta-data/iam/security-credentials/role-name
Returns: AWS access keys, secret keys, session tokens
</pre>

                <h6>FIX: How to Fix:</h6>
                <pre>
// 1. Whitelist allowed domains/IPs
$allowed_hosts = ['api.trusted.com', 'api.partner.com'];
$parsed = parse_url($url);
if(!in_array($parsed['host'], $allowed_hosts)) {
    die("Host not allowed");
}

// 2. Blacklist private IP ranges
function isPrivateIP($url) {
    $host = parse_url($url, PHP_URL_HOST);
    $ip = gethostbyname($host);
    
    if(filter_var($ip, FILTER_VALIDATE_IP, 
       FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return true;
    }
    return false;
}

// 3. Disable redirects
stream_context_create(['http' => ['follow_location' => 0]]);

// 4. Use allow-list for protocols
$allowed = ['http', 'https'];
$scheme = parse_url($url, PHP_URL_SCHEME);
if(!in_array($scheme, $allowed)) die("Protocol not allowed");

// 5. Implement timeout limits
// 6. Don't return raw responses to users
</pre>
            </div>
        </div>

        <div class="mt-3">
            <a href="dashboard-vulnerable.php">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
