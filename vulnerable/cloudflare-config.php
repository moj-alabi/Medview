<?php
// CLOUDFLARE WAF INTEGRATION
// Integrates Cloudflare Free tier with difficulty levels

class CloudflareWAF {
    
    /**
     * Check if request is coming through Cloudflare
     */
    public static function isCloudflareRequest() {
        return isset($_SERVER['HTTP_CF_RAY']);
    }
    
    /**
     * Get Cloudflare threat score (0-100)
     * 0 = No threat, 100 = Definite threat
     */
    public static function getThreatScore() {
        return isset($_SERVER['HTTP_CF_THREAT_SCORE']) ? 
               (int)$_SERVER['HTTP_CF_THREAT_SCORE'] : 0;
    }
    
    /**
     * Get real visitor IP (behind Cloudflare proxy)
     */
    public static function getRealIP() {
        return $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
    }
    
    /**
     * Get visitor's country code
     */
    public static function getCountry() {
        return $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'Unknown';
    }
    
    /**
     * Check if Cloudflare blocked/challenged the request
     */
    public static function wasBlocked() {
        // Check for Cloudflare challenge cookies
        return isset($_COOKIE['__cf_bm']) || isset($_COOKIE['cf_clearance']);
    }
    
    /**
     * Validate request based on difficulty level
     */
    public static function validateRequest($difficulty) {
        if(!self::isCloudflareRequest()) {
            // Not behind Cloudflare - use fallback
            return self::fallbackValidation($difficulty);
        }
        
        $threat_score = self::getThreatScore();
        $config = DifficultyManager::getDifficultyConfig($difficulty);
        
        // Low: Allow everything (bypass Cloudflare checks)
        if($difficulty === 'low') {
            return [
                'allowed' => true,
                'reason' => 'Low difficulty - all requests allowed',
                'bypass_waf' => true
            ];
        }
        
        // Medium: Block high threats only (75+)
        if($difficulty === 'medium') {
            if($threat_score >= 75) {
                return [
                    'allowed' => false,
                    'reason' => 'Medium difficulty - High threat score detected',
                    'threat_score' => $threat_score,
                    'challenge_required' => true
                ];
            }
            return ['allowed' => true, 'reason' => 'Threat score acceptable'];
        }
        
        // Hard: Block medium threats (50+)
        if($difficulty === 'hard') {
            if($threat_score >= 50) {
                return [
                    'allowed' => false,
                    'reason' => 'Hard difficulty - Medium/High threat detected',
                    'threat_score' => $threat_score,
                    'challenge_required' => true
                ];
            }
            return ['allowed' => true, 'reason' => 'Threat score acceptable'];
        }
        
        // Impossible: Block low threats (25+)
        if($difficulty === 'impossible') {
            if($threat_score >= 25) {
                return [
                    'allowed' => false,
                    'reason' => 'Impossible difficulty - Any suspicious activity blocked',
                    'threat_score' => $threat_score,
                    'challenge_required' => true
                ];
            }
            
            // Additional checks for Impossible
            if(!self::passedBrowserCheck()) {
                return [
                    'allowed' => false,
                    'reason' => 'Failed browser integrity check',
                    'challenge_required' => true
                ];
            }
            
            return ['allowed' => true, 'reason' => 'All checks passed'];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Check if browser integrity check was passed
     */
    public static function passedBrowserCheck() {
        // Check for Cloudflare browser challenge cookies
        return isset($_COOKIE['cf_clearance']);
    }
    
    /**
     * Fallback validation when not behind Cloudflare (local testing)
     */
    public static function fallbackValidation($difficulty) {
        // Use simulated WAF from DifficultyManager
        return [
            'allowed' => true,
            'reason' => 'Not behind Cloudflare - using local validation',
            'is_local' => true
        ];
    }
    
    /**
     * Get comprehensive Cloudflare information
     */
    public static function getInfo() {
        return [
            'behind_cloudflare' => self::isCloudflareRequest(),
            'ray_id' => $_SERVER['HTTP_CF_RAY'] ?? 'N/A',
            'threat_score' => self::getThreatScore(),
            'real_ip' => self::getRealIP(),
            'country' => self::getCountry(),
            'visitor_scheme' => $_SERVER['HTTP_CF_VISITOR'] ?? 'unknown',
            'browser_check_passed' => self::passedBrowserCheck(),
            'was_challenged' => self::wasBlocked()
        ];
    }
    
    /**
     * Display Cloudflare debug panel (for educational purposes)
     */
    public static function displayDebugPanel($difficulty) {
        $info = self::getInfo();
        $validation = self::validateRequest($difficulty);
        
        $html = '
        <div class="card mt-3 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">CLOUD: Cloudflare WAF Status</h6>
            </div>
            <div class="card-body small">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <td><strong>Behind Cloudflare:</strong></td>
                        <td><span class="badge badge-'.($info['behind_cloudflare'] ? 'success' : 'secondary').'">'.
                            ($info['behind_cloudflare'] ? 'Yes' : 'No (Local)').'</span></td>
                    </tr>';
        
        if($info['behind_cloudflare']) {
            $html .= '
                    <tr>
                        <td><strong>Ray ID:</strong></td>
                        <td><code>'.$info['ray_id'].'</code></td>
                    </tr>
                    <tr>
                        <td><strong>Threat Score:</strong></td>
                        <td><span class="badge badge-'.self::getThreatBadge($info['threat_score']).'">'.$info['threat_score'].'/100</span></td>
                    </tr>
                    <tr>
                        <td><strong>Your IP:</strong></td>
                        <td>'.$info['real_ip'].'</td>
                    </tr>
                    <tr>
                        <td><strong>Country:</strong></td>
                        <td>'.$info['country'].'</td>
                    </tr>
                    <tr>
                        <td><strong>Browser Check:</strong></td>
                        <td><span class="badge badge-'.($info['browser_check_passed'] ? 'success' : 'warning').'">'.
                            ($info['browser_check_passed'] ? 'Passed' : 'Not Yet').'</span></td>
                    </tr>';
        }
        
        $html .= '
                    <tr>
                        <td><strong>Request Status:</strong></td>
                        <td><span class="badge badge-'.($validation['allowed'] ? 'success' : 'danger').'">'.
                            ($validation['allowed'] ? 'ALLOWED' : 'BLOCKED').'</span></td>
                    </tr>
                    <tr>
                        <td><strong>Reason:</strong></td>
                        <td>'.$validation['reason'].'</td>
                    </tr>
                </table>
            </div>
        </div>';
        
        return $html;
    }
    
    /**
     * Get badge color based on threat score
     */
    private static function getThreatBadge($score) {
        if($score < 25) return 'success';
        if($score < 50) return 'info';
        if($score < 75) return 'warning';
        return 'danger';
    }
    
    /**
     * Log Cloudflare event (for analytics)
     */
    public static function logEvent($con, $user_id, $event_type, $details) {
        $info = self::getInfo();
        $log_data = json_encode([
            'cloudflare' => $info,
            'event' => $event_type,
            'details' => $details,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        // Store in database (optional - create cf_logs table if needed)
        // For now, just log to file
        $log_file = __DIR__ . '/logs/cloudflare.log';
        if(!file_exists(dirname($log_file))) {
            mkdir(dirname($log_file), 0777, true);
        }
        file_put_contents($log_file, $log_data . "\n", FILE_APPEND);
    }
    
    /**
     * Get recommended Cloudflare settings for difficulty
     */
    public static function getRecommendedSettings($difficulty) {
        $settings = [
            'low' => [
                'security_level' => 'Essentially Off',
                'waf' => 'Disabled (or Developer Mode)',
                'challenge_passage' => '30 days',
                'browser_check' => 'Off',
                'rate_limiting' => 'Off',
                'bot_fight' => 'Off',
                'description' => 'Maximum vulnerability exposure for learning'
            ],
            'medium' => [
                'security_level' => 'Low',
                'waf' => 'Managed Rules (Basic)',
                'challenge_passage' => '1 day',
                'browser_check' => 'On',
                'rate_limiting' => 'Off',
                'bot_fight' => 'Off',
                'description' => 'Basic protection, bypass techniques teachable'
            ],
            'hard' => [
                'security_level' => 'High',
                'waf' => 'Managed Rules (Full) + OWASP',
                'challenge_passage' => '1 hour',
                'browser_check' => 'On',
                'rate_limiting' => 'Moderate',
                'bot_fight' => 'On',
                'description' => 'Strong protection, advanced bypass required'
            ],
            'impossible' => [
                'security_level' => 'I\'m Under Attack',
                'waf' => 'Full Rules + Custom',
                'challenge_passage' => '5 minutes',
                'browser_check' => 'On',
                'rate_limiting' => 'Aggressive',
                'bot_fight' => 'On',
                'challenge' => 'Managed Challenge',
                'description' => 'Maximum protection, nearly impossible'
            ]
        ];
        
        return $settings[$difficulty] ?? $settings['low'];
    }
}
?>
