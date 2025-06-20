<?php
// Database connection details
// $servername = "localhost";
// $username = "envalconsultants"; // Use your MySQL username
// $password = "envalconsultants"; // Use your MySQL password
// $dbname = "enval"; // Your database name

// Database connection details
$servername = "localhost";
$username = "root"; // Use your MySQL username
$password = ""; // Use your MySQL password
$dbname = "enval"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
