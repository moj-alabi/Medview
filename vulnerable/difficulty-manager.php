<?php
// DIFFICULTY LEVEL SYSTEM - Progressive Learning
// Like DVWA but for HMS

class DifficultyManager {
    
    public static function getDifficultyLevels() {
        return [
            'low' => [
                'name' => 'Low',
                'color' => 'success',
                'icon' => '🟢',
                'description' => 'Maximum vulnerabilities, obvious hints, easy exploitation',
                'sql_protection' => false,
                'xss_protection' => false,
                'csrf_protection' => false,
                'input_validation' => false,
                'error_messages' => 'detailed',
                'hints_visible' => true,
                'waf' => false
            ],
            'medium' => [
                'name' => 'Medium',
                'color' => 'warning',
                'icon' => '🟡',
                'description' => 'Some basic protections, requires bypass techniques',
                'sql_protection' => 'basic', // mysqli_real_escape_string
                'xss_protection' => 'basic', // htmlspecialchars sometimes
                'csrf_protection' => false,
                'input_validation' => 'basic',
                'error_messages' => 'generic',
                'hints_visible' => false,
                'waf' => 'basic'
            ],
            'hard' => [
                'name' => 'Hard',
                'color' => 'danger',
                'icon' => '🔴',
                'description' => 'Good security practices, advanced exploitation required',
                'sql_protection' => 'prepared_statements',
                'xss_protection' => 'full',
                'csrf_protection' => true,
                'input_validation' => 'strict',
                'error_messages' => 'none',
                'hints_visible' => false,
                'waf' => 'advanced'
            ],
            'impossible' => [
                'name' => 'Impossible',
                'color' => 'dark',
                'icon' => '⚫',
                'description' => 'Production-level security, nearly impossible to exploit',
                'sql_protection' => 'prepared_statements',
                'xss_protection' => 'full',
                'csrf_protection' => true,
                'input_validation' => 'strict',
                'error_messages' => 'none',
                'hints_visible' => false,
                'waf' => 'advanced',
                'rate_limiting' => true,
                'captcha' => true,
                '2fa' => true
            ]
        ];
    }
    
    public static function getCurrentDifficulty($user) {
        return isset($user['difficulty_level']) ? $user['difficulty_level'] : 'low';
    }
    
    public static function getDifficultyConfig($level) {
        $levels = self::getDifficultyLevels();
        return isset($levels[$level]) ? $levels[$level] : $levels['low'];
    }
    
    // SQL Injection protection based on difficulty
    public static function sanitizeSQL($input, $con, $difficulty) {
        $config = self::getDifficultyConfig($difficulty);
        
        if($config['sql_protection'] === false) {
            // Beginner: No protection
            return $input;
        } elseif($config['sql_protection'] === 'basic') {
            // Intermediate: Basic escaping (still bypassable)
            return mysqli_real_escape_string($con, $input);
        } else {
            // Advanced/Expert: Would use prepared statements (handled elsewhere)
            return mysqli_real_escape_string($con, $input);
        }
    }
    
    // XSS protection based on difficulty
    public static function sanitizeOutput($output, $difficulty) {
        $config = self::getDifficultyConfig($difficulty);
        
        if($config['xss_protection'] === false) {
            // Beginner: No protection
            return $output;
        } elseif($config['xss_protection'] === 'basic') {
            // Intermediate: Basic encoding (sometimes)
            // Randomly apply protection (50% chance) to simulate inconsistent security
            return (rand(0,1) == 0) ? $output : htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
        } else {
            // Advanced/Expert: Full protection
            return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Check if SQL injection detected
    public static function detectSQLInjection($input, $difficulty) {
        $config = self::getDifficultyConfig($difficulty);
        
        if($config['waf'] === 'advanced') {
            // Advanced WAF blocks common patterns
            $patterns = [
                "/'.*or.*'/i",
                "/union.*select/i",
                "/--/",
                "/#/",
                "/\/\*/",
                "/;.*drop/i",
                "/;.*delete/i",
                "/;.*update/i"
            ];
            
            foreach($patterns as $pattern) {
                if(preg_match($pattern, $input)) {
                    return "WAF_BLOCKED";
                }
            }
        } elseif($config['waf'] === 'basic') {
            // Basic WAF blocks obvious patterns only
            if(preg_match("/(union.*select|;.*drop)/i", $input)) {
                return "WAF_BLOCKED";
            }
        }
        
        return false;
    }
    
    // Error message based on difficulty
    public static function getErrorMessage($difficulty, $type = 'login') {
        $config = self::getDifficultyConfig($difficulty);
        
        if($config['error_messages'] === 'detailed') {
            // Beginner: Detailed errors (information disclosure)
            if($type === 'login') {
                return "Invalid credentials. SQL Query failed or user not found.";
            }
            return "Error: Detailed technical message here";
        } elseif($config['error_messages'] === 'generic') {
            // Intermediate: Generic but still informative
            if($type === 'login') {
                return "Login failed. Please try again.";
            }
            return "An error occurred.";
        } else {
            // Advanced/Expert: No detailed errors
            return "Access denied.";
        }
    }
    
    // Get hint visibility
    public static function showHints($difficulty) {
        $config = self::getDifficultyConfig($difficulty);
        return $config['hints_visible'];
    }
    
    // Update user difficulty
    public static function updateDifficulty($con, $user_id, $new_difficulty) {
        $levels = self::getDifficultyLevels();
        if(!isset($levels[$new_difficulty])) {
            return false;
        }
        
        $query = "UPDATE users SET difficulty_level='".mysqli_real_escape_string($con, $new_difficulty)."' WHERE id='".$user_id."'";
        return mysqli_query($con, $query);
    }
}
?>
