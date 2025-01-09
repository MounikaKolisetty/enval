<?php
$to = 'your_email@example.com';
$subject = 'Test Email';
$message = 'This is a test email.';
$headers = 'From: mouni.kolisetty@gmail.com' . "\r\n" .
           'Reply-To: mouni.kolisetty@gmail.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if(mail($to, $subject, $message, $headers)) {
    echo 'Email sent successfully.';
} else {
    echo 'Failed to send email.';
}
?>
