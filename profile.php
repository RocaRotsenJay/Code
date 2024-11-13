<?php
session_start();
require 'vendor/autoload.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

// Connect to MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->my_database;
$collection = $database->users;

// Fetch user data using session-stored user ID
$userId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
$user = $collection->findOne(['_id' => $userId]);

if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Profile</title>
<link rel="stylesheet" href="style.css">
<style>
/* Profile container styling */
.profile-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
h2 {
    color: #333;
    text-align: center;
    font-family: 'Arial', sans-serif;
    margin-bottom: 20px;
}
.profile-item {
    font-size: 18px;
    padding: 12px 0;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    font-family: 'Roboto', sans-serif;
}
.profile-item label {
    font-weight: bold;
    color: #555;
    flex: 1;
}
.profile-item span {
    flex: 2;
    color: #333;
    word-wrap: break-word;
}
#map {
    width: 100%;
    height: 400px;
    margin-top: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Navigation bar styling */
.navbar {
    background-color: #ff5722;
    padding: 12px;
    text-align: center;
    font-family: 'Arial', sans-serif;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.navbar a {
    color: white;
    margin: 0 20px;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
}
.navbar a:hover {
    color: #f1f1f1;
}
.logout-btn {
    display: block;
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background-color: #ff5722;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 18px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}
.logout-btn:hover {
    background-color: #e64a19;
}
</style>
</head>
<body>

<!-- Navigation Bar with "My Purchases" Link -->
<div class="navbar">
    <a href="profile.php">Profile</a>
    <a href="my_purchases.php">My Purchases</a>
    <a href="logout.php">Logout</a>
</div>

<div class="profile-container">
    <h2>User Profile</h2>

    <!-- Display User Profile Information -->
    <div class="profile-item"><label>Username:</label> <span><?php echo htmlspecialchars($user['username']); ?></span></div>
    <div class="profile-item"><label>Full Name:</label> <span><?php echo htmlspecialchars($user['name']); ?></span></div>
    <div class="profile-item"><label>Email:</label> <span><?php echo htmlspecialchars($user['email']); ?></span></div>
    <div class="profile-item"><label>Gender:</label> <span><?php echo htmlspecialchars($user['gender']); ?></span></div>
    <div class="profile-item"><label>Date of Birth:</label> <span><?php echo htmlspecialchars($user['dob']); ?></span></div>

    <!-- Address Field with Google Maps -->
    <div class="profile-item">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" placeholder="Enter your address" readonly>
    </div>

    <!-- Map Container -->
    <div id="map"></div>
</div>

<!-- Load Google Maps API -->
<!-- Load Google Maps API with the callback function -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initializeMap&libraries=places" async defer></script>

<script>
    
    let map;
let geocoder;

function initializeMap() {
    geocoder = new google.maps.Geocoder();

    // Create the map centered at a default location (if no address found).
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: -34.397, lng: 150.644 } // Default location in case no address is available
    });

    // Fetch the address value from the input field
    const address = document.getElementById('address').value;

    if (address) {
        // Geocode the address to get the coordinates
        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status === 'OK') {
                // If the address is valid, set the map's center to the location
                map.setCenter(results[0].geometry.location);

                // Place a marker on the map
                new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            } else {
                // If the address is invalid or can't be geocoded, show an alert
                alert('Could not display location on map: ' + status);
            }
        });
    } else {
        // If no address, hide the map container
        document.getElementById('map').style.display = 'none';
    }
}

// Initialize the map as soon as the page loads
window.onload = initializeMap;

</script>
</body>
</html>
