<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);
require 'vendor/autoload.php'; // Include Composer's autoloader

$client = new MongoDB\Client("mongodb+srv://enval:enval@enval-signup.hct2v.mongodb.net/?retryWrites=true&w=majority&appName=Enval-signup");

$collection = $client->myapp->test; // Replace 'test' with your database name and 'users' with your collection name
$insertResult = $collection->insertOne([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

echo "Inserted with Object ID '{$insertResult->getInsertedId()}'";
?>
