<?php
// GAMIFICATION SYSTEM - CTF-style exploit rewards
// Tracks exploits and rewards users with money + flags

function award_exploit($con, $user_id, $vulnerability_name, $ip_address = null) {
    if(!$ip_address) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    
    // Get flag details
    $query = "SELECT * FROM exploit_flags WHERE vulnerability_name='".mysqli_real_escape_string($con, $vulnerability_name)."'";
    $result = mysqli_query($con, $query);
    
    if($result && mysqli_num_rows($result) > 0) {
        $flag = mysqli_fetch_array($result);
        
        // Check if already exploited by this user
        $check = "SELECT * FROM user_exploits WHERE user_id='".$user_id."' AND flag_id='".$flag['id']."'";
        $existing = mysqli_query($con, $check);
        
        if(mysqli_num_rows($existing) == 0) {
            // First time exploiting this vulnerability
            
            // Award money
            $update_balance = "UPDATE users SET balance = balance + ".$flag['reward_amount']." WHERE id='".$user_id."'";
            mysqli_query($con, $update_balance);
            
            // Record exploit
            $insert_exploit = "INSERT INTO user_exploits (user_id, flag_id, ip_address) 
                              VALUES ('".$user_id."', '".$flag['id']."', '".$ip_address."')";
            mysqli_query($con, $insert_exploit);
            
            return array(
                'success' => true,
                'first_time' => true,
                'flag' => $flag['flag_code'],
                'reward' => $flag['reward_amount'],
                'vulnerability' => $flag['vulnerability_name'],
                'difficulty' => $flag['difficulty'],
                'description' => $flag['description']
            );
        } else {
            // Already exploited
            return array(
                'success' => true,
                'first_time' => false,
                'flag' => $flag['flag_code'],
                'message' => 'You already exploited this vulnerability!'
            );
        }
    }
    
    return array('success' => false, 'message' => 'Flag not found');
}

function display_exploit_notification($exploit_result) {
    if(!$exploit_result || !$exploit_result['success']) return '';
    
    if($exploit_result['first_time']) {
        $difficulty_colors = [
            'Easy' => 'success',
            'Medium' => 'warning', 
            'Hard' => 'danger',
            'Expert' => 'dark'
        ];
        $color = $difficulty_colors[$exploit_result['difficulty']] ?? 'info';
        
        return '
        <div class="alert alert-'.$color.' alert-dismissible fade show exploit-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 400px; animation: slideIn 0.5s;">
            <h4 class="alert-heading">🎉 VULNERABILITY EXPLOITED!</h4>
            <hr>
            <p><strong>Vulnerability:</strong> '.$exploit_result['vulnerability'].'</p>
            <p><strong>Difficulty:</strong> <span class="badge badge-'.$color.'">'.$exploit_result['difficulty'].'</span></p>
            <p><strong>Reward:</strong> ₦'.number_format($exploit_result['reward'], 2).' NGN</p>
            <hr>
            <p class="mb-0"><strong>🚩 FLAG:</strong></p>
            <code style="background: #000; color: #0f0; padding: 10px; display: block; font-size: 14px;">'.$exploit_result['flag'].'</code>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <style>
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        </style>
        ';
    } else {
        return '
        <div class="alert alert-info alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <strong>Already Exploited!</strong><br>
            Flag: <code>'.$exploit_result['flag'].'</code>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>';
    }
}

function get_user_stats($con, $user_id) {
    // Get user's exploit count and total earnings
    $query = "SELECT COUNT(*) as exploit_count, SUM(ef.reward_amount) as total_earned
              FROM user_exploits ue
              JOIN exploit_flags ef ON ue.flag_id = ef.id
              WHERE ue.user_id = '".$user_id."'";
    $result = mysqli_query($con, $query);
    $stats = mysqli_fetch_array($result);
    
    // Get current balance
    $balance_query = "SELECT balance FROM users WHERE id='".$user_id."'";
    $balance_result = mysqli_query($con, $balance_query);
    $balance_row = mysqli_fetch_array($balance_result);
    
    return array(
        'exploit_count' => $stats['exploit_count'] ?? 0,
        'total_earned' => $stats['total_earned'] ?? 0,
        'current_balance' => $balance_row['balance'] ?? 0
    );
}

function get_scoreboard($con, $limit = 10) {
    $query = "SELECT u.fullname, u.email, COUNT(ue.id) as exploits, SUM(ef.reward_amount) as total_earned
              FROM users u
              LEFT JOIN user_exploits ue ON u.id = ue.user_id
              LEFT JOIN exploit_flags ef ON ue.flag_id = ef.id
              GROUP BY u.id
              ORDER BY exploits DESC, total_earned DESC
              LIMIT ".$limit;
    return mysqli_query($con, $query);
}
?>
