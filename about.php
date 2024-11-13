<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Navigation Bar */
        .navbar {
            background-color: #ff5722;
            padding: 15px;
            text-align: center;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            margin: 0 15px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Container */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Section Styling */
        .section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .section-title {
            text-align: center;
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
        }

        /* Card Styling */
        .card {
            flex: 1 1 45%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            font-size: 1.2em;
            color: #ff5722;
            margin-bottom: 10px;
        }

        /* Our Way Section */
        .our-way {
            text-align: center;
            margin-top: 40px;
        }

        .way-cards {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            flex-wrap: wrap;
        }

        .way-card {
            flex: 1 1 30%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .way-card img {
            width: 100%;
            max-width: 150px;
            margin-bottom: 15px;
        }

        .way-card h4 {
            font-size: 1.2em;
            color: #ff5722;
            margin-bottom: 10px;
        }

        .way-card p {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <a href="homepage.php">Home</a> <!-- Link to your homepage -->
    <a href="about.php">About Us</a> <!-- Current page link (optional) -->
</div>

<div class="container">

    <!-- Purpose and Positioning Section -->
    <div class="section-title">About Us</div>
    <div class="section">
        <div class="card">
            <h3>Our Purpose</h3>
            <p>We believe in the transformative power of technology and want to change the world for the better by providing a platform to connect buyers and sellers within one community.</p>
        </div>
        <div class="card">
            <h3>Our Positioning</h3>
            <p>To Internet users across the region, Shopee offers a one-stop online shopping experience that provides a wide selection of products, a social community for exploration, and seamless fulfillment services.</p>
        </div>
    </div>

    <!-- Our Way Section -->
    <div class="our-way">
        <h2>Our Way</h2>
        <p>To define who we are - how we talk, behave or react to any given situation - in essence, we are Simple, Happy and Together. These key attributes are visible at every step of the Shopee journey.</p>
    </div>
    <div class="way-cards">
        <div class="way-card">
            <img src="simple.png" alt="Simple Icon">
            <h4>Simple</h4>
            <p>We believe in simplicity and integrity, ensuring a life that’s honest, down to earth, and true to self.</p>
        </div>
        <div class="way-card">
            <img src="happy.png" alt="Happy Icon">
            <h4>Happy</h4>
            <p>We are friendly, fun-loving, and bursting with heaps of energy, spreading the joy with everyone we meet.</p>
        </div>
        <div class="way-card">
            <img src="together.png" alt="Together Icon">
            <h4>Together</h4>
            <p>We enjoy spending quality time together while shopping online with friends and family – doing the things we love as one big unit.</p>
        </div>
    </div>

</div>

</body>
</html>
