<?php 
// Start session
session_start();

// Redirect to login if not authenticated
// Add any necessary authentication code here

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "prison_db";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error_message = "";
$success_message = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_visitor'])) {
        // Add new visitor
        $name = $_POST['name'];
        $inmate_id = $_POST['inmate_id'];
        $visit_date = $_POST['visit_date'];
        $relationship = $_POST['relationship'];
        $contact_number = $_POST['contact_number'];

        $query = "INSERT INTO visitors (name, inmate_id, visit_date, relationship, contact_number) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisss", $name, $inmate_id, $visit_date, $relationship, $contact_number);

        if ($stmt->execute()) {
            $success_message = "Visitor added successfully!";
            // Redirect to visitor_list.php after successful insert
            header("Location: visitor_list.php");
            exit();
        } else {
            $error_message = "Error adding visitor: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['update_visitor'])) {
        // Update visitor
        $id = $_POST['id'];
        $name = $_POST['name'];
        $inmate_id = $_POST['inmate_id'];
        $visit_date = $_POST['visit_date'];
        $relationship = $_POST['relationship'];
        $contact_number = $_POST['contact_number'];

        $query = "UPDATE visitors SET name=?, inmate_id=?, visit_date=?, relationship=?, contact_number=? 
                  WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisssi", $name, $inmate_id, $visit_date, $relationship, $contact_number, $id);

        if ($stmt->execute()) {
            $success_message = "Visitor updated successfully!";
        } else {
            $error_message = "Error updating visitor: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_GET['delete_id'])) {
        // Delete visitor
        $id = $_GET['delete_id'];

        $query = "DELETE FROM visitors WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $success_message = "Visitor deleted successfully!";
        } else {
            $error_message = "Error deleting visitor: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all visitors
$query = "SELECT visitors.*, inmates.name AS inmate_name 
          FROM visitors 
          INNER JOIN inmates ON visitors.inmate_id = inmates.id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Visitors - Prison Management System</title>
    <style>
        /* General Reset */
        body, h1, h2, h3, p, ul, li {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a; /* Dark background */
            color: #f4f4f9; /* Light text */
            line-height: 1.6;
        }

        /* Top Navigation Bar */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333; /* Dark gray */
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .toggle-sidebar {
            font-size: 24px;
            background: none;
            border: none;
            color: #f4f4f9;
            cursor: pointer;
            transition: color 0.3s;
        }

        .toggle-sidebar:hover {
            color: #ff8c42; /* Orange accent */
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff8c42; /* Orange accent */
        }

        .user-profile a {
            color: #f4f4f9;
            text-decoration: none;
            font-size: 20px;
            transition: color 0.3s;
        }

        .user-profile a:hover {
            color: #ff8c42; /* Orange accent */
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 70px; /* Adjust for top-nav height */
            left: -250px;
            width: 250px;
            height: calc(100vh - 70px);
            background-color: #262626; /* Dark gray */
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
            transition: left 0.3s ease;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background-color: #333; /* Dark gray */
            border: none;
            color: #f4f4f9;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .sidebar button:hover {
            background-color: #444; /* Lighter gray */
            transform: translateX(5px);
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 100px auto 20px; /* Adjust for top-nav */
            padding: 20px;
            background-color: #262626; /* Dark gray */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #ff8c42; /* Orange accent */
        }

        h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #f4f4f9;
        }

        .container {
            margin-left: 100px;
            padding: 10px;
            transition: margin 0.3s;
        }

        /* Form Styles */
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #333;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #f4f4f4;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background: #444;
            color: #f4f4f4;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #ff6347;
            outline: none;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background: #ff6347;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-group button:hover {
            background: #ff4500;
        }


        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-primary {
            background-color: #ff8c42; /* Orange accent */
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #e67332; /* Darker orange */
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #dc3545; /* Red */
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c82333; /* Darker red */
            transform: translateY(-2px);
        }

        /* Messages */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
        }

        .success {
            background-color: #28a745; /* Green */
            color: #fff;
        }

        .error {
            background-color: #dc3545; /* Red */
            color: #fff;
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <div class="top-nav">
        <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
        <div class="logo">Prison Management System</div>
        <div class="user-profile">
            <a href="logout.php">🔓 Logout</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button onclick="location.href='register_inmate.php'">Register New Inmate</button>
        <button onclick="location.href='log_incident.php'">Log an Incident</button>
        <button onclick="location.href='manage_visitors.php'">Manage Visitors</button>
        <button onclick="location.href='visitor_list.php'">View Visitors</button>
    </div>

    <!-- Main Content -->
    <div class="container">
        

        <!-- Display Messages -->
        <?php if (!empty($success_message)) : ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)) : ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Visitor Form -->
        <h2><?php echo isset($_GET['edit_id']) ? 'Edit Visitor' : ''; ?></h2>
        <form method="POST" action="">
            <?php if (isset($_GET['edit_id'])) : ?>
                <input type="hidden" name="id" value="<?php echo $_GET['edit_id']; ?>">
            <?php endif; ?>
            <div class="form-group">
            <h1>Manage Visitors</h1>
                <label for="name">Visitor Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="inmate_id">Inmate:</label>
                <select id="inmate_id" name="inmate_id" required>
                    <?php
                    $inmates_query = "SELECT id, name FROM inmates";
                    $inmates_result = $conn->query($inmates_query);
                    while ($row = $inmates_result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="visit_date">Visit Date:</label>
                <input type="datetime-local" id="visit_date" name="visit_date" required>
            </div>
            <div class="form-group">
                <label for="relationship">Relationship:</label>
                <input type="text" id="relationship" name="relationship" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" required>
            </div>
            <button type="submit" name="<?php echo isset($_GET['edit_id']) ? 'update_visitor' : 'add_visitor'; ?>" class="btn btn-primary">
                <?php echo isset($_GET['edit_id']) ? 'Update Visitor' : 'Add Visitor'; ?>
            </button>
        </form>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("open");
        }
    </script>
</body>
</html>