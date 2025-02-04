<?php
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

// Fetch users from the database
$query = "SELECT id, username, full_name, age, marital_status, contact, address, role, approval_status FROM users";
$result = $conn->query($query);

if ($result === false) {
    die("Error fetching users: " . $conn->error);
}

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List - Prison Management System</title>
    <style>
        /* General Reset */
        body, h1, h2, h3, p, ul, li {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a; /* Dark background */
            color: #f4f4f9; /* Light text */
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Top Navigation Bar */
        .top-nav {
            width: 100%;
            background-color: #333; /* Dark gray */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .top-nav .logo {
            font-size: 30px;
            font-weight: bold;
            color: #ff8c42; /* Orange accent */
        }

        .top-nav .user-profile a {
            color: white;
            text-decoration: none;
            padding: 10px 30px;
            background: #ff8c42; /* Orange accent */
            border-radius: 5px;
            transition: background 0.3s;
        }

        .top-nav .user-profile a:hover {
            background: #e67332; /* Darker orange on hover */
        }

        /* Main Content Area */
        .main-content {
            margin-left: 250px; /* Adjust for the sidebar width */
            margin-top: 70px; /* Adjust for the top-nav height */
            padding: 20px;
            flex-grow: 1;
            transition: margin-left 0.3s ease-in-out;
        }

        .main-content.expanded {
            margin-left: 0; /* Expand main content when sidebar is collapsed */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #262626; /* Dark gray */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ff8c42; /* Orange accent */
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        table th {
            background-color: #333; /* Dark gray */
            color: #ff8c42; /* Orange accent */
        }

        table tr:hover {
            background-color: #444; /* Darker gray on hover */
        }

        table td {
            color: #f4f4f9; /* Light text */
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .action-buttons button.edit {
            background: #ff8c42; /* Orange accent */
            color: white;
        }

        .action-buttons button.delete {
            background: #e74c3c; /* Red accent */
            color: white;
        }

        .action-buttons button:hover {
            opacity: 0.8;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            background-color: #262626; /* Dark gray */
            padding: 20px;
            position: fixed;
            top: 70px; /* Adjust for the height of the top-nav */
            left: 0;
            height: calc(100vh - 70px); /* Full height minus top-nav */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.collapsed {
            transform: translateX(-250px); /* Hide sidebar */
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #ff8c42; /* Orange accent */
        }

        .sidebar button {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 10px;
            border: none;
            background: #ff8c42; /* Orange accent */
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        .sidebar button:hover {
            background: #e67332; /* Darker orange on hover */
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="top-nav">
    <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
        <div class="logo">Prison Management System</div>
       
        <div class="user-profile">
            <span>Welcome, Admin</span>
            <a href="../logout.php">🔓</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Quick Actions</h2>
        <button onclick="location.href='add_user.php'">Add New User</button>
        <button onclick="location.href='manage_roles.php'">Manage Roles</button>
        <button onclick="location.href='audit_logs.php'">View Audit Logs</button>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container">
            <h1>Users List</h1>

            <table>
    <thead>
        <tr>
           
            <th>Username</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Marital Status</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Role</th>
          
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
           
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['age']) ?></td>
                <td><?= htmlspecialchars($user['marital_status']) ?></td>
                <td><?= htmlspecialchars($user['contact']) ?></td>
                <td><?= htmlspecialchars($user['address']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                
                <td class="action-buttons">
                    <!-- Keep existing action buttons -->
                    <button class="edit" onclick="location.href='edit_user.php?id=<?= $user['id'] ?>'">Edit</button>
                    <button class="delete" onclick="confirmDelete(<?= $user['id'] ?>)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </div>

    <script>
        // Function to confirm user deletion
        function confirmDelete(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                window.location.href = `delete_user.php?id=${userId}`;
            }
        }

        // Function to toggle the sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    </script>
</body>
</html>