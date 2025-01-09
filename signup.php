<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable CORS
header("Access-Control-Allow-Origin: *"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

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

    // Execute the query
    if (!$stmt->execute()) {
        echo json_encode(["message" => "Execute failed: " . $stmt->error]);
        http_response_code(500);
        exit();
    } else {
        echo json_encode(["message" => "User added successfully", "emailInUse" => false]);
        http_response_code(201); // Created
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
