# Cloudflare WAF Integration Guide
## Complete Setup for Medview+ Vulnerable HMS

Copyright (c) 2026 MOJ for Sheflabs. All rights reserved.

---

## **Quick Overview**

Your vulnerable HMS now integrates with **Cloudflare Free tier** to provide real WAF protection that scales with difficulty levels:

- Low: **Low:** All attacks allowed (learning mode)
- Medium: **Medium:** Basic protection (bypass training)
- Hard: **Hard:** Strong protection (advanced techniques)
- Impossible: **Impossible:** Maximum protection (nearly unbreakable)

---

## **Prerequisites**

Domain name (e.g., medview.com)
Cloudflare account (free tier sufficient)
Access to domain DNS settings
PHP application deployed on web server

---

## **Step-by-Step Setup**

### **Step 1: Add Domain to Cloudflare**

1. **Login to Cloudflare:** https://dash.cloudflare.com
2. **Click "Add a Site"**
3. **Enter your domain:** medview.com
4. **Select Free Plan**
5. **Cloudflare scans your DNS records**
6. **Review and confirm DNS records**

### **Step 2: Update Nameservers**

1. Cloudflare provides 2 nameservers:
   ```
   Example:
   ns1.cloudflare.com
   ns2.cloudflare.com
   ```

2. Go to your domain registrar (GoDaddy, Namecheap, etc.)
3. Replace existing nameservers with Cloudflare's
4. Wait for DNS propagation (5 mins - 48 hours)
5. Cloudflare will email when active

### **Step 3: Enable Cloudflare Proxy**

1. In Cloudflare Dashboard → DNS
2. Find your A/CNAME records
3. Toggle the cloud icon to **Orange** (Proxied)
   - Orange = Traffic goes through Cloudflare
   - Grey = Direct to origin (bypass Cloudflare)
4. Ensure your vulnerable app's subdomain is proxied

### **Step 4: Configure SSL/TLS**

1. Go to SSL/TLS tab
2. Set encryption mode: **Full** (recommended for training)
   - Not "Full (Strict)" unless you have valid SSL cert
3. This encrypts traffic between visitor and Cloudflare

---

## **Cloudflare Settings by Difficulty**

### **Low: Low Difficulty Settings**

**Purpose:** Allow all attacks for learning

**Configuration:**
1. **Security → Settings → Security Level:** Essentially Off
2. **Security → WAF:** Disable Managed Rules
3. **Security → Settings → Browser Integrity Check:** OFF
4. **Speed → Optimization:** Enable Developer Mode (bypasses cache/protection for 3 hours)

**OR use Developer Mode shortcut:**
- Quick Actions → Development Mode: ON
- This automatically allows everything

**Firewall Rules (Free: 5 rules):**
```
Rule Name: Allow Training IPs - Low
Expression: (http.request.uri.path contains "/vulnerable/")
Action: Allow
Priority: 1
```

---

### **Medium: Medium Difficulty Settings**

**Purpose:** Basic protection, teach bypasses

**Configuration:**
1. **Security → Settings → Security Level:** Low
2. **Security → WAF:** Enable Basic Managed Rules
   - OWASP Core Ruleset: OFF (save for Hard)
   - Cloudflare Managed Ruleset: ON
3. **Security → Settings → Browser Integrity Check:** ON
4. **Security → Bots:** Bot Fight Mode: OFF
5. **Speed:** Developer Mode: OFF

**Firewall Rules:**
```
Rule Name: Medium Difficulty Challenge
Expression: (cf.threat_score gt 75 and http.request.uri.path contains "/vulnerable/")
Action: Managed Challenge
```

---

### **Hard: Hard Difficulty Settings**

**Purpose:** Strong protection, advanced bypass required

**Configuration:**
1. **Security → Settings → Security Level:** High
2. **Security → WAF:** Enable Full Managed Rules
   - OWASP Core Ruleset: ON
   - Cloudflare Managed Ruleset: ON
   - Cloudflare Specials: ON
3. **Security → Settings → Browser Integrity Check:** ON
4. **Security → Bots:** Bot Fight Mode: ON
5. **Security → Settings → Challenge Passage:** 1 hour

**Firewall Rules:**
```
Rule Name: Hard Difficulty - Block High Threats
Expression: (cf.threat_score gt 50 and http.request.uri.path contains "/vulnerable/")
Action: Managed Challenge

Rule Name: Hard Difficulty - Rate Limit
Expression: (http.request.uri.path contains "/vulnerable/user-login")
Rate: 5 requests per minute per IP
Action: Challenge
```

---

### **Impossible: Impossible Difficulty Settings**

**Purpose:** Maximum protection

**Configuration:**
1. **Quick Actions → I'm Under Attack Mode:** ON
   - This enables strongest protection
2. **Security → Settings → Security Level:** I'm Under Attack
3. **Security → WAF:** All rules enabled + Custom rules
4. **Security → Settings → Browser Integrity Check:** ON
5. **Security → Bots:** Bot Fight Mode: ON
6. **Security → Settings → Challenge Passage:** 5 minutes

**Firewall Rules:**
```
Rule Name: Impossible - Block Any Threat
Expression: (cf.threat_score gt 25 and http.request.uri.path contains "/vulnerable/")
Action: Block

Rule Name: Impossible - Aggressive Rate Limit
Expression: (http.request.uri.path contains "/vulnerable/")
Rate: 3 requests per minute per IP
Action: Block
```

---

## **Implementation in Code**

### **Files Created:**

1. **cloudflare-config.php** - Main integration class
   - Detects Cloudflare presence
   - Reads CF headers (threat score, IP, country)
   - Validates requests per difficulty
   - Displays debug panel
   - Logs events

2. **user-login-sqli.php** - Updated with CF integration
   - Checks Cloudflare validation
   - Falls back to simulated WAF if local
   - Shows Cloudflare debug panel (Low difficulty only)

### **Cloudflare Headers Available:**

```php
$_SERVER['HTTP_CF_RAY']              // Unique request ID
$_SERVER['HTTP_CF_THREAT_SCORE']     // 0-100 threat score
$_SERVER['HTTP_CF_CONNECTING_IP']    // Real visitor IP
$_SERVER['HTTP_CF_IPCOUNTRY']        // Visitor country
$_SERVER['HTTP_CF_VISITOR']          // HTTP or HTTPS
$_COOKIE['cf_clearance']             // Challenge passed
$_COOKIE['__cf_bm']                  // Bot management
```

---

## **Testing the Integration**

### **Test 1: Verify Cloudflare is Active**

**Local (without Cloudflare):**
```
Visit: http://localhost/Medview/vulnerable/user-login-sqli.php
Expected: "Behind Cloudflare: No (Local)"
```

**Production (with Cloudflare):**
```
Visit: https://medview.com/vulnerable/user-login-sqli.php
Expected: "Behind Cloudflare: Yes"
Shows: Ray ID, Threat Score, Real IP, Country
```

### **Test 2: Low Difficulty (Should Allow Attack)**

1. Select difficulty: Low: Low
2. Try SQL injection: `admin' OR '1'='1' --`
3. Expected: Login succeeds, flag captured
4. Debug panel shows: "Low difficulty - all requests allowed"

### **Test 3: Medium Difficulty (Bypass Required)**

1. Set Cloudflare Security Level: Low
2. Select difficulty: Medium: Medium
3. Try SQL injection: `admin' OR '1'='1' --`
4. If threat score < 75: Allowed
5. If threat score >= 75: Blocked with challenge

### **Test 4: Hard Difficulty (Advanced Required)**

1. Set Cloudflare Security Level: High
2. Enable OWASP rules
3. Select difficulty: Hard: Hard
4. Try SQL injection: Usually blocked by WAF
5. Need: Bypass techniques, encoding, etc.

### **Test 5: Impossible Difficulty**

1. Enable "I'm Under Attack" mode
2. Select difficulty: Impossible: Impossible
3. Try any attack: Should be blocked
4. Need: Advanced bypass (nearly impossible)

---

## **Troubleshooting**

### **Issue: "Not Behind Cloudflare"**

**Solution:**
- Check DNS propagation: https://dnschecker.org
- Verify nameservers point to Cloudflare
- Ensure proxy (orange cloud) is enabled
- Wait up to 48 hours for DNS propagation

### **Issue: All Attacks Blocked (Even on Low)**

**Solution:**
- Enable Developer Mode in Cloudflare
- Set Security Level to "Essentially Off"
- Disable all WAF rules temporarily
- Check firewall rules aren't blocking

### **Issue: No Attacks Blocked (Even on Hard)**

**Solution:**
- Disable Developer Mode
- Set Security Level to "High"
- Enable WAF Managed Rules
- Enable OWASP rules
- Check firewall rules are active

### **Issue: Cloudflare Challenge Loop**

**Solution:**
- Clear browser cookies
- Disable aggressive rate limiting
- Increase Challenge Passage time
- Whitelist training IPs

---

## **Monitoring & Analytics**

### **Cloudflare Analytics (Free Tier):**

**Available Metrics:**
- Total requests
- Unique visitors
- Threats mitigated
- Bandwidth saved
- Top countries
- Top threats

**Access:**
1. Cloudflare Dashboard
2. Analytics & Logs tab
3. Traffic, Security, Performance tabs

### **Application Logging:**

The integration logs to: `vulnerable/logs/cloudflare.log`

**Log Format:**
```json
{
  "cloudflare": {
    "behind_cloudflare": true,
    "ray_id": "abc123",
    "threat_score": 45,
    "real_ip": "1.2.3.4",
    "country": "US"
  },
  "event": "waf_block",
  "details": {
    "username": "admin' OR '1'='1",
    "difficulty": "hard",
    "threat_score": 85
  },
  "timestamp": "2026-01-01 18:00:00"
}
```

---

## **Teaching Cloudflare Bypass Techniques**

### **Low → Medium (Beginner Techniques):**

1. **Cookie Manipulation**
   ```
   Set cookie: difficulty=low
   Cloudflare may cache based on cookies
   ```

2. **User-Agent Spoofing**
   ```
   Change User-Agent to Googlebot, etc.
   Some bypasses possible
   ```

3. **Request Throttling**
   ```
   Slow down requests to avoid rate limiting
   ```

### **Medium → Hard (Intermediate):**

1. **Cloudflare Solver Tools**
   ```
   - cloudscraper (Python)
   - undetected-chromedriver
   - FlareSolverr
   ```

2. **Browser Automation**
   ```
   Use Selenium/Puppeteer to solve challenges
   ```

3. **API Endpoint Discovery**
   ```
   Find endpoints that bypass WAF
   ```

### **Hard → Impossible (Advanced):**

1. **Origin IP Discovery**
   ```
   - Historical DNS records
   - SSL certificates
   - Subdomain scanning
   - Direct IP access (if found)
   ```

2. **Request Smuggling**
   ```
   - HTTP/2 smuggling
   - Header manipulation
   ```

3. **Zero-Day Techniques**
   ```
   - CF bypass exploits
   - Research current bypasses
   ```

---

## **Best Practices**

### **For Training Environment:**

**Use separate subdomain** for vulnerable version
**Whitelist training lab IPs** in firewall rules
**Enable logging** for student activity tracking
**Start on Low**, progress through difficulties
**Document bypass techniques** students discover
**Rotate difficulty settings** as students progress

### **Security Considerations:**

⚠️ **Never expose** vulnerable version publicly long-term
⚠️ **Use strong passwords** for Cloudflare account
⚠️ **Enable 2FA** on Cloudflare account
⚠️ **Monitor logs** for suspicious activity
⚠️ **Set IP allowlist** to restrict access
⚠️ **Regular backups** of configuration

---

## 📚 **Additional Resources**

**Cloudflare Documentation:**
- WAF Rules: https://developers.cloudflare.com/waf/
- Firewall Rules: https://developers.cloudflare.com/firewall/
- Security Settings: https://developers.cloudflare.com/ssl/

**Bypass Research:**
- https://github.com/topics/cloudflare-bypass
- Bug bounty reports on HackerOne
- Security research papers

---

## **Quick Setup Checklist**

- [ ] Domain added to Cloudflare
- [ ] Nameservers updated
- [ ] DNS propagation complete
- [ ] Proxy (orange cloud) enabled
- [ ] SSL/TLS configured
- [ ] Security Level adjusted per difficulty
- [ ] WAF rules configured
- [ ] Firewall rules created (optional)
- [ ] Application code updated
- [ ] Testing completed
- [ ] Logging verified
- [ ] Training materials prepared

---

## **You're Ready!**

Your vulnerable HMS now has:
Real Cloudflare WAF integration
Progressive difficulty with real protection
Educational debug panels
Comprehensive logging
Industry-standard WAF experience

**Start training and teach real-world bypass techniques!** 🚀

---

**Questions or Issues?**
Check troubleshooting section or Cloudflare documentation.

**Last Updated:** 2026-01-01
