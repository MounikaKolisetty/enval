<?php
// Enable error reporting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Enable CORS
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

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "success" => false, 
        "error" => htmlspecialchars("Method Not Allowed", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}
include 'connect.php';

error_log('Session started, session id: ' . session_id());

function logout() {
    if (isset($_SESSION['SessionId']) && !empty($_SESSION['SessionId'])) {
        error_log('isset($_SESSION["SessionId"]): ' . isset($_SESSION['SessionId']) . ', !empty($_SESSION["SessionId"]): ' . !empty($_SESSION['SessionId']));
        $sessionId = $_SESSION['SessionId'];
        deleteUserSession($sessionId);
        unset($_SESSION['SessionId']);
        echo json_encode([
            "success" => true,
            "message" => htmlspecialchars("Logout successful", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        error_log('isset($_SESSION["SessionId"]): ' . isset($_SESSION['SessionId']) . ', !empty($_SESSION["SessionId"]): ' . !empty($_SESSION['SessionId']));
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Logout unsuccessful", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}

function deleteUserSession($sessionId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM sessions WHERE session_id = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $db->error);
        return;
    }
    $stmt->execute([$sessionId]);
    if ($stmt->error) {
        error_log("Execute failed: " . $stmt->error);
    } else {
        error_log("Session deleted from database.");
    }
}

logout();
