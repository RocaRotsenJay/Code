<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$mongoClient = new Client("mongodb://localhost:27017");
$collection = $mongoClient->myDatabase->products;

$products = $collection->find();

echo "<h2>Product List</h2>";
echo "<table border='1'><tr><th>Name</th><th>Description</th><th>Price</th><th>Image</th></tr>";
foreach ($products as $product) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($product['name']) . "</td>";
    echo "<td>" . htmlspecialchars($product['description']) . "</td>";
    echo "<td>$" . htmlspecialchars($product['price']) . "</td>";
    echo "<td><img src='" . htmlspecialchars($product['imagePath']) . "' width='100'></td>";
    echo "</tr>";
}
echo "</table>";
?>
