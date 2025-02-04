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

// Initialize variables
$total_users = 0;
$total_roles = 0;
$pending_approvals = 0;

// Get total users count
$total_users_query = "SELECT COUNT(*) AS total_users FROM users";
$total_users_result = $conn->query($total_users_query);

if ($total_users_result === false) {
    die("Error in total users query: " . $conn->error);
} else {
    $total_users = $total_users_result->fetch_assoc()['total_users'];
}

// Get total roles count
$roles_query = "SELECT COUNT(*) AS total_roles FROM roles"; // Assuming you have a roles table
$roles_result = $conn->query($roles_query);

if ($roles_result === false) {
    die("Error in roles query: " . $conn->error);
} else {
    $total_roles = $roles_result->fetch_assoc()['total_roles'];
}

// Get pending approvals count
$pending_approvals_query = "SELECT COUNT(*) AS pending_approvals FROM users WHERE approval_status = 'pending'"; // Adjust query for pending approvals
$pending_approvals_result = $conn->query($pending_approvals_query);

if ($pending_approvals_result === false) {
    die("Error in pending approvals query: " . $conn->error);
} else {
    $pending_approvals = $pending_approvals_result->fetch_assoc()['pending_approvals'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Prison Management System</title>
    <style>
        /* General Reset */
        body, h1, h2, h3, p, ul, li {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color:rgb(26, 26, 26); /* Dark background */
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

        /* Widgets */
        .widgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .widget {
            background: #333; /* Dark gray */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .widget h3 {
            margin-bottom: 10px;
            color: #ff8c42; /* Orange accent */
        }

        .widget p {
            font-size: 18px;
            font-weight: bold;
            color: #f4f4f9; /* Light text */
        }
        .toggle-sidebar {
            background: #ff8c42; /* Orange accent */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        /* Quick Actions */
        .quick-actions {
            margin-top: 30px;
        }

        .quick-actions h2 {
            margin-bottom: 15px;
            color: #ff8c42; /* Orange accent */
        }

        .quick-actions button {
            background: #ff8c42; /* Orange accent */
            color: white;
            border: none;
            padding: 10px 20px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .quick-actions button:hover {
            background: #e67332; /* Darker orange on hover */
        }

        @media (max-width: 600px) {
            .widgets {
                grid-template-columns: 1fr;
            }

            .quick-actions button {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="top-nav">
    <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
        <div class="logo">Prison Management System</div>
        <div class="user-profile">
            <span>👑</span>
            <a href="../logout.php">🔓</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button onclick="location.href='add_user.php'">Add New User</button>
        <button onclick="location.href='manage_roles.php'">Manage Roles</button>
        <button onclick="location.href='audit_logs.php'">View Audit Logs</button>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container">
            <h1>Admin Panel</h1>

            <!-- Admin Widgets -->
            <div class="widgets">
    <div class="widget">
        <h3>Total Users 👥</h3>
        <p id="total-users"><?php echo $total_users; ?></p>
    </div>
    <div class="widget">
        <h3>Roles Defined 🔑</h3>
        <p id="roles-defined"><?php echo $total_roles; ?></p>
    </div>
    <div class="widget">
        <h3>Pending Approvals ⏳</h3>
        <p id="pending-approvals"><?php echo $pending_approvals; ?></p>
    </div>
</div>


            <!-- Quick Actions -->
            
        </div>
    </div>

    <script>
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