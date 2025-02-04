<?php
// Start the session (if needed)
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "prison_db"; // Match your database name

// Create a database connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
// Handle form submission to add a new alter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_alter'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Validate input
    if (!empty($name) && !empty($description)) {
        // Insert the new alter into the database
        $sql = "INSERT INTO alters (name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Alter added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding alter: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Name and description are required.');</script>";
    }
}

// Fetch data from the database
$sql = "SELECT id, name, description FROM alters"; // Replace with your table name
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $alters[] = $row;
    }
} else {
    echo "No records found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alters</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="home.php">Dashboard</a></li>
                    <li><a href="alters.php" class="active">Alters</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Alters</h1>

            <!-- Add Alter Form -->
            <form method="POST" action="">
                <h2>Add New Alter</h2>
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div>
                    <button type="submit" name="add_alter">Add Alter</button>
                </div>
            </form>

            <!-- Display Alters Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($alters)): ?>
                        <?php foreach ($alters as $alter): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alter['id']); ?></td>
                                <td><?php echo htmlspecialchars($alter['name']); ?></td>
                                <td><?php echo htmlspecialchars($alter['description']); ?></td>
                                <td>
                                    <a href="edit_alter.php?id=<?php echo $alter['id']; ?>">Edit</a>
                                    <a href="delete_alter.php?id=<?php echo $alter['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No alters found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
<style>
    /* General Styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #e9ecef;
    color: #2c3e50;
}

.container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sidebar nav ul {
    list-style-type: none;
    padding: 0;
    margin: 20px 0;
}

.sidebar nav ul li {
    margin-bottom: 15px;
}

.sidebar nav ul li a {
    color: #bdc3c7;
    text-decoration: none;
    display: block;
    padding: 12px;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.sidebar nav ul li a:hover {
    background-color: #34495e;
    color: #ecf0f1;
}

.sidebar nav ul li a.active {
    background-color: #3498db;
    color: white;
}

/* Main Content Styles */
.main-content {
    flex-grow: 1;
    padding: 30px;
    background-color: #f8f9fa;
}

.main-content h1 {
    color: #2c3e50;
    border-bottom: 3px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 25px;
}

/* Prison-style Form */
form {
    background-color: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border: 1px solid #dee2e6;
}

form h2 {
    color: #2c3e50;
    margin-top: 0;
    margin-bottom: 25px;
    font-size: 1.4em;
}

form div {
    margin-bottom: 15px;
}

form label {
    display: block;
    margin-bottom: 8px;
    color: #495057;
    font-weight: 600;
}

form input[type="text"],
form textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

form input[type="text"]:focus,
form textarea:focus {
    border-color: #3498db;
    outline: none;
}

form button {
    background-color: #3498db;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #2980b9;
}

/* Security-style Table */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-radius: 6px;
    overflow: hidden;
}

table th {
    background-color: #2c3e50;
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9em;
}

table td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
}

table tr:nth-child(even) {
    background-color: #f8f9fa;
}

table tr:hover {
    background-color: #f1f4f7;
}

table a {
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 3px;
    margin-right: 8px;
    font-weight: 500;
}

table a[href*="edit"] {
    color: #3498db;
    border: 1px solid #3498db;
}

table a[href*="delete"] {
    color: #e74c3c;
    border: 1px solid #e74c3c;
}

table a:hover {
    opacity: 0.8;
}
</style>
</html>