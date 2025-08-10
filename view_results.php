<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch results with exam and category information
$results = $conn->query("
    SELECT 
        users.name, 
        exams.exam_name, 
        exam_categories.category_name, 
        results.correct_answers, 
        results.total_questions, 
        ROUND((results.correct_answers / results.total_questions) * 100, 2) AS score_percentage, 
        results.timestamp
    FROM results
    JOIN users ON results.user_id = users.id 
    JOIN exams ON results.exam_id = exams.id
    JOIN exam_categories ON exams.category_id = exam_categories.id
");

if (!$results) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-link {
            text-decoration: none;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            margin-top: 20px;
            display: inline-block;
        }
        .btn-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Student Results</h2>

    <table>
        <tr>
            <th>Student Name</th>
            <th>Exam Name</th>
            <th>Category</th>
            <th>Correct / Total</th>
            <th>Score (%)</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $results->fetch_assoc()) { 
            // Format exam date (if it's in DATETIME format)
            $exam_date = new DateTime($row["timestamp"]);
            $formatted_date = $exam_date->format('M d, Y'); // Example format: Jan 01, 2025
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td><?php echo "{$row['correct_answers']} / {$row['total_questions']}"; ?></td>
            <td><?php echo "{$row['score_percentage']}%"; ?></td>
            <td><?php echo $formatted_date; ?></td>
        </tr>
        <?php } ?>
    </table>

    <br><br>
    <a href="admin_dashboard.php" class="btn-link">Back to Dashboard</a>
</body>
</html>
