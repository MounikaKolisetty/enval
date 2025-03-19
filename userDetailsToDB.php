<?php
// Allow CORS
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
//header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
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
    echo json_encode(["message" => "Invalid CSRF token"]);
    http_response_code(403);
    exit();
}

session_start();
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    error_log("CSRF Token Mismatch");
    echo json_encode(["message" => "Invalid CSRF token"]);
    http_response_code(403);
    exit();
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

require 'connect.php';

// Get the POST data
$input = json_decode(file_get_contents('php://input'), true);
$verificationDetails = $input['verificationDetails'];
$userDetails = $input['userDetails'];
$paymentVerify = $input['paymentVerify'];

// Prepare and execute the SQL statement
$stmt = $conn->prepare('INSERT INTO user_details (user_title, user_name, user_contact, user_designation, user_department, user_organization, user_location, user_business_area, user_vavme, user_responsibilities, user_degree, user_pg_degree, user_other_degree, user_sponsoredby, user_purpose, user_usage, user_expectation, user_email, razorpay_payment_id, razorpay_order_id, razorpay_signature, course_title, payment_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

$stmt->execute([
    $userDetails['userTitle'],
    $userDetails['userName'],
    $userDetails['userContact'],
    $userDetails['userDesignation'],
    $userDetails['userDepartment'],
    $userDetails['userOrganization'],
    $userDetails['userLocation'],
    $userDetails['userBusinessArea'],
    $userDetails['userVAVME'],
    $userDetails['userResponsibilities'],
    $userDetails['userDegree'],
    $userDetails['userPGDegree'],
    $userDetails['userOtherDegree'],
    $userDetails['userSponsoredby'],
    $userDetails['userPurpose'],
    $userDetails['userUsage'],
    $userDetails['userExpectation'],
    $userDetails['userEmail'],
    $verificationDetails['razorpay_payment_id'],
    $verificationDetails['razorpay_order_id'],
    $verificationDetails['razorpay_signature'],
    $verificationDetails['course_title'],
    $paymentVerify
]);

// Return a success response
echo json_encode(['status' => 'success']);
?>
