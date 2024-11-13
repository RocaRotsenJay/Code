<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Porma Hub - Login/Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        h1 {
            color: #ff5722;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .form-container h2 {
            color: #ff5722;
            margin-bottom: 20px;
        }
        .form-container input[type="text"],
        .form-container input[type="password"],
        .form-container input[type="email"],
        .form-container input[type="date"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #ff5722;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #e64a19;
        }
        .form-toggle {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
        .form-toggle a {
            color: #ff5722;
            text-decoration: none;
        }
        .form-toggle a:hover {
            text-decoration: underline;
        }
        .error, .success {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .error { color: red; }
        .success { color: green; }
        /* New styling for "Login as Admin" */
        .admin-link {
            margin-top: 10px;
            font-size: 14px;
        }
        .admin-link a {
            color: #ff5722;
            text-decoration: none;
        }
        .admin-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Porma Hub Store</h1>
        <div class="form-container">
            <?php
            session_start();
            require 'vendor/autoload.php';

            $client = new MongoDB\Client("mongodb://localhost:27017");
            $database = $client->my_database;
            $collection = $database->users;

            if (isset($_POST['submit'])) {
                if ($_POST['submit'] === 'login') {
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $user = $collection->findOne(['username' => $username]);
                    if ($user && password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['_id']->__toString();
                        $_SESSION['logged_in'] = true;
                        header("Location: homepage.php");
                        exit;
                    } else {
                        $loginError = "Invalid username or password.";
                    }
                } elseif ($_POST['submit'] === 'register') {
                    $username = $_POST['username'];
                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $gender = $_POST['gender'];
                    $dob = $_POST['dob'];
                    $address = $_POST['address'];
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    $existingUser = $collection->findOne(['username' => $username]);
                    if ($existingUser) {
                        $registerError = "Username already exists.";
                    } else {
                        $insertResult = $collection->insertOne([
                            'username' => $username,
                            'name' => $name,
                            'email' => $email,
                            'gender' => $gender,
                            'dob' => $dob,
                            'address' => $address,
                            'password' => $password
                        ]);
                        if ($insertResult->getInsertedId()) {
                            $registerSuccess = "Registration successful! Please login.";
                        } else {
                            $registerError = "Registration failed. Please try again.";
                        }
                    }
                }
            }
            ?>

            <div id="loginForm">
                <h2>Login</h2>
                <?php if (isset($loginError)): ?>
                    <p class="error"><?php echo $loginError; ?></p>
                <?php endif; ?>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="hidden" name="submit" value="login">
                    <button type="submit">Login</button>
                </form>
                <div class="admin-link">
                    <a href="admin.php">Login as Admin</a>
                </div>
            </div>

            <div id="registerForm" style="display: none;">
                <h2>Register</h2>
                <?php if (isset($registerError)): ?>
                    <p class="error"><?php echo $registerError; ?></p>
                <?php endif; ?>
                <?php if (isset($registerSuccess)): ?>
                    <p class="success"><?php echo $registerSuccess; ?></p>
                <?php endif; ?>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="date" name="dob" required>
                    <input type="text" name="address" placeholder="Address" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="hidden" name="submit" value="register">
                    <button type="submit">Register</button>
                </form>
            </div>

            <p class="form-toggle" id="toggleRegister">Don't have an account? <a href="#" onclick="toggleRegisterForm()">Sign Up</a></p>
        </div>
    </div>

    <script>
        function toggleRegisterForm() {
            var registerForm = document.getElementById("registerForm");
            var loginForm = document.getElementById("loginForm");
            if (registerForm.style.display === "none") {
                registerForm.style.display = "block";
                loginForm.style.display = "none";
                document.getElementById("toggleRegister").innerHTML = 'Already have an account? <a href="#" onclick="toggleRegisterForm()">Login</a>';
            } else {
                registerForm.style.display = "none";
                loginForm.style.display = "block";
                document.getElementById("toggleRegister").innerHTML = 'Don\'t have an account? <a href="#" onclick="toggleRegisterForm()">Sign Up</a>';
            }
        }
    </script>
</body>
</html>
