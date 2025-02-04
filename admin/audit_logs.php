<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "prison_db";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch logs with pagination
$limit = 10; // Number of logs per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM audit_logs ORDER BY timestamp DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM audit_logs";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Prison Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #222; /* Dark prison-like color */
            color: #ddd;
        }

        .navbar {
            background: #444;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #ffa500;
        }

        .container {
            padding: 20px;
            max-width: 90%;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffa500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #333;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            border: 1px solid #555;
            text-align: left;
        }

        th {
            background: #555;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #444;
        }

        tr:hover {
            background: #666;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            color: white;
            padding: 8px 12px;
            margin: 5px;
            text-decoration: none;
            background: #555;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .pagination a:hover {
            background: #ffa500;
        }

        .pagination .active {
            background: #ffa500;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">Prison Management System</div>
    <div>
        <a href="admin.php">Dashboard</a>
        <a href="users_list.php">Users</a>
        <a href="audit_logs.php">Audit Logs</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<!-- Logs Table -->
<div class="container">
    <h1>Audit Logs</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user']) ?></td>
                <td><?= htmlspecialchars($row['action']) ?></td>
                <td><?= htmlspecialchars($row['details']) ?></td>
                <td><?= htmlspecialchars($row['timestamp']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>
