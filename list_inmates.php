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

// Fetch inmates from the database
$query = "SELECT * FROM inmates";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Inmates - Prison Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #3b3f44;
            color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 40px;
            font-size: 2.5em;
            color: #f39c12;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            background-color: #2c3e50;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
            border-radius: 8px;
            margin-top: 40px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #7f8c8d;
        }

        th {
            background-color: #34495e;
            color: #ecf0f1;
        }

        tr:nth-child(even) {
            background-color: #2c3e50;
        }

        tr:hover {
            background-color: #f39c12;
            color: #2c3e50;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9em;
            color: #7f8c8d;
        }

        footer a {
            color: #f39c12;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Inmate List</h1>

    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Name</th><th>Age</th><th>Gender</th><th>Crime</th><th>Sentence</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['age'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "<td>" . $row['crime'] . "</td>";
                echo "<td>" . $row['sentence'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No inmates found.</p>";
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2025 Prison Management System | <a href="privacy_policy.php">Privacy Policy</a></p>
    </footer>

</body>
</html>

<?php
$conn->close();
?>
