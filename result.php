<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized. Please log in first.");
}

if (!isset($_GET['result_id'])) {
    die("Missing result ID.");
}

$user_id = $_SESSION['user_id'];
$result_id = $_GET['result_id'];

$sql = "SELECT exams.exam_name, results.total_questions, results.correct_answers, results.incorrect_answers, results.timestamp 
        FROM results 
        JOIN exams ON results.exam_id = exams.id 
        WHERE results.user_id = ? AND results.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $result_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Exam Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .result-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .result-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
        }

        .footer-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Your Exam Result</h2>

<?php if ($result && $row = $result->fetch_assoc()): ?>
    <div class="result-card">
        <h3><?= htmlspecialchars($row['exam_name']) ?></h3>
        <div class="result-row">
            <strong>Total Questions:</strong>
            <span><?= $row['total_questions'] ?></span>
        </div>
        <div class="result-row">
            <strong>Correct Answers:</strong>
            <span><?= $row['correct_answers'] ?></span>
        </div>
        <div class="result-row">
            <strong>Incorrect Answers:</strong>
            <span><?= $row['incorrect_answers'] ?></span>
        </div>
        <div class="result-row">
            <strong>Date Taken:</strong>
            <span><?= $row['timestamp'] ?></span>
        </div>
        <div class="result-row">
            <strong>Score:</strong>
            <span>
                <?php
                $score = round(($row['correct_answers'] / $row['total_questions']) * 100, 2);
                echo $score . "%";
                ?>
            </span>
        </div>
    </div>
<?php else: ?>
    <p style="text-align:center; color: #888;">No result found for this ID.</p>
<?php endif; ?>

<div class="footer-links">
    <a href="page2.php">Back to Exams</a> |
    <a href="logout.php">Logout</a>
</div>

</body>
</html>
