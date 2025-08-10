<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid Exam ID.");
}

// Fetch exam details
$stmt = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();
$stmt->close();

if (!$exam) {
    die("Exam not found.");
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exam_name = trim($_POST['exam_name']);
    $duration = intval($_POST['duration']);

    if (empty($exam_name) || $duration <= 0) {
        echo "<script>alert('Please provide valid exam name and duration!');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE exams SET exam_name = ?, duration = ? WHERE id = ?");
        $stmt->bind_param("sii", $exam_name, $duration, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Exam updated successfully!'); window.location='manage_exams.php';</script>";
        } else {
            echo "<script>alert('Error updating exam.');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Exam</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
<div class="edit-container">
    <h2>Edit Exam</h2>
    <form method="POST">
        <label>ID:</label>
        <input type="text" value="<?php echo $id; ?>" disabled>
        <label>Exam Name:</label>
        <input type="text" name="exam_name" value="<?php echo htmlspecialchars($exam['exam_name']); ?>" required>
        <label>Duration (minutes):</label>
        <input type="number" name="duration" value="<?php echo $exam['duration']; ?>" required>
        <button type="submit">Update Exam</button>
    </form>
    <br>
    <a href="manage_exams.php">Back to Manage Exams</a>
</div>
</body>
</html>
