<?php
// Start session to manage login
session_start();

// Database connection
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$database = "prison_db";

$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Default admin credentials
$default_username = "admin";
$default_password = "admin123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the provided credentials match the default admin credentials
    if ($username === $default_username && $password === $default_password) {
        // Redirect to admin panel
        $_SESSION['username'] = $username;
        header("Location: admin/admin.php");
        exit();
    } else {
        // Check against database for user credentials
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch user data from the database
            $user = $result->fetch_assoc();

            // Verify the password using password_verify
            if (password_verify($password, $user['password'])) {
                // Valid user login
                $_SESSION['username'] = $username;
                header("Location: home.php");
                exit();
            } else {
                // Invalid password
                $error = "Invalid username or password.";
            }
        } else {
            // Invalid username
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Prison Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login</h1>
            <?php if (isset($error)) { ?>
                <p style="color: red;"><?= $error ?></p>
            <?php } ?>
            <form method="POST">
                <!-- Username Field -->
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/prison_background.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7); /* Dark overlay for better contrast */
        }

        .login-box {
            background: #2c3e50; /* Dark blue-gray background */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 320px;
        }

        h1 {
            margin-bottom: 20px;
            color: #ecf0f1; /* Light gray for text */
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            font-size: 14px;
            color: #bdc3c7; /* Light gray for labels */
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #34495e; /* Darker border */
            border-radius: 5px;
            font-size: 14px;
            background: #34495e; /* Darker input background */
            color: #ecf0f1; /* Light text color */
        }

        input[type="text"]::placeholder, input[type="password"]::placeholder {
            color: #95a5a6; /* Light gray placeholder text */
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #e74c3c; /* Red accent for the button */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background: #c0392b; /* Darker red on hover */
        }

        /* Error message styling */
        p {
            color: #ff4444; /* Red for error messages */
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</body>
</html>
