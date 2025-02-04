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

// Fetch all roles
$query = "SELECT * FROM roles";
$result = $conn->query($query);

// Update role details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_id = $_POST['role_id'];
    $role_name = $_POST['role_name'];
    $permissions = $_POST['permissions'];

    $updateQuery = "UPDATE roles SET role_name=?, permissions=? WHERE role_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $role_name, $permissions, $role_id);

    if ($stmt->execute()) {
        echo "<div class='message success'>Role updated successfully!</div>";
        header("refresh:2;url=manage_roles.php"); // Redirect after 2 seconds
        exit();
    } else {
        echo "<div class='message error'>Error updating role: " . $conn->error . "</div>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles - Prison Management System</title>
    <link rel="stylesheet" href="styles.css">
    
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            background-color: #2f4f4f;
            color: white;
        }

        /* Navbar */
        .navbar {
            background: #222;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #f2a900;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }

        /* Form Section */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-box {
            background: #444;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }

        .form-box h1 {
            margin-bottom: 20px;
            color: #f2a900;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            font-size: 14px;
            color: #f2a900;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-btn {
            width: 100%;
            padding: 10px;
            background: #f2a900;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-btn:hover {
            background: #d88e00;
        }

        /* Messages */
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .message.success {
            background: #4caf50;
            color: white;
        }

        .message.error {
            background: #f44336;
            color: white;
        }

        .role-list {
            margin-top: 20px;
            text-align: left;
        }

        .role-list th, .role-list td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .role-list th {
            background: #333;
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
        <h1>Manage Roles</h1>

        <!-- Display roles in a table -->
        <table class="role-list">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $conn = new mysqli($host, $user, $password, $database);
                $query = "SELECT * FROM roles";
                $result = $conn->query($query);
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['role_name']) . "</td>
                        <td>" . htmlspecialchars($row['permissions']) . "</td>
                        <td>
                            <a href='manage_roles.php?id=" . $row['role_id'] . "' class='btn-edit'>Edit</a>
                        </td>
                    </tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>

        <!-- Form to edit role -->
        <?php
        if (isset($_GET['id'])) {
            $role_id = $_GET['id'];
            $conn = new mysqli($host, $user, $password, $database);
            $query = "SELECT * FROM roles WHERE role_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $role_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $role = $result->fetch_assoc();
            $conn->close();
        ?>
        <form method="POST" action="manage_roles.php">
            <input type="hidden" name="role_id" value="<?= htmlspecialchars($role['role_id']) ?>">

            <div class="form-group">
                <label for="role_name">Role Name:</label>
                <input type="text" name="role_name" id="role_name" value="<?= htmlspecialchars($role['role_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="permissions">Permissions:</label>
                <textarea name="permissions" id="permissions" rows="4" required><?= htmlspecialchars($role['permissions']) ?></textarea>
            </div>

            <button type="submit" class="form-btn">Update Role</button>
        </form>
        <?php } ?>
    </div>
</div>

</body>
</html>
