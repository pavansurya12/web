<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch past results
$sql = "SELECT ec.category_name, e.exam_name, ur.total_questions, ur.correct_answers, ur.incorrect_answers, ur.timestamp 
        FROM results ur
        JOIN exams e ON ur.exam_id = e.id
        JOIN exam_categories ec ON e.category_id = ec.id
        WHERE ur.user_id = ?
        ORDER BY ur.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Exam Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Past Exam Results</h2>
        <?php if ($result->num_rows > 0) { ?>
            <table border="1">
                <tr>
                    <th>Exam</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Total Questions</th>
                    <th>Correct</th>
                    <th>Incorrect</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { 
                    // Format exam date (if it's in DATETIME format)
                    $exam_date = new DateTime($row["timestamp"]);
                    $formatted_date = $exam_date->format('M d, Y'); // Example format: Jan 01, 2025
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row["exam_name"]); ?></td>
                        <td><?= htmlspecialchars($row["category_name"]); ?></td>
                        <td><?= $formatted_date; ?></td>
                        <td><?= $row["total_questions"]; ?></td>
                        <td><?= $row["correct_answers"]; ?></td>
                        <td><?= $row["incorrect_answers"]; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No past results found.</p>
        <?php } ?>
        <a href="page2.php">Back to Exams</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
