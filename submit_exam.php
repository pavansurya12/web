<?php
session_start();
include 'db_connect.php';

// Validate session and exam_id
if (!isset($_SESSION['user_id']) || !isset($_POST['exam_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid submission."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = $_POST['exam_id'];
$user_answers = isset($_POST['answer']) ? $_POST['answer'] : []; // Safe default

// Fetch correct answers
$sql = "SELECT id, correct_option FROM questions WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

$total_questions = 0;
$correct_answers = 0;
$incorrect_answers = 0;

while ($row = $result->fetch_assoc()) {
    $qid = $row['id'];
    $correct_option = strtolower(trim($row['correct_option']));
    $total_questions++;

    if (isset($user_answers[$qid])) {
        $user_option = strtolower(trim($user_answers[$qid]));

        if ($user_option === $correct_option) {
            $correct_answers++;
        } else {
            $incorrect_answers++;
        }
    } else {
        $incorrect_answers++;
    }
}
$stmt->close();

// Save result to database
$sql = "INSERT INTO results (user_id, exam_id, total_questions, correct_answers, incorrect_answers, timestamp)
        VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iiiii", $user_id, $exam_id, $total_questions, $correct_answers, $incorrect_answers);

if ($stmt->execute()) {
    $result_id = $conn->insert_id;

    // Send back the result ID as a JSON response for AJAX
    echo json_encode(["status" => "success", "result_id" => $result_id]);
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
}
$stmt->close();
?>
