# Medview+ Vulnerable Hospital Management System

**Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.**

---

## Project Overview

Medview+ Hospital Management System is an intentionally vulnerable web application designed for security training, penetration testing practice, and cybersecurity education. It simulates a real Hospital Management System with authentic workflows while containing deliberate security vulnerabilities for educational purposes.

**Version:** 1.0  
**Release Date:** January 2026  
**License:** MIT License (See LICENSE file)  
**Author:** MOJ for Sheflabs

---

## Key Features

### Functional HMS System
- Patient registration and authentication
- Doctor appointment booking system
- Medical records management
- Administrative dashboard
- Payment processing with cryptocurrency integration
- Real business logic workflows

### Security Training Features
- 25+ different vulnerability types
- 4 difficulty levels (Low, Medium, Hard, Impossible)
- CTF-style gamification with 20 unique flags
- Reward system (500 NGN per exploit)
- Cloudflare WAF integration
- Real-world CVE examples (30+ mapped)
- 100% OWASP Top 10 2025 coverage
- 100% OWASP API Top 10 coverage

### Educational Components
- Progressive difficulty system
- Hidden hints in source code
- Exploit detection and tracking
- Automated flag capture
- Comprehensive documentation
- Step-by-step exploitation guides


---
### Screenshot

1. Home page

![1](https://user-images.githubusercontent.com/43385016/166294368-cec1c705-0d1f-4ac8-8902-e53c89ebae70.png)

2. Login page

![2](https://user-images.githubusercontent.com/43385016/166294637-06fbe77a-58c4-4a07-a67b-1edd41732ce1.png)

3. OTP verification

![3](https://user-images.githubusercontent.com/43385016/166295128-8cc47c70-6d78-4bd3-8977-558c62fd9986.png)

4. Patient's dashboard

![4](https://user-images.githubusercontent.com/43385016/166295747-dcecffc6-16f2-43ed-a8e9-4ba8fcb00727.png)

5. Doctor's dashboard

![5](https://user-images.githubusercontent.com/43385016/166295213-7eb312b1-434b-432a-8581-e9d03017782d.png)

6. Admin dashboard

![6](https://user-images.githubusercontent.com/43385016/166295271-1dffb62a-8cf2-4e40-98e9-17b2675ff0f8.png)


---

## Quick Start

### Installation

**Requirements:**
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+
- Apache or Nginx
- 512MB RAM minimum

**Setup:**
```bash
# 1. Install LAMP/WAMP/XAMPP
# 2. Copy application to web root
# 3. Create database
mysql -u root -p
CREATE DATABASE medview_vulnerable;
EXIT;

# 4. Import database schema
mysql -u root -p medview_vulnerable < vulnerable/setup_database.sql

# 5. Configure database connection
# Edit vulnerable/config-vulnerable.php with your credentials

# 6. Access application
http://localhost/Medview/vulnerable/
```

**Default Credentials:**
```
Patient: ali@sheflabs.com / Password123
Doctor: sarah@medview.com / doctor123
Admin: admin / admin123
```

For detailed installation instructions, see [INSTALLATION.md](../INSTALLATION.md)

---

## Security Warnings

### CRITICAL WARNINGS

**THIS IS A VULNERABLE APPLICATION FOR TRAINING ONLY**

1. **NEVER** deploy to production environments
2. **NEVER** use on public internet without proper isolation
3. **NEVER** store real patient or sensitive data
4. **NEVER** use real payment credentials
5. **ALWAYS** use in isolated lab environments
6. **ALWAYS** comply with local laws and regulations
7. **ALWAYS** obtain written permission before testing

### Legal Notice

Unauthorized access to computer systems is illegal. This application is provided solely for educational purposes in authorized training environments. Users are responsible for ensuring lawful use of this software.

---

# Complete Runbook & Training Manual

## Runbook Structure

This runbook provides complete documentation for the Medview+ Vulnerable Hospital Management System, including program descriptions, vulnerability catalogs, and step-by-step exploitation guides.

---

## Table of Contents

### **Part 1: Program Overview**
- [01 - Program Overview & Features](01-PROGRAM-OVERVIEW.md)
  - System description
  - Normal HMS features
  - Architecture overview
  - User roles & capabilities

### **Part 2: CVE Mapping & Real-World Examples**
- [02 - CVE Mapping](02-CVE-MAPPING.md)
  - 30+ real-world CVE examples
  - Breach case studies
  - Industry impact analysis
  - Vulnerability-to-CVE mapping

### **Part 3: Cloudflare WAF Setup**
- [03 - Cloudflare Setup Guide](03-CLOUDFLARE-SETUP.md)
  - WAF configuration per difficulty level
  - Firewall rules
  - Security settings
  - Testing procedures

### **Part 4: Complete Exploitation Walkthrough**
- [04 - Complete Exploitation Walkthrough](04-COMPLETE-EXPLOITATION-WALKTHROUGH.md)
  - 15+ vulnerabilities covered
  - Step-by-step instructions
  - Multiple payloads per vulnerability
  - Success indicators and flag capture
  - Focuses on Low difficulty (with notes on other levels)

### **Part 5: Man-in-the-Middle Attacks**
- [05 - MITM Attacks Guide](05-MITM-ATTACKS-GUIDE.md)
  - Complete Burp Suite setup and mastery
  - Traffic interception techniques
  - Request/response modification
  - Alternative tools (ZAP, mitmproxy, Fiddler)
  - MITM for all difficulty levels
  - 8 practical exploitation scenarios

### **Part 6: Exploitation Guides by Vulnerability**

#### **Basic Vulnerabilities (Low Difficulty)**
- [06 - SQL Injection Exploitation](06-EXPLOITATION-SQLI.md)
- [07 - Cross-Site Scripting (XSS)](07-EXPLOITATION-XSS.md)
- [08 - Insecure Direct Object Reference (IDOR)](08-EXPLOITATION-IDOR.md)
- [09 - Open Redirect](09-EXPLOITATION-OPEN-REDIRECT.md)

#### **Intermediate Vulnerabilities (Medium Difficulty)**
- [10 - Mass Assignment](10-EXPLOITATION-MASS-ASSIGNMENT.md)
- [11 - Directory Traversal / LFI](11-EXPLOITATION-DIRECTORY-TRAVERSAL.md)
- [12 - Business Logic Flaws](12-EXPLOITATION-BUSINESS-LOGIC.md)
- [13 - API Security Issues](13-EXPLOITATION-API.md)

#### **Advanced Vulnerabilities (Hard Difficulty)**
- [14 - Command Injection](14-EXPLOITATION-COMMAND-INJECTION.md)
- [15 - File Upload RCE](15-EXPLOITATION-FILE-UPLOAD.md)
- [16 - Server-Side Request Forgery (SSRF)](16-EXPLOITATION-SSRF.md)
- [17 - Race Conditions](17-EXPLOITATION-RACE-CONDITIONS.md)

#### **Expert Vulnerabilities (Impossible Difficulty)**
- [18 - Server-Side Template Injection (SSTI)](18-EXPLOITATION-SSTI.md)
- [19 - Insecure Deserialization](19-EXPLOITATION-DESERIALIZATION.md)
- [20 - JWT Advanced Attacks](20-EXPLOITATION-JWT.md)
- [21 - XML External Entity (XXE)](21-EXPLOITATION-XXE.md)

### **Part 7: Tools & Techniques**
- [22 - Penetration Testing Tools](22-TOOLS-REFERENCE.md)
  - Burp Suite
  - SQLMap
  - OWASP ZAP
  - Custom scripts
  - Automation tools

- [23 - WAF Bypass Techniques](23-WAF-BYPASS-TECHNIQUES.md)
  - WAF bypass methods
  - Filter evasion
  - Encoding techniques
  - Obfuscation methods

### **Part 8: CTF & Gamification**
- [24 - CTF Guide & Flags](24-CTF-GUIDE.md)
  - All 20 flags
  - Capture methods
  - Scoring system
  - Leaderboard

### **Part 9: Remediation**
- [25 - Security Fixes & Best Practices](25-REMEDIATION-GUIDE.md)
  - How to fix each vulnerability
  - Secure coding practices
  - Defense strategies
  - Security controls

---

## Quick Start Guides

### **For Beginners:**
Start here → [01-PROGRAM-OVERVIEW.md](01-PROGRAM-OVERVIEW.md)
Then → [04-COMPLETE-EXPLOITATION-WALKTHROUGH.md](04-COMPLETE-EXPLOITATION-WALKTHROUGH.md)
Learn MITM → [05-MITM-ATTACKS-GUIDE.md](05-MITM-ATTACKS-GUIDE.md)
Practice → Start with SQL Injection (covered in guide 04)

### **For Intermediate:**
Review → [02-CVE-MAPPING.md](02-CVE-MAPPING.md)
Master MITM → [05-MITM-ATTACKS-GUIDE.md](05-MITM-ATTACKS-GUIDE.md)
Practice → Medium difficulty exploits (guides 10-13)
Tools → [22-TOOLS-REFERENCE.md](22-TOOLS-REFERENCE.md)

### **For Advanced:**
Master → Hard difficulty guides (14-17)
Expert → Impossible difficulty guides (18-21)
Bypasses → [23-WAF-BYPASS-TECHNIQUES.md](23-WAF-BYPASS-TECHNIQUES.md)
Advanced MITM → All difficulty levels in guide 05

---

## Document Statistics

- **Total Pages:** 23 comprehensive guides
- **Vulnerabilities Covered:** 25+
- **Exploitation Examples:** 100+
- **Tool References:** 50+
- **Code Snippets:** 200+
- **Screenshots:** Described in detail
- **Difficulty Levels:** 4 (Low/Medium/Hard/Impossible)

---

## Learning Path

### **Week 1: Foundations**
```
Day 1-2: Read 01-PROGRAM-OVERVIEW.md
Day 3-4: Read 02-VULNERABILITY-CATALOG.md  
Day 5-7: Practice 04-EXPLOITATION-SQLI.md (Low difficulty)
```

### **Week 2: Basic Attacks**
```
Day 8-9: Practice 05-EXPLOITATION-XSS.md
Day 10-11: Practice 06-EXPLOITATION-IDOR.md
Day 12-14: Complete all Low difficulty
```

### **Week 3-4: Intermediate**
```
Week 3: Medium difficulty guides (08-11)
Week 4: Tool mastery (20-TOOLS-REFERENCE.md)
```

### **Week 5-6: Advanced**
```
Week 5: Hard difficulty guides (12-15)
Week 6: Bypass techniques (21-BYPASS-TECHNIQUES.md)
```

### **Week 7-8: Expert**
```
Week 7: Impossible difficulty guides (16-19)
Week 8: CTF completion & remediation study
```

---

## Pro Tips

### **For Students:**
1. Start with Low difficulty - learn the basics
2. Read the exploitation guide completely before trying
3. Follow step-by-step instructions exactly
4. Use recommended tools
5. Capture all 20 flags for completeness
6. Document your findings

### **For Instructors:**
1. Assign one vulnerability per week
2. Start easy, increase difficulty gradually
3. Review remediation after successful exploitation
4. Use CTF scoring for engagement
5. Discuss real-world impact of each vulnerability
6. Compare student techniques

---

## Safety Warnings

**CRITICAL WARNINGS:**

1. **NEVER use these techniques on systems you don't own**
2. **This is a TRAINING environment only**
3. **Unauthorized hacking is ILLEGAL**
4. **Always get written permission**
5. **Use isolated lab networks**
6. **Don't expose vulnerable version publicly**

---

## Support & Resources

### **Documentation:**
- Main README: `VULNERABLE_VERSION_README.md`
- Quick Start: `QUICK_START.md`
- CVE Mapping: `CVE_MAPPING.md`
- Cloudflare Setup: `CLOUDFLARE_SETUP.md`

### **External Resources:**
- OWASP Testing Guide: https://owasp.org/www-project-web-security-testing-guide/
- PortSwigger Academy: https://portswigger.net/web-security
- HackTheBox: https://www.hackthebox.com
- TryHackMe: https://tryhackme.com

---

## Document Conventions

### **Formatting:**
- `Code blocks` for commands
- **Bold** for important terms
- *Italics* for emphasis
- > Blockquotes for tips
- ⚠️ Warnings clearly marked
- ✅ Steps clearly numbered

### **Difficulty Indicators:**
- Low - Beginner friendly
- Medium - Intermediate
- Hard - Advanced
- Impossible - Expert

---

## Get Started

**Ready to begin?**

Start with: [01 - Program Overview & Features →](01-PROGRAM-OVERVIEW.md)

---

**Last Updated:** 2026-01-01
**Version:** 1.0
**Total Pages:** 23
**Estimated Reading Time:** 40+ hours
**Hands-On Practice Time:** 80+ hours


