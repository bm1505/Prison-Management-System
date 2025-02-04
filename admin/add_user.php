<?php
// Start session
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

// Initialize variables for messages
$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $full_name = trim($_POST['full_name']);
    $age = trim($_POST['age']);
    $marital_status = trim($_POST['marital_status']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $role = trim($_POST['role']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($full_name) || empty($age) || empty($marital_status) || empty($contact) || empty($address) || empty($role) || empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if the username already exists
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (  full_name, age, marital_status, contact, address, role, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("sissssss",   $full_name, $age, $marital_status, $contact, $address, $role, $username, $hashed_password);

            if ($insert_stmt->execute()) {
                $success = "User added successfully!";
                // Redirect to a page to see the users
                header("Location: users_list.php"); // Change 'users_list.php' to the page where you want to view users
                exit();
            } else {
                $error = "Error adding user: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Prison Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       /* General Styles */
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background: #2f2f2f; /* Dark gray background to reflect a prison vibe */
    color: #dcdcdc; /* Lighter gray text */
}

/* Navigation Bar */
.navbar {
    background: #333; /* Dark background for the navbar */
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

.navbar a {
    color: #dcdcdc;
    text-decoration: none;
    margin: 0 15px;
    font-size: 16px;
    transition: color 0.3s;
}

.navbar a:hover {
    color: #ffcc00; /* Yellow color on hover for a stark contrast */
}

.navbar .logo {
    font-size: 24px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* Form Container */
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 70px); /* Adjust for navbar height */
    padding: 20px;
    background: #232323; /* Dark background for the form section */
}

/* Form Box */
.form-box {
    background: #1d1d1d; /* Dark gray background for the form */
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    width: 400px;
    text-align: center;
    border: 2px solid #444; /* Subtle border for more rigidity */
}

.form-box h1 {
    margin-bottom: 20px;
    color: #ffcc00; /* Yellow text for titles to match the navbar hover */
    font-size: 22px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Form Group */
.form-group {
    margin-bottom: 15px;
    text-align: left;
}

.form-group label {
    font-size: 14px;
    color: #dcdcdc;
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #444; /* Border color to match the prison theme */
    border-radius: 5px;
    background: #2c2c2c; /* Dark background for input fields */
    color: #dcdcdc;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
    height: 80px;
}

.form-group select {
    cursor: pointer;
    background: #2c2c2c; /* Same dark background for select */
}

/* Submit Button */
.form-btn {
    width: 100%;
    padding: 12px;
    background: #ffcc00; /* Bright yellow for the button */
    border: none;
    border-radius: 5px;
    color: #333;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
}

.form-btn:hover {
    background: #d4a600; /* Darker yellow on hover */
}

/* Message Styles */
.message {
    margin-bottom: 20px;
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
    border: 1px solid #444;
}

.message.success {
    background: #4caf50; /* Green background for success */
    color: #ffffff;
}

.message.error {
    background: #f44336; /* Red background for errors */
    color: #ffffff;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-box {
        width: 90%;
    }

    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar a {
        margin: 5px 0;
    }
}

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="logo">Prison Management System</div>
        <div>
        <a href="admin.php">Dashboard</a>
        <a href="users_list.php">Users</a>
        <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <div class="form-box">
            <h1>Add Prison Guide</h1>

            <!-- Display success or error messages -->
            <?php if (!empty($success)) { ?>
                <div class="message success"><?= $success ?></div>
            <?php } ?>
            <?php if (!empty($error)) { ?>
                <div class="message error"><?= $error ?></div>
            <?php } ?>

            <form method="POST">
                <!-- Username Field -->
                <!-- Full Name Field -->
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter full name" required>
                </div>

                <!-- Age Field -->
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" placeholder="Enter age" required>
                </div>

                <!-- Marital Status Field -->
                <div class="form-group">
                    <label for="marital_status">Marital Status:</label>
                    <select id="marital_status" name="marital_status" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>

                <!-- Contact Field -->
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" placeholder="Enter contact number" required>
                </div>

                <!-- Address Field -->
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" placeholder="Enter address" required></textarea>
                </div>
                <!-- Role Field -->
<div class="form-group">
    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="">Select Role</option>
        <option value="Admin">Admin</option>
        <option value="Staff">Staff</option>
        <option value="Warden">Warden</option>
        <option value="Officer">Officer</option>
        <option value="Guard">Guard</option>
        <option value="Medical">Medical</option>
        <option value="Counselor">Counselor</option>
        <option value="Inmate">Inmate</option>
        <option value="Visitor">Visitor</option>
        <option value="Clerk">Clerk</option>
        <option value="Chaplain">Chaplain</option>
        <option value="Cook">Cook</option>
        <option value="Maintenance">Maintenance</option>
        <option value="Trainer">Trainer</option>
    </select>
</div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter a username" required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter a password" required>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="form-btn">Add User</button>
            </form>
        </div>
    </div>
</body>
</html>