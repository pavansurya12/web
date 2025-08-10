<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_name = $_POST['exam_name'];
    $category_id = $_POST['category_id'];
    $duration = $_POST['duration'];

    $sql = "INSERT INTO exams (exam_name, category_id, duration) VALUES ('$exam_name', '$category_id', '$duration')";
    if ($conn->query($sql)) {
        echo "Exam added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Exam</title>
</head>
<body>
    <h2>Add New Exam</h2>
    <form method="POST">
        <input type="text" name="exam_name" required placeholder="Exam Name">
        <select name="category_id">
            <?php while ($row = $categories->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['category_name']; ?></option>
            <?php } ?>
        </select>
        <input type="number" name="duration" required placeholder="Exam Duration (in minutes)">
        <button type="submit">Create Exam</button>
    </form>
</body>
</html>
