<?php
include 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Delete user if requested
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $user_id");
    header("Location: manage_users.php");
    exit;
}

// Fetch all users
$users = $conn->query("SELECT * FROM users");

if (!$users) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-link {
            text-decoration: none;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            margin-right: 5px;
        }
        .btn-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <h2>Manage Users</h2>

    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $users->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <a class="btn-link" href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="btn-link" href="manage_users.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <br><br>
    <a href="admin_dashboard.php" class="btn-link">Back to Dashboard</a>

</body>
</html>
