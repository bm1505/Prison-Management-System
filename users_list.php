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

// Fetch all users
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
</head>
<body>
    <h1>Users List</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['password'] ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
