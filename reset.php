<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Include the database connection
require 'connect.php'; // Ensure this file creates the $conn object
include 'rateLimit.php'; 
include 'inputValidation.php';

// Function to get the reset token from the database
function getPasswordResetToken($token) {
    global $conn; // Declare $conn as global
    $query = "SELECT * FROM passwordresettokens WHERE token = ? AND isVerified = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get the user by ID
function getUserById($userId) {
    global $conn; // Declare $conn as global
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to update the user's password
function updateUserPassword($userId, $newPassword) {
    global $conn; // Declare $conn as global
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$hashedPassword, $userId]);
}

// Handle the password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkRateLimit($conn, "reset")) {
        error_log("RESET: Rate limit exceeded for Client Key.");
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Too many attempts. Please try again after an hour.", ENT_QUOTES, 'UTF-8'),
            "captcha_required" => true
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(429);
        exit();
    }
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? null;
    $newPassword = $input['newPassword'] ?? null;

    // Validate required fields
    if (!validate_required_fields([$token, $newPassword])) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("All fields are required.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }

    // Retrieve the reset token from the database
    $resetToken = getPasswordResetToken($token);
    if (!$resetToken || strtotime($resetToken['expiration']) < time()) {
        echo json_encode([
            "success" => false, 
            'message' => htmlspecialchars('Invalid or expired token.', ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit;
    }

    // Retrieve the user associated with the token
    $user = getUserById($resetToken['user_id']);
    if (!$user) {
        echo json_encode([
            "success" => false, 
            'message' => htmlspecialchars('User not found.', ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit;
    }

    // Update the user's password
    if (updateUserPassword($user['id'], $newPassword)) {
        echo json_encode([
            "success" => true, 
            'message' => htmlspecialchars('Password has been reset.', ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        echo json_encode([
            "success" => false, 
            'message' => htmlspecialchars('Failed to reset password.', ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    $stmt = $conn->prepare("UPDATE passwordresettokens SET isVerified = 1, token = NULL WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    exit;
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
