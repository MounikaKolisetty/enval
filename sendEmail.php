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

include 'inputValidation.php'; // Include input validation functions

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the raw POST data
    $rawData = file_get_contents("php://input");

    // Decode the JSON data
    $data = json_decode($rawData, true);
    $formData = $data['formData'] ?? [];

    if ($data) {
        // Sanitize and retrieve form data
        $name = sanitize_input($formData['name']) ?? '';
        $email = sanitize_input($formData['email']) ?? '';
        $message = sanitize_input($formData['message']) ?? ''; 
        $corporateTraining = !empty($formData['corporateTraining']) && filter_var($formData['corporateTraining'], FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No';
        $trainingForPractitioners = !empty($formData['trainingForPractitioners']) && filter_var($formData['trainingForPractitioners'], FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No';
        $consulting = !empty($formData['consulting']) && filter_var($formData['consulting'], FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No';
        $projects = !empty($formData['projects']) && filter_var($formData['projects'], FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No';
        $subscribe = !empty($formData['subscribe']) && filter_var($formData['subscribe'], FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No';

        $captchaResponse = $data['captchaResponse'];

        // Verify CAPTCHA
        $secretKey = "6LfeP5cqAAAAAFuoiQlEzNQEtsEslby-HmeLf-YV"; // Replace with your actual secret key
        // $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // Google test secret key
        if (!verify_captcha($captchaResponse, $secretKey)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("CAPTCHA verification failed.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            http_response_code(400);
            exit();
        }

        // Validate required fields
        if (!validate_required_fields([$name, $email, $message, $corporateTraining, $trainingForPractitioners, $consulting, $projects, $subscribe])) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("All fields are required.", ENT_QUOTES, 'UTF-8')
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

        if (!validate_name($name)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid name format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }

        if (!validate_message($message)) {
            echo json_encode([
                "success" => false,
                "message" => htmlspecialchars("Invalid message format.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }  

        if (!validate_boolean($formData['corporateTraining'])) {
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Invalid value for corporateTraining.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        if (!validate_boolean($formData['trainingForPractitioners'])) {
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Invalid value for trainingForPractitioners.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        if (!validate_boolean($formData['consulting'])) {
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Invalid value for consulting.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        if (!validate_boolean($formData['projects'])) {
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Invalid value for projects.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }
        
        if (!validate_boolean($formData['subscribe'])) {
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Invalid value for subscribe.", ENT_QUOTES, 'UTF-8')
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
                       "Email: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n" .
                       "Message: " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "\n" ;
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
            echo json_encode([
                "success" => false, 
                "message" => htmlspecialchars("Invalid or unauthorized recipient.", ENT_QUOTES, 'UTF-8')
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit();
        }


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
