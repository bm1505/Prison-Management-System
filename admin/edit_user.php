<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "prison_db";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch user details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $marital_status = $_POST['marital_status'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $approval_status = $_POST['approval_status'];

    $updateQuery = "UPDATE users SET username=?, full_name=?, age=?, marital_status=?, contact=?, address=?, role=?, approval_status=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssisssssi", $username, $full_name, $age, $marital_status, $contact, $address, $role, $approval_status, $id);
    
    if ($stmt->execute()) {
        echo "<div class='message success'>User updated successfully!</div>";
        header("refresh:2;url=users.php"); // Redirect after 2 seconds
        exit();
    } else {
        echo "<div class='message error'>Error updating user: " . $conn->error . "</div>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Prison Management System</title>
    <link rel="stylesheet" href="styles.css">
    
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #2C2C2C;
            color: #E0E0E0;
        }

        /* Navigation Bar */
        .navbar {
            background: #000;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .navbar a {
            color: #D2691E;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #E07A5F;
        }

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }

        /* Form Container */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-box {
            background: #3B3B3B;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            width: 400px;
            text-align: center;
        }

        .form-box h1 {
            margin-bottom: 20px;
            color: #D2691E;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            font-size: 14px;
            color: #CCCCCC;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background: #555;
            color: white;
            font-size: 14px;
        }

        .form-btn {
            width: 100%;
            padding: 10px;
            background: #D2691E;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-btn:hover {
            background: #E07A5F;
        }

        /* Messages */
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .message.success {
            background: #6B8E23;
            color: white;
        }

        .message.error {
            background: #B22222;
            color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">Prison Management System</div>
    <div>
        <a href="admin.php">Dashboard</a>
        <a href="users_list.php">Users</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<!-- Form Section -->
<div class="form-container">
    <div class="form-box">
        <h1>Edit User</h1>
        <form method="POST" action="edit_user.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>" required>
            </div>

            <div class="form-group">
                <label>Marital Status:</label>
                <input type="text" name="marital_status" value="<?= htmlspecialchars($user['marital_status']) ?>" required>
            </div>

            <div class="form-group">
                <label>Contact:</label>
                <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>" required>
            </div>

            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
            </div>

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

            

            <button type="submit" class="form-btn">Update User</button>
        </form>
    </div>
</div>

</body>
</html>
