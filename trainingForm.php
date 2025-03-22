<?php
// Allow CORS
//header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

include 'connect.php';
include 'rateLimit.php'; 

if (!checkRateLimit($conn, "trainingForm")) {
    error_log("TRAININGFORM: Rate limit exceeded for Client Key.");
    echo json_encode([
        "success" => false,
        "message" => "Too many attempts. Please try again after an hour.",
        "captcha_required" => true
    ]);
    http_response_code(429);
    exit();
}

// Get the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data
$data = json_decode($rawData, true);
$formData = $data['formData'] ?? [];

if ($data) {
    // Sanitize and retrieve form data
    $name = isset($formData['name']) ? htmlspecialchars($formData['name']) : '';
    $designation = isset($formData['designation']) ? htmlspecialchars($formData['designation']) : '';
    $organization = isset($formData['organization']) ? htmlspecialchars($formData['organization']) : '';
    $location = isset($formData['location']) ? htmlspecialchars($formData['location']) : '';
    $trainees = isset($formData['trainees']) ? htmlspecialchars($formData['trainees']) : '';
    $email = isset($formData['email']) ? htmlspecialchars($formData['email']) : '';
    $mobile = isset($formData['mobile']) ? htmlspecialchars($formData['mobile']) : '';

    $captchaResponse = $data['captchaResponse'];

    // Verify CAPTCHA
    $secretKey = "6LfeP5cqAAAAAFuoiQlEzNQEtsEslby-HmeLf-YV"; // Replace with your actual secret key
    // $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // Google test secret key
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";

    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        echo json_encode(["message" => "CAPTCHA verification failed."]);
        http_response_code(400);
        exit();
    }

    // Validate required fields
    if (empty($name) || empty($designation) || empty($organization) || empty($location) || empty($trainees) || empty($email) || empty($mobile)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required."
        ]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format."
        ]);
        exit();
    }

    // Email details
    $to = "enval.connect@gmail.com";
    $subject = "New Form Submission";
    $headers = "From: enval.connect@gmail.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Prepare message
    $fullMessage = "Name: $name\nDesignation: $designation\nOrganization: $organization\nLocation: $location\nNo. of Trainees: $trainees\nMobile: $mobile\nEmail: $email\n";

    // Send email using mail() function
    if (mail($to, $subject, $fullMessage, $headers)) {
        echo json_encode([
            "success" => true,
            "message" => "Email successfully sent!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to send email."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON data."
    ]);
}
?>
