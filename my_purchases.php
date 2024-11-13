<?php
session_start();
require 'vendor/autoload.php';

use MongoDB\Client;

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

// Verify user ID from session and ensure it's unique per user
$userIdFromSession = $_SESSION['user_id'] ?? null;
if (!$userIdFromSession) {
    error_log("User ID is missing from session.");
    exit("User not logged in.");
}

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$database = $client->my_database;
$purchasesCollection = $database->purchases;

// Try converting session user ID to MongoDB ObjectId
try {
    $userId = new MongoDB\BSON\ObjectId($userIdFromSession);
} catch (Exception $e) {
    error_log("Failed to convert session user ID to ObjectId: " . $e->getMessage());
    exit("Invalid user ID format.");
}

// Fetch user purchases
$purchases = $purchasesCollection->find(['user_id' => $userId]);

// Count and reset iterator for display
$purchaseCount = iterator_count($purchases);
$purchases = $purchasesCollection->find(['user_id' => $userId]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Purchases</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .purchase-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 10px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .purchase-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        .purchase-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.2s;
        }
        .purchase-card:hover {
            transform: scale(1.02);
        }
        .purchase-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .purchase-details {
            margin-top: 10px;
        }
        .purchase-details h3 {
            font-size: 16px;
            color: #333;
        }
        .purchase-details p {
            font-size: 14px;
            color: #777;
            margin: 5px 0;
        }
        .price {
            font-size: 18px;
            color: #ff5722;
            font-weight: bold;
        }
        .quantity {
            font-size: 14px;
            color: #555;
        }
        .rate-button {
            display: inline-block;
            margin-top: 10px;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .rate-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="purchase-container">
    <h2>My Purchases</h2>
    <div class="purchase-grid">
        <?php if ($purchaseCount > 0): ?>
            <?php foreach ($purchases as $purchase): ?>
                <div class="purchase-card">
                    <div class="purchase-image">
                        <img src="<?php echo htmlspecialchars($purchase['product_image']); ?>" alt="Product Image">
                    </div>
                    <div class="purchase-details">
                        <h3><?php echo htmlspecialchars($purchase['product_name']); ?></h3>
                        <p class="price">₱<?php echo htmlspecialchars($purchase['price']); ?></p>
                        <p class="quantity">Quantity: <?php echo htmlspecialchars($purchase['quantity']); ?></p>
                        <p class="date">Date: <?php echo htmlspecialchars($purchase['date']->toDateTime()->format('Y-m-d')); ?></p>
                        
                        <!-- Check if product_id exists and is a valid MongoDB ObjectId -->
                        <!-- Check if product_id exists and is a valid MongoDB ObjectId -->
<?php if (isset($purchase['product_id']) && $purchase['product_id'] instanceof MongoDB\BSON\ObjectId): ?>
    <a href="feedback.php?product_id=<?php echo htmlspecialchars((string)$purchase['product_id']); ?>" class="rate-button">Rate this Product?</a>
<?php else: ?>
    <p>Product ID is missing or invalid.</p>
<?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No purchases found for your account.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
