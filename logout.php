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
session_start();

function logout() {
    if (isset($_SESSION['SessionId']) && !empty($_SESSION['SessionId'])) {
        $sessionId = $_SESSION['SessionId'];
        // Ensure that this function deletes the session from the database
        deleteUserSession($sessionId);
        unset($_SESSION['SessionId']);
        echo json_encode(array("message" => "Logout successful"));
    } else {
        echo json_encode(array("message" => "Logout unsuccessful"));
    }
}

function deleteUserSession($sessionId) {
    $stmt = $db->prepare("DELETE FROM user_sessions WHERE session_id = ?");
    $stmt->execute([$sessionId]);
}

logout();
?>
