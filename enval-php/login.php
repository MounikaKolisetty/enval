<?php
// Enable error reporting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log');

// Enable CORS
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
// header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
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

// Set content type to JSON
header('Content-Type: application/json');

// Include the database connection file
include 'connect.php';
include 'rateLimit.php'; 
include 'inputValidation.php'; // Include input validation functions

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!checkRateLimit($conn, "login")) {
        error_log("LOGIN: Rate limit exceeded for Client Key.");
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Too many attempts. Please try again after an hour.", ENT_QUOTES, 'UTF-8'),
            "captcha_required" => true
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(429);
        exit();
    }

    // Get the JSON input from the Angular app
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Invalid JSON input: " . json_last_error_msg(), ENT_QUOTES, 'UTF-8'),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(400);
        exit();
    }

    // Get user details from input
    $email = sanitize_input($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $captchaResponse = $input['captchaResponse'];

    // Verify CAPTCHA
    $secretKey = "6LfeP5cqAAAAAFuoiQlEzNQEtsEslby-HmeLf-YV"; // Replace with your actual secret key
    if (!verify_captcha($captchaResponse, $secretKey)) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("CAPTCHA verification failed.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(400);
        exit();
    }

    // Validate required fields
    if (!validate_required_fields([$email, $password])) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("All fields are required.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }

    if (!isValidEmail($email)) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Invalid email address!", ENT_QUOTES, 'UTF-8')
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

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Prepare failed: " . $conn->error, ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(500);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Invalid email or password.", ENT_QUOTES, 'UTF-8'),
            "invalidInputs" => true
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(200);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Fetch user data
    $user = $result->fetch_assoc();

    // Check if the account is verified
    if ($user['isVerified'] == 0) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Your account is not verified. Please check your email for the verification link.", ENT_QUOTES, 'UTF-8'),
            "notVerified" => true
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(200);
        exit();
    }
    
    if (password_verify($password, $user['password'])) {
        // Create session ID
        $sessionId = bin2hex(random_bytes(16));
        $userId = $user['id'];
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $_SESSION['SessionId'] = $sessionId;
        $_SESSION['user_id'] = $userId;
        // Insert session into database
        $sessionStmt = $conn->prepare("INSERT INTO sessions (session_id, user_id, expires_at) VALUES (?, ?, ?)");
        if (!$sessionStmt) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Prepare failed: " . $conn->error, ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            http_response_code(500);
            exit();
        }
        $sessionStmt->bind_param("sis", $sessionId, $userId, $expiresAt);
        if (!$sessionStmt->execute()) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Execute failed: " . $sessionStmt->error, ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            http_response_code(500);
            exit();
        }

        // Set session ID in response
        setcookie("SessionId", $sessionId, time() + (30 * 60), "/"); // 30 minutes

        echo json_encode([
            "message" => htmlspecialchars("Login successful", ENT_QUOTES, 'UTF-8'),
            "invalidInputs" => false, 
            "user" => $user
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(200);
    } else {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Invalid email or password.", ENT_QUOTES, 'UTF-8'),
            "invalidInputs" => true
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(200);
    }

    // Close the statement and connection
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
