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

require 'connect.php';
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


// Get the POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid JSON data.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Extract user details safely
$userDetails = $input['userDetails'] ?? [];
$verificationDetails = $input['verificationDetails'] ?? [];
$paymentVerify = isset($input['paymentVerify']) ? filter_var($input['paymentVerify'], FILTER_VALIDATE_BOOLEAN) : false;

// Validate and sanitize user input
$userTitle = sanitize_input($userDetails['userTitle'] ?? '');
$userName = sanitize_input($userDetails['userName'] ?? '');
$userContact = sanitize_input($userDetails['userContact'] ?? '');
$userDesignation = sanitize_input($userDetails['userDesignation'] ?? '');
$userDepartment = sanitize_input($userDetails['userDepartment'] ?? '');
$userOrganization = sanitize_input($userDetails['userOrganization'] ?? '');
$userLocation = sanitize_input($userDetails['userLocation'] ?? '');
$userBusinessArea = sanitize_input($userDetails['userBusinessArea'] ?? '');
$userVAVME = sanitize_input($userDetails['userVAVME'] ?? '');
$userResponsibilities = sanitize_input($userDetails['userResponsibilities'] ?? '');
$userDegree = sanitize_input($userDetails['userDegree'] ?? '');
$userPGDegree = sanitize_input($userDetails['userPGDegree'] ?? '');
$userOtherDegree = sanitize_input($userDetails['userOtherDegree'] ?? '');
$userSponsoredby = sanitize_input($userDetails['userSponsoredby'] ?? '');
$userPurpose = sanitize_input($userDetails['userPurpose'] ?? '');
$userUsage = sanitize_input($userDetails['userUsage'] ?? '');
$userExpectation = sanitize_input($userDetails['userExpectation'] ?? '');
$userEmail = sanitize_input($userDetails['userEmail'] ?? '');
$courseTitle = sanitize_input($verificationDetails['course_title'] ?? '');
$razorpay_payment_id = sanitize_input($verificationDetails['razorpay_payment_id'] ?? '');
$razorpay_order_id = sanitize_input($verificationDetails['razorpay_order_id'] ?? '');
$razorpay_signature = sanitize_input($verificationDetails['razorpay_signature'] ?? '');

if (!validate_required_fields([$userTitle, $userName, $userContact, $userDesignation, $userDepartment, $userOrganization, 
                               $userLocation, $userBusinessArea, $userVAVME, $userResponsibilities, $userDegree, $userPGDegree,
                               $userOtherDegree, $userSponsoredby, $userPurpose, $userUsage, $userExpectation, $userEmail,
                               $courseTitle, $razorpay_payment_id, $razorpay_order_id, $razorpay_signature])) {
    echo json_encode([
        "success" => false,
        "message" => htmlspecialchars("All fields are required.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Title validation
if (!validate_title($userTitle)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid title format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Name validation
if (!validate_name($userName)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid name format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Contact validation
if (!validate_mobile($userContact)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid contact number.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Designation validation
if (!validate_designation($userDesignation)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid designation format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Department validation
if (!validate_department($userDepartment)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid department format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Organization validation
if (!validate_organization($userOrganization)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid organization format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Location validation
if (!validate_location($userLocation)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid location format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Business Area validation
if (!validate_business_area($userBusinessArea)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid business area format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// VAVME validation
if (!validate_vavme($userVAVME)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid VAVME format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Responsibilities validation
if (!validate_responsibilities($userResponsibilities)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid responsibilities format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Degree validation
if (!validate_degree($userDegree) || !validate_degree($userPGDegree) || !validate_degree($userOtherDegree)) {
    exit(json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid degree format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
}

// Sponsoredby validation
if (!validate_sponsored_by($userSponsoredby)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid Sponseredby format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Purpose validation
if (!validate_description($userPurpose) || !validate_description($userUsage) || !validate_description($userExpectation)) {
    exit(json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid input. Only letters, numbers, and basic punctuation allowed.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
}

// Email validation
if (!validate_email($userEmail)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid mail format.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Course Title validation
if (!validate_course_title($courseTitle)) {
    echo json_encode([
        "success" => false, 
        "message" => htmlspecialchars("Invalid course title.", ENT_QUOTES, 'UTF-8')
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

// Prepare and execute the SQL statement
$stmt = $conn->prepare('INSERT INTO user_details (user_title, user_name, user_contact, user_designation, user_department, user_organization, user_location, user_business_area, user_vavme, user_responsibilities, user_degree, user_pg_degree, user_other_degree, user_sponsoredby, user_purpose, user_usage, user_expectation, user_email, razorpay_payment_id, razorpay_order_id, razorpay_signature, course_title, payment_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

$stmt->execute([
    $userTitle,
    $userName,
    $userContact,
    $userDesignation,
    $userDepartment,
    $userOrganization,
    $userLocation,
    $userBusinessArea,
    $userVAVME,
    $userResponsibilities,
    $userDegree,
    $userPGDegree,
    $userOtherDegree,
    $userSponsoredby,
    $userPurpose,
    $userUsage,
    $userExpectation,
    $userEmail,
    $razorpay_payment_id,
    $razorpay_order_id,
    $razorpay_signature,
    $courseTitle,
    $paymentVerify
]);

// Return a success response
echo json_encode(['status' => 'success']);
?>
