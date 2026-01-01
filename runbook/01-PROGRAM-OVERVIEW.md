# 01 - Program Overview & Features
## Medview+ Vulnerable Hospital Management System

Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.

---

## System Description

**Medview+ Vulnerable HMS** is an intentionally vulnerable web application designed for security training and penetration testing practice. It simulates a real-world Hospital Management System with authentic healthcare workflows while containing deliberate security vulnerabilities for educational purposes.

### **Purpose:**
- Security training platform
- Penetration testing practice environment
- Bug bounty preparation
- University cybersecurity courses
- OSCP/CEH/GWAPT certification prep
- Red team/Blue team exercises

### **Target Audience:**
- Security students (beginner to expert)
- Penetration testers
- Security researchers
- Bug bounty hunters
- Security instructors
- CTF participants

---

## Hospital Management System Features

### **1. Patient Management**

**User Registration & Authentication:**
- Patient self-registration portal
- Email verification system
- Secure login (intentionally vulnerable in training mode)
- Password reset functionality
- Profile management

**Medical Records:**
- Personal health information storage
- Medical history tracking
- Prescription records
- Diagnostic reports
- Lab results access

**Appointment System:**
- Online doctor appointment booking
- Appointment scheduling
- Doctor specialization search
- Appointment history viewing
- Appointment cancellation

### **2. Doctor Management**

**Doctor Portal:**
- Separate doctor login
- Patient management interface
- Appointment viewing and management
- Medical record updates
- Prescription management

**Specializations:**
- Cardiology
- Neurology
- Pediatrics
- General Medicine
- Emergency Care

### **3. Administrative Functions**

**Admin Dashboard:**
- User management
- Doctor management
- Appointment oversight
- System reports
- Query management

**Reporting:**
- Appointment statistics
- Patient demographics
- Doctor performance metrics
- System usage reports
- Date-range filtering

### **4. Payment & Billing (NEW)**

**Financial Management:**
- Patient account balance (starting ₦1,000 NGN)
- Service payment processing
- Medication purchasing
- Transaction history
- Invoice generation

**Cryptocurrency Integration:**
- Bitcoin exchange (₦65M per BTC)
- Ethereum exchange (₦8.5M per ETH)
- Wallet transfers
- Crypto-to-fiat conversion
- Withdrawal to Nigerian banks

### **5. Gamification & CTF (NEW)**

**Exploit Rewards:**
- ₦500 NGN per unique exploit
- 20 unique flags to capture
- Difficulty-based challenges
- Exploit tracking & logging
- Scoreboard system

**Flags:**
- One-time rewards per vulnerability
- Unique flag codes (e.g., FLAG{SQL_1nj3ct10n_M4st3r_2025})
- Difficulty levels: Easy, Medium, Hard, Expert
- Real-world CVE examples

---

## Technical Architecture

### **Technology Stack:**

**Frontend:**
- HTML5
- CSS3 (Bootstrap 4)
- JavaScript (jQuery)
- Responsive design

**Backend:**
- PHP 7.4+
- MySQL/MariaDB database
- Session-based authentication
- File upload handling

**Security Features (Disabled in Training):**
- Input validation (configurable)
- SQL injection protection (configurable)
- XSS filtering (configurable)
- CSRF tokens (configurable)
- WAF integration (Cloudflare)

**External Integrations:**
- Cloudflare WAF (optional)
- PHPMailer for emails
- Captcha system (disabled)

### **Directory Structure:**

```
Medview/
├── vulnerable/              # Vulnerable version
│   ├── user-login-sqli.php       # Patient login
│   ├── dashboard-vulnerable.php   # Patient dashboard
│   ├── book-appointment-vulnerable.php
│   ├── appointment-history-vulnerable.php
│   ├── profile-vulnerable.php
│   ├── medical-history-vulnerable.php
│   ├── mass-assignment-vulnerable.php
│   ├── cashout-crypto.php        # NEW: Crypto cashout
│   ├── api-vulnerable.php        # NEW: REST API
│   ├── config-vulnerable.php     # Database config
│   ├── gamification.php          # NEW: CTF system
│   ├── difficulty-manager.php    # NEW: Difficulty system
│   └── cloudflare-config.php     # NEW: WAF integration
│
├── hms/                    # Original secure version
│   ├── user-login.php
│   ├── dashboard.php
│   └── ...
│
├── images/                 # Static assets
├── css/                    # Stylesheets
├── js/                     # JavaScript files
└── runbook/               # This documentation
```

---

## User Roles & Capabilities

### **1. Patient (Regular User)**

**Credentials:**
```
Email: ali@sheflabs.com
Password: Password123
Starting Balance: ₦1,000 NGN
```

**Capabilities:**
- Register new account
- Login/logout
- View personal dashboard
- Book doctor appointments
- View appointment history
- Update profile information
- View medical records
- Make payments for services
- Purchase medications
- Cashout to crypto/bank
- Exploit vulnerabilities for rewards

**Vulnerable Actions:**
- SQL injection in login
- IDOR in appointment viewing
- XSS in profile updates
- Mass assignment in payments
- Business logic exploitation

### **2. Doctor**

**Credentials:**
```
Email: sarah@medview.com
Password: doctor123
```

**Capabilities:**
- Login to doctor portal
- View assigned patients
- Manage appointments
- Update medical records
- Prescribe medications
- Search patient records

**Vulnerable Actions:**
- SQL injection in searches
- Unauthorized patient access
- Prescription tampering

### **3. Administrator**

**Credentials:**
```
Username: admin
Password: admin123
```

**Capabilities:**
- Full system access
- User management (CRUD)
- Doctor management (CRUD)
- Appointment oversight
- System reports
- Query management
- Configuration changes

**Vulnerable Actions:**
- Privilege escalation
- Mass user manipulation
- System-wide SQL injection
- Command injection in reports

---

## Key Features for Training

### **1. Progressive Difficulty System**

**Four Difficulty Levels:**

**Low:**
- All protections disabled
- Obvious vulnerabilities
- Visible exploitation hints
- No WAF blocking
- Perfect for beginners

**Medium:**
- Basic input validation
- Some SQL escaping
- Simple WAF rules
- Bypass techniques required
- Intermediate level

**Hard:**
- Strong input validation
- Prepared statements
- Advanced WAF (Cloudflare)
- Complex bypass needed
- Advanced techniques

**Impossible:**
- Production-level security
- All protections enabled
- Aggressive WAF + rate limiting
- Nearly unbreakable
- Expert challenge

### **2. Real-World Vulnerability Examples**

**Mirrors 30+ CVE Examples:**
- CVE-2017-5638 (Equifax breach)
- CVE-2014-6271 (Shellshock)
- CVE-2021-44228 (Log4j)
- CVE-2021-21972 (VMware SSRF)
- And 25+ more real CVEs

**OWASP Coverage:**
- 100% OWASP Top 10 2025
- 100% OWASP API Top 10
- Common weakness enumeration (CWE)

### **3. Educational Features**

**Hidden Hints:**
```php
// Hidden Hint: Try ../../../etc/passwd in file parameter
// Hidden Hint: SQL injection possible - no sanitization
// Hidden Hint: Extract() creates variables from POST data
```

**Debug Information:**
```html
<!-- DEBUG: SQL Query: SELECT * FROM users WHERE email='admin' -->
<!-- DEBUG: File path: /var/www/html/uploads/shell.php -->
```

**Exploit Notifications:**
```
VULNERABILITY EXPLOITED!
Vulnerability: SQL Injection - Login Bypass
Difficulty: Easy
Reward: ₦500.00 NGN
FLAG: FLAG{SQL_1nj3ct10n_M4st3r_2025}
```

### **4. Realistic Workflows**

**Complete HMS Functionality:**
- Not just isolated vulnerability demos
- Real business logic
- Authentic user flows
- Production-like features
- Real-world scenarios

**Example Workflow:**
```
1. Patient registers → SQL injection possible
2. Patient logs in → Authentication bypass
3. Books appointment → IDOR vulnerability
4. Makes payment → Mass assignment flaw
5. Cashes out → Business logic exploit
6. Earns ₦500 per exploit!
```

---

## Gamification & Economics

### **Starting State:**
- **Balance:** ₦1,000 NGN
- **Flags Captured:** 0/20
- **Exploits Found:** 0
- **Difficulty:** Low (default)

### **Earning Money:**

**Method 1: Capture Flags**
```
Each unique vulnerability exploit: +₦500 NGN
Total possible: 20 flags × ₦500 = ₦10,000 NGN
```

**Method 2: Business Logic Exploits**
```
Negative withdrawal: Infinite money
Rate manipulation: Free crypto
Race conditions: Double-spend
```

### **Spending Money:**

**Medications:**
- Paracetamol: ₦50
- Amoxicillin: ₦150
- Ibuprofen: ₦75
- Medical equipment: ₦2,500-₦5,000

**Services:**
- Doctor appointments: ₦100-₦200
- Lab tests: ₦500
- Prescriptions: Variable

**Cashout Options:**
- Nigerian bank withdrawal
- Bitcoin purchase (₦65M/BTC)
- Ethereum purchase (₦8.5M/ETH)
- External wallet transfer

---

## Security Posture by Mode

### **Training Mode (Vulnerable):**
```
Path: /vulnerable/
Protection: Minimal to none
WAF: Configurable (Cloudflare optional)
Purpose: Learning exploitation
Audience: Students, pentesters
Status: Intentionally insecure
```

**Characteristics:**
- No input validation (configurable)
- No output encoding (configurable)
- Detailed error messages
- Weak authentication
- No rate limiting
- No CAPTCHA
- MD5 password hashing
- SQL concatenation
- Hidden educational hints (enabled)
- Exploit tracking (enabled)
- CTF gamification (enabled)

### **Production Mode (Secure):**
```
Path: /hms/
Protection: Full security controls
WAF: Production-grade
Purpose: Secure reference
Audience: Comparison/learning
Status: Properly secured
```

**Characteristics:**
- Input validation (enabled)
- Output encoding (enabled)
- Prepared statements (enabled)
- Strong passwords (enforced)
- Rate limiting (enabled)
- CAPTCHA protection (enabled)
- bcrypt hashing (enabled)
- CSRF tokens (enabled)
- Secure headers (enabled)
- Logging & monitoring (enabled)

---

## System Statistics

### **Application Metrics:**
- **Total PHP Files:** 50+
- **Vulnerable Pages:** 20
- **Database Tables:** 15+
- **User Roles:** 3 (Patient, Doctor, Admin)
- **API Endpoints:** 10+
- **File Upload Points:** 3
- **Form Inputs:** 100+

### **Vulnerability Metrics:**
- **Total Vulnerabilities:** 25+ types
- **OWASP Top 10:** 10/10 covered
- **OWASP API Top 10:** 10/10 covered
- **CVE Examples:** 30+
- **Unique Flags:** 20
- **Difficulty Levels:** 4

### **Training Metrics:**
- **Estimated Learning Time:** 80+ hours
- **Difficulty Progression:** 8 weeks
- **Tool Coverage:** 20+ tools
- **Techniques Taught:** 50+

---

## Educational Value

### **Skills Developed:**

**Technical Skills:**
- Web application penetration testing
- SQL injection exploitation
- XSS attack vectors
- Authentication bypass
- Authorization flaws
- Business logic exploitation
- API security testing
- WAF bypass techniques
- Tool proficiency (Burp, SQLMap, etc.)

**Conceptual Understanding:**
- OWASP Top 10
- Common vulnerabilities
- Attack vectors
- Defense mechanisms
- Secure coding practices
- Risk assessment
- Remediation strategies

**Real-World Application:**
- Bug bounty hunting
- Penetration testing engagements
- Security audits
- Code review
- Threat modeling
- Incident response

---

## Getting Started

### **Quick Start:**

1. **Access the system:**
   ```
   URL: http://localhost/Medview/vulnerable/
   ```

2. **Login with default credentials:**
   ```
   Username: ali@sheflabs.com
   Password: Password123
   ```

3. **Select difficulty level:**
   ```
   Difficulty: Low (start here)
   ```

4. **Start exploring:**
   - View dashboard
   - Try booking appointment
   - Attempt SQL injection
   - Capture your first flag!

### **Next Steps:**
1. Read [02 - Vulnerability Catalog](02-VULNERABILITY-CATALOG.md)
2. Understand [03 - Difficulty System](03-DIFFICULTY-SYSTEM.md)
3. Practice [04 - SQL Injection Exploitation](04-EXPLOITATION-SQLI.md)

---

## Important Disclaimers

### **Legal:**
This system is for **AUTHORIZED TRAINING ONLY**
Never use these techniques on systems you don't own
Unauthorized access to computer systems is **ILLEGAL**
Always obtain written permission
Use in isolated lab environments only

### **Ethical:**
- Respect responsible disclosure
- Don't harm real systems
- Use knowledge for defense
- Help make software more secure
- Follow laws and regulations

---

**Next:** [02 - Vulnerability Catalog →](02-VULNERABILITY-CATALOG.md)

---

**Last Updated:** 2026-01-01
**Version:** 1.0
