<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
error_log('csrf_token' . $_SESSION['csrf_token']);
header('Content-Type: application/json');
echo json_encode(["csrf_token" => $_SESSION['csrf_token']]);
?>
