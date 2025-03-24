<?php
// Allow CORS
//header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
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
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode([
        "success" => false,
        "error" => htmlspecialchars("Unauthorized access", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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

require 'index.php';
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

header('Content-Type: application/json');

try {
    $postData = json_decode(file_get_contents('php://input'), true);
    if (!$postData) {
        throw new Exception('Invalid input data');
    }

    $payment_id = $postData['razorpay_payment_id'];
    $order_id = $postData['razorpay_order_id'];
    $signature = $postData['razorpay_signature'];
    $courseTitle = $postData['course_title'];
    $userEmail = $postData['user_email'];

    if (!$payment_id || !$order_id || !$signature) {
        throw new Exception('Missing payment verification data');
    }

    $attributes = [
        'razorpay_order_id' => $order_id,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $signature
    ];

    $api->utility->verifyPaymentSignature($attributes);

    // Payment is successful, you can update your database or perform other actions
    $to = $userEmail; // Replace with recipient's 
    $subject = "Your Purchase of $courseTitle is Successful!"; 
    $headers = "From: enval.connect@gmail.com\r\n"; 
    $headers .= "Reply-To: enval.connect@gmail.com\r\n"; 
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; 
    $message = " 
    <html> 
    <head> 
        <title>Purchase Confirmation</title> 
    </head> 
    <body> 
        <h1>Congratulations!</h1> 
        <p>Your purchase of the course titled <strong>$courseTitle</strong> was successful.</p> 
        <p>Order ID: $order_id</p> 
        <p>If you have any questions, feel free to contact us.</p> 
    </body> 
    </html> "; 
    if (mail($to, $subject, $message, $headers)) { 
        echo json_encode([
            'status' => 'success', 
            'message' => htmlspecialchars('Payment was successful and email sent!', ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); } 
        else { throw new Exception('Failed to send email.'); }
} catch (Exception $e) {
    // Payment failed or was tampered with
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => htmlspecialchars('Payment verification failed: ' . $e->getMessage(), ENT_QUOTES, 'UTF-8'
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
?>
