<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid Question ID.");
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();
$stmt->close();

if (!$question) {
    die("Question not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = trim($_POST['question_text']);
    $a = trim($_POST['option_a']);
    $b = trim($_POST['option_b']);
    $c = trim($_POST['option_c']);
    $d = trim($_POST['option_d']);
    $correct = strtoupper(trim($_POST['correct_option']));

    if (empty($text) || empty($a) || empty($b) || empty($c) || empty($d) || !in_array($correct, ['A', 'B', 'C', 'D'])) {
        echo "<script>alert('Please fill all fields correctly.');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $text, $a, $b, $c, $d, $correct, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Question updated successfully!'); window.location='manage_questions.php';</script>";
        } else {
            echo "<script>alert('Error updating question.');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Question</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
<div class="edit-container">
    <h2>Edit Question</h2>
    <form method="POST">
        <label>ID:</label>
        <input type="text" value="<?php echo $id; ?>" disabled>
        <label>Question Text:</label>
        <textarea name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
        <label>Option A:</label>
        <input type="text" name="option_a" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
        <label>Option B:</label>
        <input type="text" name="option_b" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
        <label>Option C:</label>
        <input type="text" name="option_c" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
        <label>Option D:</label>
        <input type="text" name="option_d" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
        <label>Correct Option (A/B/C/D):</label>
        <input type="text" name="correct_option" value="<?php echo htmlspecialchars($question['correct_option']); ?>" required>
        <button type="submit">Update Question</button>
    </form>
    <br>
    <a href="manage_questions.php">Back to Manage Questions</a>
</div>
</body>
</html>
