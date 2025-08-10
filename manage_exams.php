<?php
include 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $exam_id = intval($_GET['delete']); // prevent SQL injection
    $conn->query("DELETE FROM exams WHERE id = $exam_id");
    header("Location: manage_exams.php");
    exit;
}

// Fetch all exams with their category names
$exams = $conn->query("
    SELECT exams.*, categories.category_name 
    FROM exams 
    JOIN categories ON exams.category_id = categories.id
");

if (!$exams) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exams</title>
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

    <h2>Manage Exams</h2>

    <table>
        <tr>
            <th>Exam Name</th>
            <th>Category</th>
            <th>Duration (mins)</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $exams->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['exam_name']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= (int)$row['duration'] ?></td>
                <td>
                    <a class="btn-link" href="edit_exam.php?id=<?= $row['id'] ?>">Edit</a>
                    <a class="btn-link" href="manage_exams.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this exam?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br><br>
    <a href="admin_dashboard.php" class="btn-link">Back to Dashboard</a>

</body>
</html>
