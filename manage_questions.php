<?php
include 'db_connect.php';
session_start();

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Delete question
if (isset($_GET['delete'])) {
    $question_id = intval($_GET['delete']); // Prevent SQL injection
    $conn->query("DELETE FROM questions WHERE id = $question_id");
    header("Location: manage_questions.php");
    exit;
}

// Fetch all questions with exam names
$questions = $conn->query("
    SELECT questions.*, exams.exam_name 
    FROM questions 
    JOIN exams ON questions.exam_id = exams.id
");

if (!$questions) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Questions</title>
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

    <h2>Manage Questions</h2>

    <table>
        <tr>
            <th>Exam Name</th>
            <th>Question</th>
            <th>Options</th>
            <th>Correct Answer</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $questions->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['exam_name']) ?></td>
            <td><?= htmlspecialchars($row['question_text']) ?></td>
            <td>
                A) <?= htmlspecialchars($row['option_a']) ?> |
                B) <?= htmlspecialchars($row['option_b']) ?> |
                C) <?= htmlspecialchars($row['option_c']) ?> |
                D) <?= htmlspecialchars($row['option_d']) ?>
            </td>
            <td><?= strtoupper(htmlspecialchars($row['correct_option'])) ?></td>
            <td>
                <a class="btn-link" href="edit_question.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="btn-link" href="manage_questions.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <br><br>
    <a href="admin_dashboard.php" class="btn-link">Back to Dashboard</a>

</body>
</html>
