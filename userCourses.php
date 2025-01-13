<?php
// header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

// Include the database connection
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the JSON input from the Angular app
    $input = json_decode(file_get_contents('php://input'), true);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["message" => "Invalid JSON input: " . json_last_error_msg()]);
        http_response_code(400);
        exit();
    }

    // Extract the user ID from the input and convert it to an integer
    $user_id = intval($input['userid']);

    // Step 1: Fetch email from users table based on user_id
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    if ($stmt === false) {
        echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    // Check if email was found
    if (empty($email)) {
        echo json_encode(["message" => "Email not found for the provided user ID. "]);
        http_response_code(200);
        exit();
    }

    // Step 2: Fetch details from user_details table based on email
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE user_email = ?");
    if ($stmt === false) {
        echo json_encode(array("error" => "Prepare statement failed: " . $conn->error));
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the data and store it in an array
        $response = array();
        while($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        // Respond with the data in JSON format
        echo json_encode($response);
    } else {
        // Respond with an empty array if no results found
        echo json_encode(array());
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
