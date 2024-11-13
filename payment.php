<?php
session_start();

// Check if the user has items in the cart
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. Please add products before proceeding to payment.</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f8f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2em;
            color: #333;
        }

        .payment-options {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .payment-option {
            width: 45%;
            padding: 20px;
            text-align: center;
            background-color: #f0f0f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .payment-option img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .payment-option p {
            font-size: 1.2em;
            color: #333;
            margin: 0;
        }

        .payment-option.selected {
            border: 2px solid #27ae60;
            background-color: #ecfdf3;
        }

        .phone-input {
            display: none;
            margin: 20px 0;
            text-align: center;
        }

        .phone-input input {
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        .next-button {
            display: block;
            width: 100%;
            margin-top: 30px;
            background-color: #27ae60;
            color: white;
            padding: 12px 20px;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .next-button:hover {
            background-color: #2ecc71;
        }

        /* Popup styling */
        #thankYouPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
        }
        
        #thankYouPopup p {
            font-size: 1.2em;
            color: #27ae60;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Choose Your Payment Method</h1>
    </div>

    <div class="payment-options">
        <!-- Google Payment Online -->
        <div class="payment-option" id="google" onclick="selectPaymentOption('google')">
            <img src="./uploads/gpay.jpg" alt="Google Payment Online">
            <p>Google Payment Online</p>
        </div>

        <!-- GCash -->
        <div class="payment-option" id="gcash" onclick="selectPaymentOption('gcash')">
            <img src="./uploads/Gcashlogo.png" alt="GCash">
            <p>GCash</p>
        </div>
    </div>

    <div id="phoneInput" class="phone-input">
        <p>Please enter your phone number:</p>
        <input type="text" id="phoneNumber" name="phone_number" placeholder="09XXXXXXXXX" required>
    </div>

    <form onsubmit="showThankYouPopup(event)">
        <input type="hidden" id="selected-payment" name="payment_method" value="">

        <button type="submit" class="next-button" id="place-order-btn" disabled>Complete Payment</button>
    </form>
</div>

<!-- Thank You Popup -->
<div id="thankYouPopup">
    <p>Thank you for buying products from Porma Hub!</p>
</div>

<script>
    function selectPaymentOption(method) {
        // Reset all options
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('selected');
        });

        // Highlight the selected option
        document.getElementById(method).classList.add('selected');
        document.getElementById('selected-payment').value = method;
        document.getElementById('place-order-btn').disabled = false;

        // Show phone input when a payment option is selected
        document.getElementById('phoneInput').style.display = 'block';
    }

    function showThankYouPopup(event) {
        event.preventDefault();  // Prevent form submission

        // Check if phone number is provided
        const phoneNumber = document.getElementById('phoneNumber').value;
        if (!phoneNumber) {
            alert('Please enter your phone number to complete the payment.');
            return;
        }

        // Display thank you message
        document.getElementById('thankYouPopup').style.display = 'block';

        // Hide the form and disable further interaction
        document.querySelector('form').style.display = 'none';
        setTimeout(() => {
            document.getElementById('thankYouPopup').style.display = 'none';
            // Redirect to homepage after the popup disappears
            window.location.href = 'homepage.php';  // Change this to your homepage URL if needed
        }, 3000); // Popup stays for 3 seconds before redirecting
    }
</script>

</body>
</html>
