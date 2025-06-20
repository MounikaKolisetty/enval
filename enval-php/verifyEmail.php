<?php
include 'connect.php';
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'www.enval.in',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

$headers = getallheaders();
$csrf_token = $headers['X-CSRF-Token'] ?? ($headers['X-Csrf-Token'] ?? ''); // Case handling

if (empty($csrf_token)) {
    error_log("CSRF Token Missing");
    echo json_encode([
        "success" => false,
        "message" => htmlspecialchars("Invalid CSRF token", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    http_response_code(403);
    exit();
}

session_start();
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    error_log("CSRF Token Mismatch");
    echo json_encode([
        "success" => false,
        "message" => htmlspecialchars("Invalid CSRF token", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    http_response_code(403);
    exit();
}

include 'connect.php';
include 'rateLimit.php'; 

if (!checkRateLimit($conn, "userDetailsToDB")) {
    error_log("USERDETAILSTODB: Rate limit exceeded for Client Key.");
    echo json_encode([
        "success" => false,
        "message" => htmlspecialchars("Too many attempts. Please try again after an hour.", ENT_QUOTES, 'UTF-8'),
        "captcha_required" => true
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    http_response_code(429);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? '';

    if (!$token) {
        echo json_encode([
            "success" => false, 
            "message" => htmlspecialchars("Invalid token.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }

    // Check if token exists in the database
    $stmt = $conn->prepare("SELECT email, expiry FROM users WHERE token = ? AND isVerified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $expiryTime = strtotime($row['expiry']);
        $currentTime = time();

        error_log('verify email' . $expiryTime . $currentTime);

        if ($currentTime > $expiryTime) {
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Expired token.", ENT_QUOTES, 'UTF-8')
            ]);
            exit();
        }
        // Update user as verified
        $stmt = $conn->prepare("UPDATE users SET isVerified = 1, token = NULL WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo json_encode([
            "success" => true, 
            "message" => htmlspecialchars("Email verified successfully.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => htmlspecialchars("Invalid or expired token.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    $stmt->close();
    $conn->close();
}
else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false, 
        "error" => htmlspecialchars("Method Not Allowed", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}
?>
