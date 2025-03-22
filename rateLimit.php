<?php
function checkRateLimit($conn, $action, $limits = []) {
    // Define rate limits for different actions
    $defaultLimits = [
        "signup" => ["limit" => 5, "window" => 3600],  // 5 attempts per hour  
        "request" => ["limit" => 20, "window" => 900], // 20 requests per 15 mins
    ];
    
    // Use provided limits or fallback to defaults
    $limit = $limits[$action]["limit"] ?? $defaultLimits[$action]["limit"];
    $window = $limits[$action]["window"] ?? $defaultLimits[$action]["window"];

    // Ensure client_id is set
    if (!isset($_COOKIE['client_id'])) {
        $clientId = bin2hex(random_bytes(16));
        setcookie('client_id', $clientId, time() + (86400 * 30), "/", "enval.in", true, true);
        $_COOKIE['client_id'] = $clientId;
    }

    // Unique client key per action type
    $clientKey = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_COOKIE['client_id'] . $action);
    
    $now = date('Y-m-d H:i:s');
    $expires = date('Y-m-d H:i:s', time() + $window);
    
    $conn->begin_transaction();
    
    try {
        $cleanupStmt = $conn->prepare("DELETE FROM ratelimits WHERE expires_at < ?");
        $cleanupStmt->bind_param("s", $now);
        $cleanupStmt->execute();
        
        $countStmt = $conn->prepare("SELECT count FROM ratelimits WHERE client_key = ?");
        $countStmt->bind_param("s", $clientKey);
        $countStmt->execute();
        $result = $countStmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['count'] >= $limit) {
                $conn->commit();
                return false; // Rate limit exceeded
            }
            $updateStmt = $conn->prepare("UPDATE ratelimits SET count = count + 1 WHERE client_key = ?");
            $updateStmt->bind_param("s", $clientKey);
            $updateStmt->execute();
        } else {
            $insertStmt = $conn->prepare("INSERT INTO ratelimits (client_key, count, expires_at) VALUES (?, 1, ?)");
            $insertStmt->bind_param("ss", $clientKey, $expires);
            $insertStmt->execute();
        }
        
        $conn->commit();
        return true; 
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Rate limit error: " . $e->getMessage());
        return true;
    }
}
?>