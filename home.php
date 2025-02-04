<?php
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

// Initialize variables with default values
$total_inmates = 0;
$staff_availability = 0;
$visitors_today = 0;
$alerts = 0;

// Fetch data from the database
try {
    // Fetch total inmates
    $result = $conn->query("SELECT COUNT(*) as total FROM inmates");
    if ($result === false) {
        throw new Exception("Error fetching total inmates: " . $conn->error);
    }
    $total_inmates = $result->fetch_assoc()['total'];

    // Fetch staff availability
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE status = 'available'");
    if ($result === false) {
        throw new Exception("Error fetching staff availability: " . $conn->error);
    }
    $staff_availability = $result->fetch_assoc()['total'];

    // Fetch visitors today
    $result = $conn->query("SELECT COUNT(*) as total FROM visitors WHERE DATE(visit_date) = CURDATE()");
    if ($result === false) {
        throw new Exception("Error fetching visitors today: " . $conn->error);
    }
    $visitors_today = $result->fetch_assoc()['total'];

    // Fetch alerts
    $result = $conn->query("SELECT COUNT(*) as total FROM alters WHERE DATE(alert_date) = CURDATE()");
    if ($result === false) {
        throw new Exception("Error fetching alerts: " . $conn->error);
    }
    $alerts = $result->fetch_assoc()['total'];
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prison Management System - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Toggle Button */
        .toggle-sidebar {
            background: #ff8c42; /* Orange accent */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-sidebar:hover {
            background: #e67332; /* Darker orange on hover */
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="top-nav">
    <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
        <div class="logo">Prison Management System</div>
        <div class="user-profile">
        <a href="logout.php">🔓</a>


        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button onclick="location.href='register_inmate.php'">Register New Inmate</button>
        <button onclick="location.href='log_incident.php'">Log an Incident</button>
        <button onclick="location.href='manage_visitors.php'">Manage Visitors</button>
        <button onclick="location.href='visitor_list.php'">View Visitors</button>
        <button onclick="location.href='Alters.php'">Alters </button>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="main-content">
        <div class="container">
            <h1>Dashboard</h1>

            <!-- Display Error Message -->
            <?php if (isset($error_message)) : ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Overview Widgets -->
            <div class="widgets">
                <div class="widget">
                    <h3>Total Inmates</h3>
                    <p id="total-inmates"><?php echo $total_inmates; ?></p>
                </div>
                
                <div class="widget">
                    <h3>Visitors Today</h3>
                    <p id="visitor-stats"><?php echo $visitors_today; ?></p>
                </div>
                <div class="widget">
    <h3>Staff Availability</h3>
    <p id="staff-availability"><?php echo $staff_availability; ?></p>
</div>
<div class="widget">
    <h3>Alerts</h3>
    <p id="alerts"><?php echo $alerts; ?></p>
</div>
            </div>
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
