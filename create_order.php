
<?php
// Allow CORS
//header("Access-Control-Allow-Origin: http://localhost:4200"); // Replace with your frontend URL
header("Access-Control-Allow-Origin: https://enval.in"); // Replace with your frontend URL
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow necessary headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight request
    exit();
}

require 'index.php';

header('Content-Type: application/json');

try {
    $postData = json_decode(file_get_contents('php://input'), true);
    if (!$postData) {
        throw new Exception('Invalid input data');
    }

    $amount = $postData['amount'];
    if (!is_numeric($amount) || $amount <= 0) {
        throw new Exception('Invalid amount');
    }

    $orderData = [
        'receipt'         => uniqid(),
        'amount'          => $amount, // Amount in paise
        'currency'        => 'INR',
        'payment_capture' => 1 // auto capture
    ];

    $razorpayOrder = $api->order->create($orderData);
    $order_id = $razorpayOrder['id'];

    echo json_encode(['orderId' => $order_id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>