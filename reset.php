<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require 'connect.php'; // Ensure this file creates the $conn object

// Function to get the reset token from the database
function getPasswordResetToken($token) {
    global $conn; // Declare $conn as global
    $query = "SELECT * FROM passwordresettokens WHERE token = ? AND isVerified = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get the user by ID
function getUserById($userId) {
    global $conn; // Declare $conn as global
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to update the user's password
function updateUserPassword($userId, $newPassword) {
    global $conn; // Declare $conn as global
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$hashedPassword, $userId]);
}

// Handle the password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? null;
    $newPassword = $input['newPassword'] ?? null;

    if (!$token || !$newPassword) {
        echo json_encode(["success" => false, 'message' => 'Token and new password are required.']);
        exit;
    }

    // Retrieve the reset token from the database
    $resetToken = getPasswordResetToken($token);
    if (!$resetToken || strtotime($resetToken['expiration']) < time()) {
        echo json_encode(["success" => false, 'message' => 'Invalid or expired token.']);
        exit;
    }

    // Retrieve the user associated with the token
    $user = getUserById($resetToken['user_id']);
    if (!$user) {
        echo json_encode(["success" => false, 'message' => 'User not found.']);
        exit;
    }

    // Update the user's password
    if (updateUserPassword($user['id'], $newPassword)) {
        echo json_encode(["success" => true, 'message' => 'Password has been reset.']);
    } else {
        echo json_encode(["success" => false, 'message' => 'Failed to reset password.']);
    }

    $stmt = $conn->prepare("UPDATE passwordresettokens SET isVerified = 1, token = NULL WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    exit;
}
?>
