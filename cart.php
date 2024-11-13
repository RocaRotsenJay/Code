<?php
session_start();
require 'vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$collection = $client->my_database->products;

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add' && isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Validate MongoDB ObjectId
    if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
        $product = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);

        if ($product) {
            $found = false;

            // Check if product exists in the cart
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['_id'] === (string)$productId) {
                    $item['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = [
                    '_id' => (string)$product['_id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'imagePath' => $product['imagePath'],
                    'description' => $product['description'], // Add description
                    'quantity' => 1
                ];
            }
        }
    }
}

// Update quantity or delete item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['_id'])) {
    $cartItemId = $_POST['_id'];

    if ($_POST['action'] === 'delete') {
        // Delete item
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($cartItemId) {
            return $item['_id'] !== $cartItemId;
        });
    } elseif ($_POST['action'] === 'update' && isset($_POST['quantity'])) {
        // Update quantity
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['_id'] === $cartItemId) {
                $item['quantity'] = max(1, (int)$_POST['quantity']);
                break;
            }
        }
    }
}

// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $cartItem) {
    $totalPrice += $cartItem['price'] * $cartItem['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        /* Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; color: #333; display: flex; justify-content: center; }
        
        /* Main Cart Container */
        .cart { max-width: 900px; width: 100%; margin: 40px 0; padding: 20px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        
        /* Header */
        h2 { font-size: 2em; color: #333; text-align: center; margin-bottom: 25px; }
        
        /* Cart Items */
        .cart-items { display: flex; flex-direction: column; gap: 20px; }
        .cart-item { display: flex; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 10px; background-color: #fafafa; transition: transform 0.2s; }
        .cart-item:hover { transform: scale(1.02); }
        
        /* Image and Details */
        .cart-item img { width: 100px; height: auto; border-radius: 8px; object-fit: cover; margin-right: 20px; }
        .item-details { flex-grow: 1; display: flex; flex-direction: column; gap: 10px; }
        .item-name { font-size: 1.2em; font-weight: bold; color: #333; }
        .item-price { font-size: 1.1em; color: #f39c12; }
        .item-description { font-size: 0.9em; color: #555; margin-top: 5px; }
        
        /* Quantity Form */
        .quantity-form { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
        .quantity-form input[type="number"] { width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 5px; text-align: center; }
        
        /* Buttons */
        .update-btn, .delete-btn { border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 0.9em; transition: background-color 0.3s; }
        .update-btn { background-color: #27ae60; color: white; }
        .update-btn:hover { background-color: #219150; }
        .delete-btn { background-color: #e74c3c; color: white; }
        .delete-btn:hover { background-color: #c0392b; }
        
        /* Checkout Button */
        .checkout-btn { width: 100%; padding: 15px; margin-top: 25px; background-color: #3498db; color: white; border: none; border-radius: 5px; font-size: 1.2em; cursor: pointer; transition: background-color 0.3s; }
        .checkout-btn:hover { background-color: #2980b9; }
        
        /* Empty Cart Message */
        .empty-cart { text-align: center; font-size: 1.2em; color: #555; }
        
        /* Total Price */
        .total-price { font-size: 1.3em; font-weight: bold; color: #27ae60; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

<div class="cart">
    <h2>Your Cart Items</h2>

    <!-- Back Button -->
    <a href="homepage.php" class="back-btn">Back to Homepage</a>

    <?php if (empty($_SESSION['cart'])): ?>
        <p class="empty-cart">Your cart is empty.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($_SESSION['cart'] as $cartItem): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($cartItem['imagePath']); ?>" alt="<?php echo htmlspecialchars($cartItem['name']); ?>">
                    
                    <div class="item-details">
                        <p class="item-name"><?php echo htmlspecialchars($cartItem['name']); ?></p>
                        <p class="item-price">₱<?php echo number_format($cartItem['price'], 2); ?></p>
                        <p class="item-description">
                            <?php echo isset($cartItem['description']) ? htmlspecialchars($cartItem['description']) : 'No description available'; ?>
                        </p>
                        <form action="cart.php" method="POST" class="quantity-form">
                            <input type="hidden" name="_id" value="<?php echo $cartItem['_id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $cartItem['quantity']; ?>" min="1">
                            <button type="submit" name="action" value="update" class="update-btn">Update</button>
                        </form>
                    </div>
                    
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="_id" value="<?php echo $cartItem['_id']; ?>">
                        <button type="submit" name="action" value="delete" class="delete-btn">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Total Price Section -->
        <div class="total-price">
            <p>Total: ₱<?php echo number_format($totalPrice, 2); ?></p>
        </div>

        <form action="payment.php" method="POST">
            <button type="submit" class="checkout-btn">Check Out</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
