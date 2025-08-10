<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "<p>You must log in or register first.</p>";
    echo "<script>setTimeout(function(){ window.location.href = 'register.php'; }, 2000);</script>";
    exit;
}

require 'db_connection.php'; // Make sure you include your DB connection

$exam_id = $_GET['exam_id'] ?? 0;

// Fetch questions from DB
$sql = "SELECT * FROM questions WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Start Quiz</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }
        .question {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            border-radius: 8px;
        }
        #timer {
            font-size: 18px;
            font-weight: bold;
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>Quiz</h2>
<div id="timer">Time Left: <span id="time">10:00</span></div>

<form id="quizForm">
    <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($exam_id); ?>">

    <?php foreach ($questions as $index => $q): ?>
        <div class="question">
            <p><strong>Q<?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($q['question_text']); ?></p>
            <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A"> <?php echo $q['option_a']; ?></label><br>
            <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B"> <?php echo $q['option_b']; ?></label><br>
            <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C"> <?php echo $q['option_c']; ?></label><br>
            <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D"> <?php echo $q['option_d']; ?></label>
        </div>
    <?php endforeach; ?>

    <button type="button" onclick="autoSubmitQuiz()">Submit Now</button>
</form>

<script>
let timeLeft = 10 * 60; // 10 minutes
const timerDisplay = document.getElementById("time");
const quizForm = document.getElementById("quizForm");

function formatTime(seconds) {
    const min = Math.floor(seconds / 60);
    const sec = seconds % 60;
    return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
}

const timer = setInterval(() => {
    timeLeft--;
    timerDisplay.textContent = formatTime(timeLeft);

    if (timeLeft <= 0) {
        clearInterval(timer);
        alert("Time is up! Submitting your quiz...");
        autoSubmitQuiz();
    }
}, 1000);

function autoSubmitQuiz() {
    const formData = new FormData(quizForm);

    fetch('submit_exam.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert("Quiz submitted successfully!");
        window.location.href = "view_results.php"; // Redirect to result page
    })
    .catch(error => {
        console.error("Submission failed", error);
        alert("There was an error submitting your quiz.");
    });
}
</script>

</body>
</html>
