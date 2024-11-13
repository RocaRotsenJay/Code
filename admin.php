<?php
session_start();
require 'vendor/autoload.php'; // Include Composer's autoloader for MongoDB
use MongoDB\Client;

// Set up MongoDB connection
$client = new Client("mongodb://localhost:27017"); // Replace with your MongoDB server details
$collection = $client->my_database->products; // Replace 'myDatabase' with your database name

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = (float) $_POST['price'];
    $category = $_POST['category']; // Capture category
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        
        // Move uploaded file to the target directory
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    // Insert product data into MongoDB
    $product = [
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "category" => $category, // Add category to product data
        "imagePath" => $imagePath,
        "createdAt" => new MongoDB\BSON\UTCDateTime() // Optional, adds a timestamp
    ];

    try {
        $insertResult = $collection->insertOne($product);
        if ($insertResult->getInsertedCount() === 1) {
            echo "Product added successfully!";
        } else {
            echo "Failed to add product.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <style>
    /* Styling for the menu bar */
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
    .menu {
      background-color: #4CAF50;
      overflow: hidden;
      padding: 10px;
    }
    .menu a {
      color: white;
      padding: 14px 20px;
      text-align: center;
      text-decoration: none;
      margin-right: 10px;
      font-weight: bold;
    }
    .menu a:hover {
      background-color: #45a049;
      border-radius: 5px;
    }
    .container {
      padding: 20px;
      max-width: 600px;
      margin: 40px auto;
      background-color: white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      border-radius: 8px;
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    form label {
      font-weight: bold;
      color: #555;
    }
    form input[type="text"],
    form input[type="number"],
    form input[type="file"],
    form select,
    form textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
    }
    form button {
      width: 100%;
      padding: 12px;
      background-color: #4CAF50;
      border: none;
      border-radius: 5px;
      color: white;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    form button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="menu">
    <a href="admin.php">Manage Product</a>
    <a href="homepage.php">View Homepage</a>
    <a href="product.php">Manage Feedback</a>
    <a href="purchase_history.php">Purchase History</a>
    <a href="cart.php">Cart</a>
  </div>

  <div class="container">
    <h2>Add New Items</h2>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
      <label for="name">Product Name:</label>
      <input type="text" id="name" name="name" placeholder="Enter product name" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="4" placeholder="Enter product description" required></textarea>

      <label for="price">Price:</label>
      <input type="number" id="price" name="price" placeholder="Enter product price" required>

      <label for="category">Category:</label>
      <select id="category" name="category" required>
        <option value="Pants">Pants</option>
        <option value="Polo">Polo</option>
        <option value="Shoes">Shoes</option>
        <option value="Hats">Hats</option>
      </select>

      <label for="image">Product Image:</label>
      <input type="file" id="image" name="image" accept="image/*" required>

      <button type="submit">Add Product</button>
    </form>
  </div>
</body>
</html>
