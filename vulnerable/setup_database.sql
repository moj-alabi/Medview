-- Medview+ VULNERABLE VERSION Database Setup
-- WARNING: FOR TRAINING PURPOSES ONLY

-- Create database
CREATE DATABASE IF NOT EXISTS medview_vulnerable;
USE medview_vulnerable;

-- Users table (patients)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    gender VARCHAR(20),
    balance DECIMAL(10,2) DEFAULT 1000.00,
    difficulty_level VARCHAR(20) DEFAULT 'low',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE IF NOT EXISTS admini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors table
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctorName VARCHAR(255) NOT NULL,
    docEmail VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    specialization VARCHAR(255),
    docFees VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctorId INT,
    userId INT,
    appointmentDate DATE,
    appointmentTime TIME,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctorId) REFERENCES doctors(id),
    FOREIGN KEY (userId) REFERENCES users(id)
);

-- Insert test data with VULNERABLE password storage
-- Passwords stored in plain MD5 (INSECURE!)

INSERT INTO users (fullname, email, password, address, city, gender) VALUES
('Ali Sheikh', 'ali@sheflabs.com', MD5('Password123'), '123 Security Lane', 'Dubai', 'Male'),
('John Doe', 'john@test.com', MD5('password123'), '123 Main St', 'New York', 'Male'),
('Jane Smith', 'jane@test.com', MD5('password123'), '456 Oak Ave', 'Los Angeles', 'Female'),
('Bob Johnson', 'bob@test.com', MD5('test123'), '789 Pine Rd', 'Chicago', 'Male'),
('Alice Williams <script>alert("XSS")</script>', 'alice@test.com', MD5('pass123'), '321 Elm St', 'Houston', 'Female');

INSERT INTO admini (username, password, email) VALUES
('admin', MD5('admin123'), 'admin@medview.com'),
('superadmin', MD5('super123'), 'superadmin@medview.com');

INSERT INTO doctors (doctorName, docEmail, password, specialization, docFees) VALUES
('Dr. Sarah Connor', 'sarah@medview.com', MD5('doctor123'), 'Cardiology', '150'),
('Dr. Michael Brown', 'michael@medview.com', MD5('doctor123'), 'Neurology', '200'),
('Dr. Emily Davis', 'emily@medview.com', MD5('doctor123'), 'Pediatrics', '120');

-- User log table
CREATE TABLE IF NOT EXISTS userlog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid INT,
    username VARCHAR(255),
    userip VARCHAR(50),
    status INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctor log table
CREATE TABLE IF NOT EXISTS doctorslog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid INT,
    username VARCHAR(255),
    userip VARCHAR(50),
    status INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Verify table for OTP (even though we won't use it in vulnerable version)
CREATE TABLE IF NOT EXISTS verify (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code INT,
    expires INT,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payments table for Mass Assignment vulnerability
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    appointment_id INT,
    amount DECIMAL(10,2),
    discount DECIMAL(10,2) DEFAULT 0,
    is_premium TINYINT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

-- Medications/Services table
CREATE TABLE IF NOT EXISTS medications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 100,
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exploit flags and tracking
CREATE TABLE IF NOT EXISTS exploit_flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vulnerability_name VARCHAR(255) NOT NULL,
    flag_code VARCHAR(255) NOT NULL UNIQUE,
    reward_amount DECIMAL(10,2) DEFAULT 500.00,
    description TEXT,
    difficulty VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User exploits tracking (CTF scoreboard)
CREATE TABLE IF NOT EXISTS user_exploits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flag_id INT,
    exploited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (flag_id) REFERENCES exploit_flags(id),
    UNIQUE KEY unique_exploit (user_id, flag_id)
);

-- Insert medications
INSERT INTO medications (name, description, price, category) VALUES
('Paracetamol 500mg', 'Pain relief and fever reducer', 50.00, 'Pain Relief'),
('Amoxicillin 250mg', 'Antibiotic for bacterial infections', 150.00, 'Antibiotics'),
('Ibuprofen 400mg', 'Anti-inflammatory pain relief', 75.00, 'Pain Relief'),
('Vitamin C 1000mg', 'Immune system support', 100.00, 'Supplements'),
('Blood Pressure Monitor', 'Digital BP monitoring device', 5000.00, 'Medical Equipment'),
('First Aid Kit', 'Complete emergency first aid kit', 2500.00, 'Medical Equipment');

-- Insert vulnerability flags
INSERT INTO exploit_flags (vulnerability_name, flag_code, reward_amount, description, difficulty) VALUES
('SQL Injection - Login Bypass', 'FLAG{SQL_1nj3ct10n_M4st3r_2025}', 500.00, 'Bypassed login authentication using SQL injection', 'Easy'),
('XSS - Reflected', 'FLAG{XSS_R3fl3ct3d_H4ck3r}', 500.00, 'Executed reflected XSS attack', 'Easy'),
('XSS - Stored', 'FLAG{XSS_St0r3d_P3rs1st3nt}', 500.00, 'Stored XSS in database', 'Medium'),
('IDOR - View Other Users Data', 'FLAG{1D0R_Br0k3n_4cc3ss}', 500.00, 'Accessed other users data via IDOR', 'Easy'),
('Mass Assignment', 'FLAG{M4ss_4ss1gn_Pr1v_3sc}', 500.00, 'Exploited mass assignment vulnerability', 'Medium'),
('Directory Traversal', 'FLAG{D1r_Tr4v3rs4l_F1l3_R34d}', 500.00, 'Read system files via path traversal', 'Medium'),
('Command Injection', 'FLAG{CMD_1nj3ct_RC3_G0d}', 500.00, 'Executed system commands', 'Hard'),
('File Upload RCE', 'FLAG{F1l3_Upl04d_W3b5h3ll}', 500.00, 'Uploaded malicious file', 'Hard'),
('SSRF - Internal Access', 'FLAG{SSRF_1nt3rn4l_N3tw0rk}', 500.00, 'Accessed internal network via SSRF', 'Hard'),
('Business Logic - Negative Price', 'FLAG{B1z_L0g1c_Fr4ud_M4st3r}', 500.00, 'Exploited negative pricing', 'Medium'),
('Race Condition', 'FLAG{R4c3_C0nd1t10n_T1m1ng}', 500.00, 'Won race condition attack', 'Hard'),
('JWT - None Algorithm', 'FLAG{JWT_N0n3_4lg_Byp4ss}', 500.00, 'Bypassed JWT with none algorithm', 'Hard'),
('SSTI - Template Injection', 'FLAG{SSTI_T3mpl4t3_RC3}', 500.00, 'Executed code via SSTI', 'Expert'),
('Insecure Deserialization', 'FLAG{D3s3r14l1z3_RC3_M4g1c}', 500.00, 'Exploited PHP unserialize', 'Expert'),
('Open Redirect', 'FLAG{0p3n_R3d1r3ct_Ph1sh}', 500.00, 'Created phishing link', 'Easy'),
('API - BOLA', 'FLAG{4P1_B0L4_D4t4_L34k}', 500.00, 'Accessed unauthorized API data', 'Medium'),
('CORS Misconfiguration', 'FLAG{C0RS_Cr0ss_0r1g1n_L34k}', 500.00, 'Exploited CORS misconfiguration', 'Medium'),
('XXE - File Read', 'FLAG{XXE_F1l3_R34d_XML}', 500.00, 'Read files via XXE', 'Hard'),
('ReDoS', 'FLAG{R3D0S_R3g3x_D0S_4tt4ck}', 500.00, 'Caused ReDoS attack', 'Hard'),
('HTTP Response Splitting', 'FLAG{HTTP_R3sp_Spl1t_C4ch3}', 500.00, 'Performed response splitting', 'Hard');

-- Display success message
SELECT 'Database setup complete!' AS Message;
SELECT 'Test Credentials:' AS Info;
SELECT 'User: ali@sheflabs.com / Password123' AS UserLogin;
SELECT 'User: john@test.com / password123' AS AlternateLogin;
SELECT 'Admin: admin / admin123' AS AdminLogin;
SELECT 'Doctor: sarah@medview.com / doctor123' AS DoctorLogin;
SELECT '⚠️  All passwords stored as MD5 (VULNERABLE!)' AS Warning;
