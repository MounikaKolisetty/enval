<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable CORS
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log('Signup Started ' . date('Y-m-d H:i:s'));
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["message" => "Invalid JSON input: " . json_last_error_msg()]);
        http_response_code(400);
        exit();
    }

    // Get user details from input
    $username = $input['fullName'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_DEFAULT); // Hash the password for security
    $captchaResponse = $input['captchaResponse'];

    // Verify CAPTCHA
    $secretKey = "6LfeP5cqAAAAAFuoiQlEzNQEtsEslby-HmeLf-YV";
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";

    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        echo json_encode(["message" => "CAPTCHA verification failed."]);
        http_response_code(400);
        exit();
    }

    error_log('Captcha Verified ' . date('Y-m-d H:i:s'));

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["message" => "User already exists.", "emailInUse" => true]);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Generate email verification token
    $token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    $isVerified = false;

    // Store token in database
    $stmt = $conn->prepare("INSERT INTO users (email, username, password, token, expiry, isVerified) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $email, $username, $password, $token, $expiry, $isVerified);
    $stmt->execute();
    $stmt->close();

    // Send email verification link
    $verifyLink = "https://enval.in/verify-email?token=$token";
    $subject = "Verify Your Email";
    $message = "<html><body>
        <p>Click the link below to verify your email:</p>
        <a href='$verifyLink'>Verify Email</a>
        </body></html>";
    $headers = "From: enval.connect@gmail.com\r\n";
    $headers .= "Reply-To: enval.connect@gmail.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (!mail($email, $subject, $message, $headers)) {
        echo json_encode(["message" => "Failed to send the verification email."]);
        http_response_code(500);
        exit();
    }

    echo json_encode(["message" => "Verification email sent successfully.", "emailInUse" => false]);
    http_response_code(200);

    error_log('Signup Ended ' . date('Y-m-d H:i:s'));
    $conn->close();
}
?>
