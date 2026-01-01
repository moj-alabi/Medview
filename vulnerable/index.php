<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medview+ Vulnerable Version - Security Training Lab</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .warning-banner {
            background: #ff0000;
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
            font-size: 1.2em;
        }
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .vulnerability-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .vulnerability-card:hover {
            border-color: #ffc107;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .difficulty-badge {
            font-size: 0.8em;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .difficulty-beginner { background: #28a745; color: white; }
        .difficulty-intermediate { background: #ffc107; color: black; }
        .difficulty-advanced { background: #dc3545; color: white; }
        .owasp-badge {
            background: #000;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.75em;
            font-weight: bold;
        }
        .info-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            padding: 30px;
            background: #343a40;
            color: white;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="warning-banner">
        ⚠️⚠️⚠️ INTENTIONALLY VULNERABLE VERSION - FOR SECURITY TRAINING ONLY ⚠️⚠️⚠️
        <br><small>DO NOT DEPLOY TO PRODUCTION OR PUBLIC SERVERS</small>
    </div>

    <div class="container">
        <div class="hero">
            <h1><i class="material-icons" style="font-size: 50px; vertical-align: middle;">security</i> Medview+ Vulnerable</h1>
            <p class="lead">A Deliberately Insecure Hospital Management System for Security Training</p>
            <p>Learn to identify and exploit common web vulnerabilities in a safe environment</p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="info-section">
                    <h3><i class="material-icons" style="vertical-align: middle;">info</i> About This Lab</h3>
                    <p>This is an intentionally vulnerable version of Medview+, designed similar to DVWA (Damn Vulnerable Web Application) for security training and education. Each module demonstrates a different vulnerability class from the OWASP Top 10.</p>
                    <p><strong>Learning Goals:</strong></p>
                    <ul>
                        <li>Understand common web application vulnerabilities</li>
                        <li>Practice exploitation techniques in a legal, safe environment</li>
                        <li>Learn secure coding practices by seeing what NOT to do</li>
                        <li>Prepare for security certifications (CEH, OSCP, etc.)</li>
                    </ul>
                </div>
            </div>
        </div>

        <h2 class="mt-5 mb-4">
            <i class="material-icons" style="vertical-align: middle;">school</i> Training Modules
        </h2>

        <div class="row">
            <!-- SQL Injection Module -->
            <div class="col-md-6">
                <div class="vulnerability-card">
                    <h4>
                        <i class="material-icons" style="color: #dc3545; vertical-align: middle;">bug_report</i>
                        SQL Injection (SQLi)
                        <span class="owasp-badge">OWASP #3</span>
                    </h4>
                    <p><span class="difficulty-badge difficulty-beginner">Beginner</span></p>
                    <p><strong>Learn:</strong> How attackers manipulate SQL queries to bypass authentication and extract data</p>
                    <p><strong>Techniques:</strong> Authentication bypass, UNION queries, blind SQLi, error-based SQLi</p>
                    <a href="user-login-sqli.php" class="btn btn-danger btn-block">
                        <i class="material-icons" style="vertical-align: middle; font-size: 18px;">launch</i>
                        Launch SQL Injection Lab
                    </a>
                </div>
            </div>

            <!-- XSS Module -->
            <div class="col-md-6">
                <div class="vulnerability-card">
                    <h4>
                        <i class="material-icons" style="color: #ff9800; vertical-align: middle;">code</i>
                        Cross-Site Scripting (XSS)
                        <span class="owasp-badge">OWASP #7</span>
                    </h4>
                    <p><span class="difficulty-badge difficulty-beginner">Beginner</span></p>
                    <p><strong>Learn:</strong> Inject malicious scripts that execute in users' browsers</p>
                    <p><strong>Techniques:</strong> Reflected XSS, stored XSS, DOM XSS, cookie stealing</p>
                    <a href="search-xss.php" class="btn btn-warning btn-block">
                        <i class="material-icons" style="vertical-align: middle; font-size: 18px;">launch</i>
                        Launch XSS Lab
                    </a>
                </div>
            </div>

            <!-- Command Injection Module -->
            <div class="col-md-6">
                <div class="vulnerability-card">
                    <h4>
                        <i class="material-icons" style="color: #9c27b0; vertical-align: middle;">terminal</i>
                        Command Injection
                        <span class="owasp-badge">OWASP #3</span>
                    </h4>
                    <p><span class="difficulty-badge difficulty-intermediate">Intermediate</span></p>
                    <p><strong>Learn:</strong> Execute arbitrary system commands through vulnerable inputs</p>
                    <p><strong>Techniques:</strong> Command chaining, shell metacharacters, reverse shells</p>
                    <a href="command-injection.php" class="btn btn-primary btn-block">
                        <i class="material-icons" style="vertical-align: middle; font-size: 18px;">launch</i>
                        Launch Command Injection Lab
                    </a>
                </div>
            </div>

            <!-- File Upload Module -->
            <div class="col-md-6">
                <div class="vulnerability-card">
                    <h4>
                        <i class="material-icons" style="color: #f44336; vertical-align: middle;">cloud_upload</i>
                        Unrestricted File Upload
                        <span class="owasp-badge">OWASP #4</span>
                    </h4>
                    <p><span class="difficulty-badge difficulty-intermediate">Intermediate</span></p>
                    <p><strong>Learn:</strong> Upload malicious files to gain remote code execution</p>
                    <p><strong>Techniques:</strong> Web shells, filter bypass, RCE via uploads</p>
                    <a href="file-upload.php" class="btn btn-danger btn-block">
                        <i class="material-icons" style="vertical-align: middle; font-size: 18px;">launch</i>
                        Launch File Upload Lab
                    </a>
                </div>
            </div>
        </div>

        <div class="info-section mt-5">
            <h3><i class="material-icons" style="vertical-align: middle;">build</i> Recommended Tools</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Essential Tools:</h5>
                    <ul>
                        <li><strong>Burp Suite Community</strong> - Intercept and modify HTTP requests</li>
                        <li><strong>OWASP ZAP</strong> - Automated vulnerability scanner</li>
                        <li><strong>Browser DevTools</strong> - Inspect and debug</li>
                        <li><strong>Postman</strong> - API testing</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Advanced Tools:</h5>
                    <ul>
                        <li><strong>SQLMap</strong> - Automated SQL injection</li>
                        <li><strong>Nikto</strong> - Web server scanner</li>
                        <li><strong>Metasploit</strong> - Exploitation framework</li>
                        <li><strong>Netcat</strong> - Network tool for shells</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3><i class="material-icons" style="vertical-align: middle;">compare_arrows</i> Comparison with Secure Version</h3>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Feature</th>
                        <th>Vulnerable Version</th>
                        <th>Secure Version (Original)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SQL Queries</td>
                        <td><span style="color: red;">❌ String concatenation</span></td>
                        <td><span style="color: green;">✅ Prepared statements</span></td>
                    </tr>
                    <tr>
                        <td>Password Storage</td>
                        <td><span style="color: red;">❌ MD5 / Plaintext</span></td>
                        <td><span style="color: green;">✅ password_hash()</span></td>
                    </tr>
                    <tr>
                        <td>CAPTCHA</td>
                        <td><span style="color: red;">❌ Disabled</span></td>
                        <td><span style="color: green;">✅ Enabled</span></td>
                    </tr>
                    <tr>
                        <td>2FA/OTP</td>
                        <td><span style="color: red;">❌ Disabled</span></td>
                        <td><span style="color: green;">✅ Email OTP</span></td>
                    </tr>
                    <tr>
                        <td>Input Validation</td>
                        <td><span style="color: red;">❌ None</span></td>
                        <td><span style="color: green;">✅ Comprehensive</span></td>
                    </tr>
                    <tr>
                        <td>Output Encoding</td>
                        <td><span style="color: red;">❌ None</span></td>
                        <td><span style="color: green;">✅ htmlspecialchars()</span></td>
                    </tr>
                    <tr>
                        <td>CSRF Protection</td>
                        <td><span style="color: red;">❌ None</span></td>
                        <td><span style="color: green;">✅ Tokens</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="info-section">
            <h3><i class="material-icons" style="vertical-align: middle;">library_books</i> Learning Resources</h3>
            <div class="row">
                <div class="col-md-4">
                    <h5>Documentation:</h5>
                    <ul>
                        <li><a href="https://owasp.org/www-project-top-ten/" target="_blank">OWASP Top 10</a></li>
                        <li><a href="https://portswigger.net/web-security" target="_blank">PortSwigger Academy</a></li>
                        <li><a href="../VULNERABLE_VERSION_README.md" target="_blank">Full Documentation</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Practice Platforms:</h5>
                    <ul>
                        <li><a href="https://www.hackthebox.com/" target="_blank">HackTheBox</a></li>
                        <li><a href="https://tryhackme.com/" target="_blank">TryHackMe</a></li>
                        <li><a href="https://www.dvwa.co.uk/" target="_blank">DVWA</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Certifications:</h5>
                    <ul>
                        <li>CEH - Certified Ethical Hacker</li>
                        <li>OSCP - Offensive Security</li>
                        <li>GWAPT - Web App Pentester</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="alert alert-danger mt-5" role="alert">
            <h4 class="alert-heading"><i class="material-icons" style="vertical-align: middle;">warning</i> Legal Warning</h4>
            <p><strong>Use this training lab ONLY in your private, isolated environment.</strong></p>
            <ul>
                <li>Never deploy to production servers</li>
                <li>Do not use these techniques on systems without authorization</li>
                <li>Unauthorized access to computer systems is illegal</li>
                <li>This is for educational purposes only</li>
            </ul>
            <hr>
            <p class="mb-0"><strong>Remember:</strong> With great power comes great responsibility. Use your security knowledge ethically!</p>
        </div>

        <div class="text-center mt-5 mb-5">
            <a href="../index.html" class="btn btn-success btn-lg">
                <i class="material-icons" style="vertical-align: middle;">lock</i>
                Return to Secure Version
            </a>
        </div>
    </div>

    <div class="footer">
        <p><strong>Medview+ Vulnerable Version</strong></p>
        <p>Educational Security Training Platform | Based on OWASP Top 10</p>
        <p style="font-size: 0.9em;">⚠️ FOR TRAINING PURPOSES ONLY - DO NOT USE IN PRODUCTION ⚠️</p>
        <p style="font-size: 0.8em;">Learn to hack legally • Practice ethically • Secure the web</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
