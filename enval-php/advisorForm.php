<?php

// Allow CORS
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
include 'inputValidation.php'; // Include input validation functions

if (!checkRateLimit($conn, "advisorForm")) {
    error_log("ADVISORFORM: Rate limit exceeded for Client Key.");
    echo json_encode([
        "success" => false,
        "message" => htmlspecialchars("Too many attempts. Please try again after an hour.", ENT_QUOTES, 'UTF-8'),
        "captcha_required" => true
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    http_response_code(429);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $rawData = file_get_contents("php://input");

    // Decode the JSON data
    $data = json_decode($rawData, true);
    $formData = $data['formData'] ?? [];

    if ($data) {
        // Sanitize and retrieve form data
        $name = sanitize_input($formData['name'] ?? '');
        $designation = sanitize_input($formData['designation'] ?? '');
        $organization = sanitize_input($formData['organization'] ?? '');
        $location = sanitize_input($formData['location'] ?? '');
        $email = sanitize_input($formData['email'] ?? '');
        $mobile = sanitize_input($formData['mobile'] ?? '');

        $captchaResponse = $data['captchaResponse'];

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
        if (!validate_required_fields([$name, $designation, $organization, $location, $email, $mobile])) {
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

        if (!validate_mobile($mobile)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid mobile number format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }

        if (!validate_name($name)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid name format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }

        if (!validate_designation($designation)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid designation format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        if (!validate_organization($organization)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid organization format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        if (!validate_location($location)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid location format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        // Email details
        $to = "enval.connect@gmail.com";
        $subject = "New Form Submission";
        $headers = "From: enval.connect@gmail.com\r\n";
        $headers .= "Reply-To: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Prepare message
        $fullMessage = "Name: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "\n" .
                    "Designation: " . htmlspecialchars($designation, ENT_QUOTES, 'UTF-8') . "\n" .
                    "Organization: " . htmlspecialchars($organization, ENT_QUOTES, 'UTF-8') . "\n" .
                    "Location: " . htmlspecialchars($location, ENT_QUOTES, 'UTF-8') . "\n" .
                    "Mobile: " . htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8') . "\n" .
                    "Email: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n";

        // Send email using mail() function
        if (mail($to, $subject, $fullMessage, $headers)) {
            echo json_encode([
                "success" => true,
                "message" => htmlspecialchars("Email successfully sent!", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        } else {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Failed to send email.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Invalid JSON data.", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
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