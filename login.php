<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable CORS
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
// header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

// Set content type to JSON
header('Content-Type: application/json');

// Include the database connection file
include 'connect.php';

session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the JSON input from the Angular app
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["message" => "Invalid JSON input: " . json_last_error_msg()]);
        http_response_code(400);
        exit();
    }

    // Get user details from input
    $email = $input['email'];
    $password = $input['password'];
    $captchaResponse = $input['captchaResponse'];

    // Verify CAPTCHA
    $secretKey = "6LfeP5cqAAAAAFuoiQlEzNQEtsEslby-HmeLf-YV"; // Replace with your actual secret key
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";

    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        echo json_encode(["message" => "CAPTCHA verification failed."]);
        http_response_code(400);
        exit();
    }
    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["message" => "Prepare failed: " . $conn->error]);
        http_response_code(500);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["message" => "Invalid email or password.", "invalidInputs" => true]);
        http_response_code(200);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Check password
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // Create session ID
        $sessionId = bin2hex(random_bytes(16));
        $userId = $user['id'];
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $_SESSION['SessionId'] = $sessionId;

        // Insert session into database
        $sessionStmt = $conn->prepare("INSERT INTO sessions (session_id, user_id, expires_at) VALUES (?, ?, ?)");
        if (!$sessionStmt) {
            echo json_encode(["message" => "Prepare failed: " . $conn->error]);
            http_response_code(500);
            exit();
        }
        $sessionStmt->bind_param("sis", $sessionId, $userId, $expiresAt);
        if (!$sessionStmt->execute()) {
            echo json_encode(["message" => "Execute failed: " . $sessionStmt->error]);
            http_response_code(500);
            exit();
        }

        // Set session ID in response
        setcookie("SessionId", $sessionId, time() + (30 * 60), "/"); // 30 minutes

        echo json_encode(["message" => "Login successful", "invalidInputs" => false, "user" => $user]);
        http_response_code(200);
    } else {
        echo json_encode(["message" => "Invalid email or password.", "invalidInputs" => true]);
        http_response_code(200);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
