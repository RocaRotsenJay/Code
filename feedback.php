<?php
session_start();
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

// Get product ID from URL
$productId = $_GET['product_id'] ?? null;
if (!$productId || !preg_match('/^[a-f0-9]{24}$/i', $productId)) {
    exit("Invalid Product ID.");
}

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$database = $client->my_database;
$productCollection = $database->products;
$feedbackCollection = $database->feedback;

// Fetch product details
$product = $productCollection->findOne(['_id' => new ObjectId($productId)]);
if (!$product) {
    exit("Product not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'] ?? '';
    $uploadedFiles = [];

    // Validate and handle file uploads (limit to max 3 files)
    if (count($_FILES['media']['tmp_name']) > 3) {
        die("You can upload a maximum of 3 files.");
    }

    foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
            $fileName = basename($_FILES['media']['name'][$key]);
            $filePath = 'uploads/' . uniqid() . '_' . $fileName;
            $fileType = mime_content_type($tmpName);

            // Only allow images and video files
            if (preg_match('/^image\/|video\//', $fileType)) {
                if (move_uploaded_file($tmpName, $filePath)) {
                    $uploadedFiles[] = $filePath;
                }
            } else {
                die("Only images and videos are allowed.");
            }
        }
    }

    // Insert feedback into MongoDB
    $feedbackCollection->insertOne([
        'product_id' => new ObjectId($productId),
        'user_id' => new ObjectId($_SESSION['user_id']),
        'rating' => $rating,
        'comment' => $comment,
        'media' => $uploadedFiles,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    echo "<script>
        alert('Thank you for your feedback on {$product['name']}!');
        window.location.href = 'homepage.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rate Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        .feedback-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .feedback-container h2 {
            text-align: center;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 2em;
            color: #e0e0e0;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffd700;
        }
        .file-input {
            margin-top: 10px;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="feedback-container">
    <h2>Rate "<?php echo htmlspecialchars($product['name']); ?>"</h2>

    <form action="feedback.php?product_id=<?php echo htmlspecialchars($productId); ?>" method="POST" enctype="multipart/form-data">
        <!-- Star Rating -->
        <div class="star-rating">
            <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
            <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
        </div>

        <!-- Comment Box -->
        <textarea name="comment" rows="4" placeholder="Leave a comment" required></textarea>

        <!-- File Upload (Images/Videos) -->
        <input type="file" name="media[]" accept="image/*,video/*" class="file-input" multiple>

        <!-- Submit -->
        <button type="submit" class="submit-btn">Submit Feedback</button>
    </form>
</div>

</body>
</html>
