<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $sql = "INSERT INTO categories (category_name) VALUES ('$category')";
    if ($conn->query($sql)) {
        echo "Category added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Exam Category</title>
</head>
<body>
    <h2>Add New Exam Category</h2>
    <form method="POST">
        <input type="text" name="category" required placeholder="Enter category name">
        <button type="submit">Add Category</button>
    </form>
</body>
</html>
