<?php
session_start();
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$feedbackCollection = $client->my_database->feedback;

// Get the feedback ID from the GET request
$feedbackId = $_GET['feedback_id'] ?? null;

if (!$feedbackId || !preg_match('/^[a-f0-9]{24}$/i', $feedbackId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Feedback ID']);
    exit;
}

// Find the feedback in the database
$feedback = $feedbackCollection->findOne(['_id' => new ObjectId($feedbackId)]);
if (!$feedback) {
    echo json_encode(['success' => false, 'message' => 'Feedback not found']);
    exit;
}

// Increment the like count for the feedback
$feedbackCollection->updateOne(
    ['_id' => new ObjectId($feedbackId)],
    ['$inc' => ['likes' => 1]]
);

// Fetch the updated like count
$updatedFeedback = $feedbackCollection->findOne(['_id' => new ObjectId($feedbackId)]);

// Return the new like count as a JSON response
echo json_encode(['success' => true, 'newLikeCount' => $updatedFeedback['likes']]);
