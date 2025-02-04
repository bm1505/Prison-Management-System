<?php
// Start session
session_start();

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
    <title>Visitors List - Prison Management System</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #212529;
            color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #343a40;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        h1 {
            color: #ffc107;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #495057;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #6c757d;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-danger {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-back {
            background-color: #28a745;
            color: #fff;
            margin-right: 10px;
        }
        .btn-logout {
            background-color: #dc3545;
            color: #fff;
            margin-left: 10px;
        }
        .actions {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visitors List</h1>
        <div class="actions">
            <a href="manage_visitors.php" class="btn btn-back">Back</a>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
        <!-- Visitors List -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Visitor Name</th>
                    <th>Inmate</th>
                    <th>Visit Date</th>
                    <th>Relationship</th>
                    <th>Contact Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['inmate_name']; ?></td>
                        <td><?php echo $row['visit_date']; ?></td>
                        <td><?php echo $row['relationship']; ?></td>
                        <td><?php echo $row['contact_number']; ?></td>
                        <td>
                            <a href="manage_visitors.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="manage_visitors.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Action Buttons -->
       
    </div>
</body>
</html>
