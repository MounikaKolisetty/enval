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
    error_log("SIGNUP: CSRF Token Missing from IP: " . $_SERVER['REMOTE_ADDR']);
    echo json_encode(["message" => "Invalid CSRF token"]);
    http_response_code(403);
    exit();
}

session_start();
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    error_log("SIGNUP: CSRF Token Mismatch from IP: " . $_SERVER['REMOTE_ADDR']);
    echo json_encode(["message" => "Invalid CSRF token"]);
    http_response_code(403);
    exit();
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'connect.php';
include 'rateLimit.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check rate limit
    if (!checkRateLimit($conn, "signup")) {
        error_log("SIGNUP: Rate limit exceeded for Client Key.");
        echo json_encode([
            "success" => false,
            "message" => "Too many attempts. Please try again after an hour.",
            "captcha_required" => true
        ]);
        http_response_code(429);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("SIGNUP: Invalid JSON input from IP: " . $_SERVER['REMOTE_ADDR']);
        echo json_encode(["message" => "Invalid JSON input: " . json_last_error_msg()]);
        http_response_code(400);
        exit();
    }

    // Get user details from input
    $username = $input['fullName'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_DEFAULT); // Hash the password for security
    $captchaResponse = $input['captchaResponse'];

    error_log("SIGNUP ATTEMPT: Email: " . $email . ", IP: " . $_SERVER['REMOTE_ADDR']); 

    // Verify CAPTCHA
    $secretKey = "6LfeP5cqAAAAAFuoiQlEzNQEtsEslby-HmeLf-YV";
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";

    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        error_log("SIGNUP: CAPTCHA verification failed for email: " . $email . ", IP: " . $_SERVER['REMOTE_ADDR']);
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
        error_log("SIGNUP: Attempt to register existing email: " . $email . ", IP: " . $_SERVER['REMOTE_ADDR']);
        echo json_encode(["message" => "Unable to create account. Please ensure all information is correct and try again later."]);
        http_response_code(400);
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
        error_log("SIGNUP: Failed to send verification email to: " . $email . ", IP: " . $_SERVER['REMOTE_ADDR']);
        echo json_encode(["message" => "Failed to send the verification email."]);
        http_response_code(500);
        exit();
    }

    echo json_encode(["message" => "Verification email sent successfully."]);
    http_response_code(200);

    $conn->close();
}
?>
