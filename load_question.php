<?php
include 'db_connect.php';
session_start();

$exam_id = $_GET['exam_id'];
$index = isset($_GET['index']) ? $_GET['index'] : 0;

$questions = $conn->query("SELECT * FROM questions WHERE exam_id = $exam_id LIMIT 1 OFFSET $index");

if ($row = $questions->fetch_assoc()) {
    echo "<h3>Q" . ($index + 1) . ". " . $row['question_text'] . "</h3>";
    echo "<form id='question-form'>";
    echo "<input type='hidden' name='question_id' value='" . $row['id'] . "'>";
    echo "<input type='radio' name='answer' value='A'> " . $row['option_a'] . "<br>";
    echo "<input type='radio' name='answer' value='B'> " . $row['option_b'] . "<br>";
    echo "<input type='radio' name='answer' value='C'> " . $row['option_c'] . "<br>";
    echo "<input type='radio' name='answer' value='D'> " . $row['option_d'] . "<br>";
    echo "<button type='button' onclick='nextQuestion(" . ($index + 1) . ")'>Next</button>";
    echo "</form>";
} else {
    echo "<a href='submit_exam.php'>Submit Exam</a>";
}
?>
<script>
    function nextQuestion(index) {
        $.ajax({
            url: "save_answer.php",
            type: "POST",
            data: $("#question-form").serialize(),
            success: function () {
                loadQuestion(index);
            }
        });
    }
</script>
