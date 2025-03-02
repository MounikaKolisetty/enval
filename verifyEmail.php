<?php
include 'connect.php';
header("Content-Type: application/json");

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$token = $input['token'] ?? '';

if (!$token) {
    echo json_encode(["success" => false, "message" => "Invalid token."]);
    exit();
}

// Check if token exists in the database
$stmt = $conn->prepare("SELECT email, expiry FROM users WHERE token = ? AND isVerified = 0");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $expiryTime = strtotime($row['expiry']);
    $currentTime = time();

    error_log('verify email' . $expiryTime . $currentTime);

    if ($currentTime > $expiryTime) {
        echo json_encode(["success" => false, "message" => "Expired token."]);
        exit();
    }
    // Update user as verified
    $stmt = $conn->prepare("UPDATE users SET isVerified = 1, token = NULL WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Email verified successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid or expired token."]);
}

$stmt->close();
$conn->close();
?>
