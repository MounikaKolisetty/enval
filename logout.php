<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable CORS
header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}
include 'connect.php';
session_start();
error_log('Session started, session id: ' . session_id());

function logout() {
    if (isset($_SESSION['SessionId']) && !empty($_SESSION['SessionId'])) {
        error_log('isset($_SESSION["SessionId"]): ' . isset($_SESSION['SessionId']) . ', !empty($_SESSION["SessionId"]): ' . !empty($_SESSION['SessionId']));
        $sessionId = $_SESSION['SessionId'];
        deleteUserSession($sessionId);
        unset($_SESSION['SessionId']);
        echo json_encode(array("message" => "Logout successful"));
    } else {
        error_log('isset($_SESSION["SessionId"]): ' . isset($_SESSION['SessionId']) . ', !empty($_SESSION["SessionId"]): ' . !empty($_SESSION['SessionId']));
        echo json_encode(array("message" => "Logout unsuccessful"));
    }
}

function deleteUserSession($sessionId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM sessions WHERE session_id = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $db->error);
        return;
    }
    $stmt->execute([$sessionId]);
    if ($stmt->error) {
        error_log("Execute failed: " . $stmt->error);
    } else {
        error_log("Session deleted from database.");
    }
}

logout();
