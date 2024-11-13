<?php
session_start();
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$feedbackCollection = $client->my_database->feedback;
$productCollection = $client->my_database->products;
$orderCollection = $client->my_database->orders;

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    exit("User not logged in.");
}

$products = [];
// Fetch user's purchased products from the orders collection
$orders = $orderCollection->find(['user_id' => new ObjectId($userId)]);

foreach ($orders as $order) {
    foreach ($order['products'] as $product) {
        $productDetails = $productCollection->findOne(['_id' => new ObjectId($product['product_id'])]);
        if ($productDetails) {
            $products[] = $productDetails;
        }
    }
}

if (empty($products)) {
    echo "<p>You have no purchased products to rate.</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Product to Rate</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
    <h2>Select a Product to Rate</h2>
    <form action="feedback.php" method="GET">
        <select name="product_id" required>
            <option value="">Select a product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo htmlspecialchars((string) $product['_id']); ?>"><?php echo htmlspecialchars($product['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Rate this Product</button>
    </form>
</body>
</html>
