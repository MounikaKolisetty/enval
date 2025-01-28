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
// Include the rate limiter file
include 'rateLimiter.php';

session_start();

// Set up rate limiter with a capacity of 5 tokens and a rate of 1 token per second
if (!isset($_SESSION['rateLimiter'])) {
    $_SESSION['rateLimiter'] = new RateLimiter(5, 1);
}

$rateLimiter = $_SESSION['rateLimiter'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

if (!$rateLimiter->allowRequest()) {
    echo json_encode(["message" => "Too many requests. Please try again later."]);
    http_response_code(429); // Too Many Requests
    exit();
}
// Get the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data
$data = json_decode($rawData, true);

if ($data) {
    // Sanitize and retrieve form data
    $name = isset($data['name']) ? htmlspecialchars($data['name']) : '';
    $email = isset($data['email']) ? htmlspecialchars($data['email']) : '';
    $message = isset($data['message']) ? htmlspecialchars($data['message']) : '';
    $corporateTraining = isset($data['corporateTraining']) ? ($data['corporateTraining'] ? 'Yes' : 'No') : 'No';
    $trainingForPractitioners = isset($data['trainingForPractitioners']) ? ($data['trainingForPractitioners'] ? 'Yes' : 'No') : 'No';
    $consulting = isset($data['consulting']) ? ($data['consulting'] ? 'Yes' : 'No') : 'No';
    $projects = isset($data['projects']) ? ($data['projects'] ? 'Yes' : 'No') : 'No';
    $subscribe = isset($data['subscribe']) ? ($data['subscribe'] ? 'Yes' : 'No') : 'No';

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
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
    $fullMessage = "Name: $name\nEmail: $email\nMessage: $message\n\n";
    $fullMessage .= "Corporate Training: $corporateTraining\n";
    $fullMessage .= "Training for Practitioners: $trainingForPractitioners\n";
    $fullMessage .= "Consulting: $consulting\n";
    $fullMessage .= "Projects: $projects\n";
    $fullMessage .= "Subscribe: $subscribe\n";

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
}
?>
