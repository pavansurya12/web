<?php
include 'db_connect.php';
session_start();

$question_id = $_POST['question_id'];
$user_id = $_SESSION['user_id'];
$answer = $_POST['answer'];

$conn->query("INSERT INTO user_answers (user_id, question_id, selected_answer) 
              VALUES ($user_id, $question_id, '$answer') 
              ON DUPLICATE KEY UPDATE selected_answer='$answer'");
?>
