<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM exams");

if (!$result || $result->num_rows == 0) {
    echo "<h3>No exams available</h3>";
} else {
    echo "<ul>";
    while ($exam = $result->fetch_assoc()) {
        echo "<li><a href='start_exam.php?exam_id={$exam['id']}'>{$exam['exam_name']}</a></li>";
    }
    echo "</ul>";
}
?>
