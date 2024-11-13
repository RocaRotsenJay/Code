<?php
session_start();
require 'vendor/autoload.php';
use MongoDB\Client;

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->my_database->products;

// Fetch all products
$products = $collection->find();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Products</title>
    <style>
        /* Basic Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }

        /* Header and Navigation Styling */
        header { background-color: #333; color: #fff; padding: 1em; }
        header h1 { font-size: 2em; text-align: center; }
        nav { background-color: #444; display: flex; justify-content: center; padding: 10px 0; }
        nav a { color: white; text-decoration: none; padding: 0 15px; font-size: 1em; transition: color 0.3s; }
        nav a:hover { color: #f39c12; }

        /* Product Grid Layout */
        .products { padding: 20px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .product { background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 15px; text-align: center; transition: transform 0.2s; }
        .product:hover { transform: scale(1.05); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); }
        .product img { width: 100%; height: auto; border-radius: 8px; object-fit: cover; }
        .product-name { font-size: 1.2em; margin: 10px 0; color: #333; }
        .product-price { font-size: 1.1em; color: #f39c12; margin-bottom: 15px; }
        .add-to-cart-btn { background-color: #27ae60; color: white; border: none; padding: 10px 20px; font-size: 1em; border-radius: 4px; cursor: pointer; transition: background-color 0.3s; }
        .add-to-cart-btn:hover { background-color: #2ecc71; }

        /* Search and Filter Styling */
        .search-filter { display: flex; justify-content: center; margin-top: 20px; }
        .search-bar { padding: 10px; font-size: 1em; width: 300px; border-radius: 5px; border: 1px solid #ddd; margin-right: 10px; }
        .category-filter { padding: 10px; font-size: 1em; border-radius: 5px; border: 1px solid #ddd; margin-left: 10px; }

        /* Footer Styling */
        footer { background-color: #333; color: white; padding: 20px 0; font-size: 0.9em; }
        .footer-content { display: flex; flex-wrap: wrap; justify-content: space-around; max-width: 1200px; margin: 0 auto; }
        .footer-column { flex: 1; min-width: 200px; padding: 10px; }
        .footer-column h4 { margin-bottom: 10px; font-weight: bold; }
        .footer-column ul { list-style: none; padding: 0; }
        .footer-column ul li { margin: 5px 0; }
        .footer-column ul li a { color: white; text-decoration: none; transition: color 0.3s; }
        .footer-column ul li a:hover { color: #f39c12; }
        .footer-bottom { text-align: center; padding-top: 10px; color: #aaa; }
        .footer-icons { display: flex; gap: 10px; }
        .footer-icons img { width: 32px; height: auto; }
    </style>
</head>
<body>

<!-- Header and Navigation -->
<header>
    <h1>Welcome to Our Store</h1>
</header>
<nav>
    <a href="profile.php">Me</a>
    <a href="cart.php">Cart</a>
    <a href="feedback.php">Feedback</a>
</nav>

<!-- Search and Category Filter -->
<div class="search-filter">
    <input type="text" class="search-bar" id="searchBar" placeholder="Search by product name...">
    <select class="category-filter" id="categoryFilter">
        <option value="">Select Category</option>
        <option value="Pants">Pants</option>
        <option value="Polo">Polo</option>
        <option value="Shoes">Shoes</option>
        <option value="Hats">Hats</option>
    </select>
</div>

<div class="products">
    <h2>Featured Products</h2>
    <div class="product-grid" id="productGrid">
        <?php
        foreach ($products as $product): ?>
            <div class="product" data-category="<?php echo $product['category']; ?>" data-name="<?php echo $product['name']; ?>">
                <img src="<?php echo $product['imagePath']; ?>" alt="<?php echo $product['name']; ?>">
                <p class="product-name"><?php echo $product['name']; ?></p>
                <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?php echo $product['_id']; ?>">
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-content">
        <div class="footer-column">
            <h4>Customer Service</h4>
            <ul>
                <li><a href="helpcenter.php">Help Centre</a></li>
                <li><a href="#">Payment Methods</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>About Us</h4>
            <ul>
                <li><a href="about.php">About</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Policies</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Follow Us</h4>
            <div class="footer-icons">
                <a href="#"><img src="facebook-icon.png" alt="Facebook"></a>
                <a href="#"><img src="instagram-icon.png" alt="Instagram"></a>
                <a href="#"><img src="twitter-icon.png" alt="Twitter"></a>
            </div>
        </div>
        
    </div>
    <div class="footer-bottom">
        &copy; 2024 Porma Hub. All Rights Reserved.
    </div>
</footer>

<script>
    function applyFilters() {
        const searchTerm = document.getElementById('searchBar').value.toLowerCase();
        const selectedCategory = document.getElementById('categoryFilter').value;
        const products = document.querySelectorAll('.product');

        products.forEach(product => {
            const productName = product.getAttribute('data-name').toLowerCase();
            const productCategory = product.getAttribute('data-category');
            const matchesSearch = searchTerm === '' || productName.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || productCategory === selectedCategory;
            product.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }

    document.getElementById('searchBar').addEventListener('input', applyFilters);
    document.getElementById('categoryFilter').addEventListener('change', applyFilters);
</script>

</body>
</html>
