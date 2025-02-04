<?php
session_start();

// Database connection with error handling
$conn = new mysqli('localhost', 'root', '', 'prison_db');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $setting_name = mysqli_real_escape_string($conn, $_POST['setting_name']);
    $setting_value = mysqli_real_escape_string($conn, $_POST['setting_value']);
    
    // Update settings in the database (assuming a table `settings` exists)
    $sql = "UPDATE settings SET value='$setting_value' WHERE name='$setting_name'";
    if ($conn->query($sql) === TRUE) {
        $message = "Setting updated successfully!";
    } else {
        $message = "Error updating setting: " . $conn->error;
    }
}

// Fetch current settings
$sql = "SELECT * FROM settings";
$result = $conn->query($sql);

if ($result) {
    // Only attempt to fetch rows if the query is successful
    $settings = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['name']] = $row['value'];
        }
    }
} else {
    // Query failed, output error message
    $message = "Error executing query: " . $conn->error;
    $settings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="alters.php">Alters</a></li>
                    <li><a href="settings.php" class="active">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Settings</h1>
            
            <!-- Display messages -->
            <?php if (isset($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            
            <!-- Settings Form -->
            <form action="settings.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Setting Name</th>
                            <th>Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($settings)): ?>
                            <?php foreach ($settings as $setting_name => $setting_value): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($setting_name); ?></td>
                                    <td>
                                        <input type="text" name="setting_value" value="<?php echo htmlspecialchars($setting_value); ?>" required>
                                    </td>
                                    <td>
                                        <button type="submit" name="setting_name" value="<?php echo $setting_name; ?>">Update</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No settings available or an error occurred.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </main>
    </div>
</body>
<style>
    /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    display: flex;
}

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background-color: #333;
    color: #fff;
    height: 100vh;
    padding: 20px;
}

.sidebar nav ul {
    list-style-type: none;
    padding: 0;
}

.sidebar nav ul li {
    margin-bottom: 10px;
}

.sidebar nav ul li a {
    color: #fff;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-radius: 4px;
}

.sidebar nav ul li a:hover {
    background-color: #555;
}

.sidebar nav ul li a.active {
    background-color: #4CAF50;
}

/* Main Content Styles */
.main-content {
    flex-grow: 1;
    padding: 20px;
    background-color: #fff;
}

.main-content h1 {
    margin-top: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #f4f4f4;
}

table tr:hover {
    background-color: #f9f9f9;
}

table input {
    padding: 5px;
    width: 100%;
}

table button {
    padding: 5px 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

table button:hover {
    background-color: #45a049;
}

/* Message Styles */
.message {
    color: green;
    font-weight: bold;
}
</style>
</html>
