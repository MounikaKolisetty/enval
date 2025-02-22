<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable CORS
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

error_log('Signup Started' . date('Y-m-d H:i:s'));
// Include the database connection file
include 'connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the JSON input from the Angular app
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["message" => "Invalid JSON input: " . json_last_error_msg()]);
        http_response_code(400);
        exit();
    }

    // Get user details from input
    $username = $input['fullName'];
    $email = $input['email'];
    $password = password_hash($input['password'], PASSWORD_DEFAULT); // Hash the password for security
    $captchaResponse = $input['captchaResponse'];

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

    error_log('Captcha Verified' . date('Y-m-d H:i:s'));
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["message" => "Prepare failed: " . $conn->error]);
        http_response_code(500);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["message" => "User already exists.", "emailInUse" => true]);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Prepare and bind for insert
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["message" => "Prepare failed: " . $conn->error]);
        http_response_code(500);
        exit();
    }
    $stmt->bind_param("sss", $username, $email, $password);

    error_log('Signup Inserted to DB' . date('Y-m-d H:i:s'));

    // Execute the query
    if (!$stmt->execute()) {
        echo json_encode(["message" => "Execute failed: " . $stmt->error]);
        http_response_code(500);
        exit();
    }

    // Fetch the newly created user's details
    $userId = $stmt->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["message" => "Prepare failed: " . $conn->error]);
        http_response_code(500);
        exit();
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    error_log('Fetch newly created details' . date('Y-m-d H:i:s'));

    // Send welcome email
    $subject = "Welcome to Enval, $username";
    $message = "
    <html>
    <head>
        <title>Welcome to a world of possibilities</title>
    </head>
    <body>
        <h1>Thanks for joining our global community!</h1> 
        <p>Start exploring, and discovering, today.</p>
        <a href='https://enval.in/training'>
            <button style='padding: 10px 20px; background-color: #f0b429; border: none; color: #000000; border-radius: 5px; cursor: pointer;'>Browse Courses</button>
        </a>
    </body>
    </html>
    ";
    
    $headers = "From: enval.connect@gmail.com\r\n"; 
    $headers .= "Reply-To: enval.connect@gmail.com\r\n"; 
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; 

    if (!mail($email, $subject, $message, $headers)) {
        echo json_encode(["message" => "Failed to send the welcome email."]);
        http_response_code(500);
        exit();
    }

    error_log('Email sent' . date('Y-m-d H:i:s'));
    // Return success message along with user details
    echo json_encode(["message" => "User added successfully", "emailInUse" => false, "user" => $user]);
    http_response_code(201); // Created

    error_log('Signup Ended' . date('Y-m-d H:i:s'));
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
