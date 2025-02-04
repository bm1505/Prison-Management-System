<?php
session_start();

// Database connection with error handling
$conn = new mysqli('localhost', 'root', '', 'prison_db');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $name = $conn->real_escape_string(trim($_POST['name']));
    $age = intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $crime = $conn->real_escape_string(trim($_POST['crime']));
    $sentence = $conn->real_escape_string(trim($_POST['sentence']));

    // Use prepared statement for security
    $stmt = $conn->prepare("INSERT INTO inmates (name, age, gender, crime, sentence) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $name, $age, $gender, $crime, $sentence);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Inmate registered successfully!";
        header("Location: list_inmates.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Inmate - Prison Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Orbitron&display=swap" rel="stylesheet">
    <style>
        /* Enhanced Layout with Sidebar */
        body {
            margin: 0;
            padding-top: 60px;
            font-family: 'Roboto', sans-serif;
            background-color: #222;
            color: #f4f4f4;
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

        .container {
            margin-left: 250px;
            padding: 30px;
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

        .back-home-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #444;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

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
    <!-- Top Navigation -->
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
        <form action="register_inmate.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter inmate's full name">
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required placeholder="Enter inmate's age">
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="crime">Crime Committed</label>
                <input type="text" id="crime" name="crime" required placeholder="Enter the crime committed">
            </div>

            <div class="form-group">
                <label for="sentence">Sentence</label>
                <input type="text" id="sentence" name="sentence" required placeholder="Enter the sentence duration">
            </div>

            <div class="form-group">
                <button type="submit">Register Inmate</button>
            </div>
        </form>

      
    </div>

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
</body>
</html>