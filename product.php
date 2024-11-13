<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->my_database;
$reviews = $database->reviews;

// Check and validate the product_id from the query parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['id'];
try {
    $product_id = new MongoDB\BSON\ObjectId($product_id);
} catch (Exception $e) {
    die("Invalid Product ID format.");
}

// Fetch the reviews for the product and store in an array
$product_reviews_cursor = $reviews->find(['product_id' => $product_id]);
$product_reviews = iterator_to_array($product_reviews_cursor);

$sum = 0;
$count = count($product_reviews);

foreach ($product_reviews as $review) {
    $sum += $review['rating'];
}

$average_rating = ($count > 0) ? round($sum / $count, 1) : 'No ratings yet';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Reviews</title>
    <style>
        .reviews {
            margin-top: 20px;
        }
        .review {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .review-rating {
            color: #FFD700;
            font-size: 18px;
        }
        .review p {
            margin: 5px 0;
        }
        .review small {
            color: #888;
        }
    </style>
</head>
<body>

<h3>Average Rating</h3>
<p><strong>Average Rating:</strong> <?php echo $average_rating; ?> / 5</p>

<h3>Customer Reviews</h3>
<div class="reviews">
    <?php foreach ($product_reviews as $review): ?>
        <div class="review">
            <div class="review-rating">
                <?php echo str_repeat("★", $review['rating']); ?>
                <?php echo str_repeat("☆", 5 - $review['rating']); ?>
            </div>
            <p><?php echo htmlspecialchars($review['comment']); ?></p>
            <small><?php echo $review['date']->toDateTime()->format('Y-m-d'); ?></small>
        </div>
    <?php endforeach; ?>
</div>

<h3>Leave a Review</h3>
<form action="submit_review.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
    <label for="rating">Rating (1 to 5):</label>
    <select name="rating" id="rating" required>
        <option value="5">5 - Excellent</option>
        <option value="4">4 - Good</option>
        <option value="3">3 - Average</option>
        <option value="2">2 - Poor</option>
        <option value="1">1 - Very Poor</option>
    </select>

    <label for="comment">Comment:</label>
    <textarea name="comment" id="comment" rows="4" required></textarea>

    <button type="submit">Submit Review</button>
</form>

</body>
</html>
