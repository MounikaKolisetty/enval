<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

// Get the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data
$data = json_decode($rawData, true);
$formData = $data['formData'] ?? [];

if ($data) {
    // Sanitize and retrieve form data
    $name = isset($formData['name']) ? htmlspecialchars($formData['name']) : '';
    $email = isset($formData['email']) ? htmlspecialchars($formData['email']) : '';
    $message = isset($formData['message']) ? htmlspecialchars($formData['message']) : '';
    $corporateTraining = isset($formData['corporateTraining']) ? ($formData['corporateTraining'] ? 'Yes' : 'No') : 'No';
    $trainingForPractitioners = isset($formData['trainingForPractitioners']) ? ($formData['trainingForPractitioners'] ? 'Yes' : 'No') : 'No';
    $consulting = isset($formData['consulting']) ? ($formData['consulting'] ? 'Yes' : 'No') : 'No';
    $projects = isset($formData['projects']) ? ($formData['projects'] ? 'Yes' : 'No') : 'No';
    $subscribe = isset($formData['subscribe']) ? ($formData['subscribe'] ? 'Yes' : 'No') : 'No'; 

    $captchaResponse = $data['captchaResponse'];

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
    error_log('Data ' . $data);
    error_log('name ' . $name);
    error_log('email ' . $email);
    error_log('message ' . $message);
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            "success" => false,
            "message" => "Name, email, and message are required."
        ]);
        exit();
    }

    // ✅ 3. Rate Limit Requests
    if (!isset($_SESSION['email_sent'])) {
        $_SESSION['email_sent'] = 0;
    }
    if ($_SESSION['email_sent'] >= 5) {  
        echo json_encode(["success" => false, "message" => "Rate limit exceeded."]);
        http_response_code(429);
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
    $fullMessage = "Name: $name\nEmail: $email\nMessage: $message\n\n";
    $fullMessage .= "Corporate Training: $corporateTraining\n";
    $fullMessage .= "Training for Practitioners: $trainingForPractitioners\n";
    $fullMessage .= "Consulting: $consulting\n";
    $fullMessage .= "Projects: $projects\n";
    $fullMessage .= "Subscribe: $subscribe\n";

    $allowed_domains = ['@gmail.com', '@yahoo.com', '@enval.in'];
    $valid_email = false;

    foreach ($allowed_domains as $domain) {
        if (str_ends_with($email, $domain)) {
            $valid_email = true;
            break;
        }
    }

    if (!$email || !$valid_email || empty($name) || empty($message)) {
        echo json_encode(["success" => false, "message" => "Invalid or unauthorized recipient."]);
        exit();
    }


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
