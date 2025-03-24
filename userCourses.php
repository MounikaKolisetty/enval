<?php
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
// header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start(); // Ensure session is started

include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(403); // Forbidden
        echo json_encode([
            "success" => false
            "error" => htmlspecialchars("Unauthorized access", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    error_log($user_id);

    // Step 1: Fetch email from users table based on user_id
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    if ($stmt === false) {
        echo json_encode([
            "success" => false,
            "error" => htmlspecialchars("Prepare statement failed: " . $conn->error, ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    // Check if email was found
    if (empty($email)) {
        echo json_encode([
            "success" => false,
            "message" => htmlspecialchars("Email not found for the provided user ID. ", ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        http_response_code(200);
        exit();
    }

    // Step 2: Fetch details from user_details table based on email
    $stmt = $conn->prepare("SELECT * FROM user_details WHERE user_email = ?");
    if ($stmt === false) {
        echo json_encode([
            "success" => false,
            "error" => htmlspecialchars("Prepare statement failed: " . $conn->error, ENT_QUOTES, 'UTF-8')
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
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
