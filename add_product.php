<?php
session_start();
require 'vendor/autoload.php'; // MongoDB connection
use MongoDB\Client;

try {
    // MongoDB connection setup
    $mongoClient = new Client("mongodb://localhost:27017");
    $database = $mongoClient->my_database; // Replace 'my_database' with your actual database name
    $productCollection = $database->products; // Replace 'products' with your collection name
    error_log("Connected to MongoDB successfully.");
} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
    exit;
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture the form data
    $name = $_POST['name'] ?? ''; 
    $description = $_POST['description'] ?? '';
    $price = (float) ($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? ''; // Capture category from form
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    // MongoDB product document
    $product = [
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category, // Include category in product document
        "imagePath" => $imagePath,
        "createdAt" => new MongoDB\BSON\UTCDateTime() // Optional timestamp
    ];

    // Insert product into MongoDB
    try {
        $insertResult = $productCollection->insertOne($product);
        if ($insertResult->getInsertedCount() === 1) {
            echo "Product added successfully!";
            error_log("Product added successfully to MongoDB.");
        } else {
            echo "Failed to add product.";
            error_log("Failed to add product.");
        }
    } catch (Exception $e) {
        echo "Error inserting data: " . $e->getMessage();
        error_log("Error inserting data: " . $e->getMessage());
    }
}
?>

