<?php
session_start();
require 'vendor/autoload.php';

// Connect to MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->my_database;
$collection = $database->users;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $address = $_POST['address'];

    $userId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
    $collection->updateOne(
        ['_id' => $userId],
        ['$set' => ['address' => $address]]
    );

    header("Location: profile.php"); // Redirect back to profile page
    exit;
}
?>
