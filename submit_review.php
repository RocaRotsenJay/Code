<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->my_database;
$reviews = $database->reviews;

$product_id = $_POST['product_id'];
$rating = (int) $_POST['rating'];
$comment = $_POST['comment'];
$user_id = $_SESSION['user_id'];

// Insert review into MongoDB
$reviews->insertOne([
    'user_id' => new MongoDB\BSON\ObjectId($user_id),
    'product_id' => new MongoDB\BSON\ObjectId($product_id),
    'rating' => $rating,
    'comment' => $comment,
    'date' => new MongoDB\BSON\UTCDateTime()
]);

header("Location: product.php?id=" . $product_id);
exit;
?>
