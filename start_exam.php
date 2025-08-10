<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if exam_id is provided
if (!isset($_GET['exam_id'])) {
    echo "Invalid exam selection.";
    exit();
}

$exam_id = $_GET['exam_id'];

// Fetch exam details
$sql = "SELECT exam_name, duration FROM exams WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($exam_name, $duration);
$stmt->fetch();
$stmt->close();

// Fetch questions
$sql = "SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($exam_name) ?> Exam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Optional Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background: linear-gradient(to right, rgb(200, 178, 78), rgb(242, 251, 143));
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            padding: 30px;
            width: 90%;
            max-width: 700px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        #timer {
            text-align: right;
            font-weight: bold;
            margin-bottom: 20px;
            color: #dc3545;
        }

        .question {
            margin-bottom: 20px;
        }

        .btn-container {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($exam_name) ?> Exam</h2>
    <div id="timer">Time Left: <span id="time"></span></div>

    <form id="quiz-form">
        <input type="hidden" name="exam_id" value="<?= $exam_id ?>">

        <div id="question-container">
            <?php foreach ($questions as $index => $q) { ?>
                <div class="question" id="q<?= $q['id'] ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                    <p><strong>Q<?= $index + 1 ?>: <?= htmlspecialchars($q['question_text']) ?></strong></p>
                    <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="A"> <?= htmlspecialchars($q['option_a']) ?></label>
                    <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="B"> <?= htmlspecialchars($q['option_b']) ?></label>
                    <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="C"> <?= htmlspecialchars($q['option_c']) ?></label>
                    <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="D"> <?= htmlspecialchars($q['option_d']) ?></label>
                </div>
            <?php } ?>
        </div>

        <div class="btn-container">
            <button type="button" id="prev-btn" class="btn btn-secondary" style="display: none;">Previous</button>
            <button type="button" id="next-btn" class="btn btn-primary">Next</button>
            <button type="submit" id="submit-btn" class="btn btn-success" style="display: none;">Submit</button>
        </div>
    </form>
</div>

<script>
    let totalTime = <?= $duration ?> * 60; // in seconds
    let autoSubmit = false;

    function updateTimerDisplay(secondsLeft) {
        let minutes = Math.floor(secondsLeft / 60);
        let seconds = secondsLeft % 60;
        document.getElementById('time').innerText = `${minutes}m ${seconds < 10 ? '0' : ''}${seconds}s`;
    }

    updateTimerDisplay(totalTime);

    const countdown = setInterval(() => {
        totalTime--;
        if (totalTime <= 0) {
            clearInterval(countdown);
            updateTimerDisplay(0);
            autoSubmit = true;
            $("#quiz-form").submit(); // Auto submit
        } else {
            updateTimerDisplay(totalTime);
        }
    }, 1000);

    let currentQuestion = 0;
    let questions = $(".question");

    $("#next-btn").click(function () {
        if (currentQuestion < questions.length - 1) {
            $(questions[currentQuestion]).hide();
            currentQuestion++;
            $(questions[currentQuestion]).show();

            if (currentQuestion === questions.length - 1) {
                $("#next-btn").hide();
                $("#submit-btn").show();
            }
        }
        $("#prev-btn").show();
    });

    $("#prev-btn").click(function () {
        if (currentQuestion > 0) {
            $(questions[currentQuestion]).hide();
            currentQuestion--;
            $(questions[currentQuestion]).show();

            $("#submit-btn").hide();
            $("#next-btn").show();

            if (currentQuestion === 0) {
                $("#prev-btn").hide();
            }
        }
    });

    function submitQuiz() {
        $.ajax({
            url: "submit_exam.php",
            type: "POST",
            data: $("#quiz-form").serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = "result.php?result_id=" + response.result_id;
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("An error occurred while submitting the exam.");
            }
        });
    }

    $("#quiz-form").submit(function (event) {
        event.preventDefault();
        if (autoSubmit) {
            submitQuiz(); // No confirm if auto
        } else {
            if (confirm("Are you sure you want to submit your answers?")) {
                submitQuiz();
            }
        }
    });
</script>

</body>
</html>
