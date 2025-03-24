<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    error_log("RESET PASSWORD: CSRF Token Missing from IP: " . $_SERVER['REMOTE_ADDR']);
    echo json_encode([
        "success" => false,
        "message" => htmlspecialchars("Invalid CSRF token", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    http_response_code(403);
    exit();
}

session_start();
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    error_log("RESET PASSWORD: CSRF Token Mismatch from IP: " . $_SERVER['REMOTE_ADDR']);
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
require 'rateLimit.php';
require 'inputValidation.php';

// Function to generate a random token
function generateResetToken() {
    return bin2hex(random_bytes(16));
}

// Function to store the token
function storeResetToken($userId, $token, $expiration) {
    global $conn; // Declare $conn as global
    $isVerified = false;
    $query = "INSERT INTO passwordresettokens (user_id, token, expiration, isVerified) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issi", $userId, $token, $expiration, $isVerified);
    $stmt->execute();
    $stmt->close();
}

// Function to get user by email (MODIFIED)
function getUserByEmail($email) {
    global $conn; // Declare $conn as global
    $query = "SELECT id, email FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;  //Returning user even when its null
}

// Function to send the password reset email (MODIFIED)
function sendPasswordResetEmail($email, $resetLink) {
    $subject = "Password Reset";
    $body = "
    Hi,
    Please click on the following link to reset your password:
    $resetLink
    This link will expire in 1 hour.";

    $headers = "From: enval.connect@gmail.com\r\n";
    $headers .= "Reply-To: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    if (!mail($email, $subject, $body, $headers)) {
        error_log("RESET PASSWORD: Mail sending failed to " . $email .": " . error_get_last()['message']);
    }
}

// Handle the password reset request (MODIFIED)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limit
    if (!checkRateLimit($conn, "request")) {
        error_log("RESET PASSWORD: Rate limit exceeded for Client Key.");
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Please complete CAPTCHA to continue", ENT_QUOTES, 'UTF-8'),
            "captcha_required" => true
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(429);
        exit();
    }


    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $email = sanitize_input($input['email'] ?? null);

    error_log("RESET PASSWORD REQUEST: Attempt for email: " . $email . ", IP: " . $_SERVER['REMOTE_ADDR']);

    // Validate required fields
    if (!validate_required_fields([$email])) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("All fields are required.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }

    if (!validate_email($email)) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Invalid email format.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }

    $user = getUserByEmail($email);

    //Always generate and store the token even if the user does not exist.
    $resetToken = generateResetToken();
    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

    if($user){
        storeResetToken($user['id'], $resetToken, $expiration);
    } else {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars('If the email is registered, a password reset link has been sent to your email address.', ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(400);
    }
    $resetLink = "https://enval.in/password-reset?token={$resetToken}";
    sendPasswordResetEmail($email, $resetLink);
    echo json_encode([
        "success" => true,
        "message" => htmlspecialchars('A password reset link has been sent to your email if the email is on our system.', ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    http_response_code(200);
    exit;
}

?>
