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

// Get the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data
$data = json_decode($rawData, true);

if ($data) {
    // Sanitize and retrieve form data
    $firstname = isset($data['firstName']) ? htmlspecialchars($data['firstName']) : '';
    $lastname = isset($data['lastName']) ? htmlspecialchars($data['lastName']) : '';
    $email = isset($data['email']) ? htmlspecialchars($data['email']) : '';
    $phonenumber = isset($data['phoneNumber']) ? htmlspecialchars($data['phoneNumber']) : '';
    $message = isset($data['message']) ? htmlspecialchars($data['message']) : '';

    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($phonenumber) || empty($email) || empty($message)) {
        echo json_encode([
            "success" => false,
            "message" => "Name, email, and message are required."
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
    $to = $email;
    $subject = "New Form Submission";
    $headers = "From: enval.connect@gmail.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Prepare message
    $fullMessage = "FirstName: $firstname\nLastName: $lastname\nPhoneNumber: $phonenumber\nEmail: $email\nMessage: $message\n\n";

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
