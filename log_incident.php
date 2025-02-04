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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $incident_type = $_POST['incident_type'];
    $description = $_POST['description'];
    $reported_by = $_POST['reported_by'];
    $date_reported = date('Y-m-d H:i:s'); // Current date and time

    // Insert incident into the database
    $sql = "INSERT INTO incidents (incident_type, description, reported_by, date_reported) 
            VALUES ('$incident_type', '$description', '$reported_by', '$date_reported')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Incident logged successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Incident - Prison Management System</title>
    <style>
        /* General Reset */
        body, h1, h2, h3, p, ul, li, form, input, textarea {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a; /* Dark background */
            color: #f4f4f9; /* Light text */
        }

        /* Top Navigation Bar */
        .top-nav {
            width: 100%;
            background-color: #333; /* Dark gray */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .top-nav .logo {
            font-size: 20px;
            font-weight: bold;
            color: #ff8c42; /* Orange accent */
        }

        .top-nav .user-profile {
            display: flex;
            align-items: center;
        }

        .top-nav .user-profile a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            background: #ff8c42; /* Orange accent */
            border-radius: 5px;
            transition: background 0.3s;
        }

        .top-nav .user-profile a:hover {
            background: #e67332; /* Darker orange on hover */
        }

        /* Main Content Container */
        .container {
            max-width: 600px;
            margin: 80px auto 20px auto; /* Adjust for the height of the fixed nav bar */
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

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #ff8c42; /* Orange accent */
        }

        input, textarea, select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333; /* Dark gray */
            color: #f4f4f9; /* Light text */
            font-size: 16px;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #ff8c42; /* Orange accent */
            outline: none;
        }

        button {
            padding: 10px 15px;
            border: none;
            background: #ff8c42; /* Orange accent */
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        button:hover {
            background: #e67332; /* Darker orange on hover */
            transform: translateY(-2px);
        }

        /* Messages */
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        .error {
            background-color: #f44336; /* Red */
            color: white;
        }
        .top-nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: #1a1a1a;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            z-index: 1000;
            border-bottom: 2px solid #ff6347;
        }

       
        .toggle-sidebar {
            font-size: 24px;
            background: none;
            border: none;
            color: #f4f4f9;
            cursor: pointer;
            transition: color 0.3s;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            color: #ff6347;
            font-size: 1.5em;
            flex-grow: 1;
        }

        .user-profile a {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            background: #ff6347;
            transition: background 0.3s;
        }

        .user-profile a:hover {
            background: #ff4500;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 60px;
            bottom: 0;
            width: 250px;
            background: #1a1a1a;
            padding: 20px;
            transform: translateX(0);
            transition: transform 0.3s;
            z-index: 999;
        }

        .sidebar button {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .sidebar button:hover {
            background: #ff6347;
            border-color: #ff4500;
        }

       
        /* Form Styles */
       
        .back-home-btn:hover {
            background: #ff6347;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }

            .container {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="top-nav">
        <div class="logo">Prison Management System</div>
        <div class="user-profile">
            <a href="home.php">Back</a>
        </div>
    </div>
<!-- Top Navigation -->
<div class="top-nav">
      
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
    <!-- Main Content Area -->
    <div class="container">
        <h1>Log Incident</h1>

        <!-- Display Messages -->
        <?php if (isset($success_message)) : ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)) : ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Incident Log Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="incident_type">Incident Type</label>
            <select id="incident_type" name="incident_type" required>
                <option value="Fight">Fight</option>
                <option value="Escape Attempt">Escape Attempt</option>
                <option value="Medical Emergency">Medical Emergency</option>
                <option value="Theft">Theft</option>
                <option value="Other">Other</option>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required></textarea>

            <label for="reported_by">Reported By</label>
            <input type="text" id="reported_by" name="reported_by" required>

            <button type="submit">Log Incident</button>
        </form>
    </div>
</body>

<script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.querySelector('.container');
            sidebar.classList.toggle('active');
            if (window.innerWidth <= 768) {
                container.style.marginLeft = sidebar.classList.contains('active') ? '250px' : '0';
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.toggle-sidebar');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('active');
                document.querySelector('.container').style.marginLeft = '0';
            }
        });

        // Responsive sidebar adjustment
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.remove('active');
                document.querySelector('.container').style.marginLeft = '250px';
            } else {
                document.querySelector('.container').style.marginLeft = '0';
            }
        });
    </script>
</html>