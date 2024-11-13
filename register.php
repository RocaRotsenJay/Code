<?php
// Connect to MongoDB
require 'vendor/autoload.php';
$mongoClient = new MongoDB\Client;
$database = $mongoClient->my_database;
$collection = $database->users;

// Get user input
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check if username already exists
$existingUser = $collection->findOne(['username' => $username]);
if ($existingUser) {
    header("Location: index.php?error=username_exists");
    exit;
}

// Insert user into MongoDB
$insertOneResult = $collection->insertOne([
    'username' => $username,
    'password' => $password
]);

header("Location: index.php?success=registered");
exit;