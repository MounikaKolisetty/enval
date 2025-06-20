<?php
require 'phpmailer.php';

$response = sendEmail(
    'your@gmail.com',
    'recipient@example.com',
    'Test Subject',
    '<h1>This is the body</h1>',
    [
        'host' => 'smtp.gmail.com',
        'username' => 'your@gmail.com',
        'password' => 'yourpassword',
        'port' => 587,
        'encryption' => 'tls'
    ]
);

if ($response['status']) {
    echo "✅ " . $response['message'];
} else {
    echo "❌ " . $response['message'];
}
