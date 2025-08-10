<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php?msg=Please login to access the quizzes");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quizes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
    }
    .bg-wrapper {
      background-image: url('bgmain.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      width: 100%;
    }
    .card-section {
      margin-top: 6cm;
    }
    .card {
      transform: scale(0.9);
      transform-origin: top center;
      margin: 0.5rem auto;
    }
    .welcome-text h1,
    .welcome-text p {
      color: #ffffff !important;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
    }
    .text-bg-dark p,
    .text-bg-dark .text-body-secondary {
      color: #ffffff !important;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }
  </style>
</head>

<body>
  <div class="bg-wrapper">
    <!-- Navbar -->
    <div class="navbar navbar-dark bg-dark shadow-sm">
      <div class="container">
        <a href="#" class="navbar-brand d-flex align-items-center">
          <img src="brain-logo.png" width="30" height="30" class="me-2" alt="Brain Logo">
          <strong>Brain Up</strong>
        </a>
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </div>

    <!-- Collapsible About Section -->
    <div class="text-bg-dark collapse" id="navbarHeader">
      <div class="container">
        <div class="row">
          <!-- About Text -->
          <div class="col-sm-8 col-md-7 py-4">
            <h4>About</h4>
            <p class="text-body-secondary">
              This quiz platform is built to make learning exciting and engaging! Whether you're brushing up on your general knowledge, testing your movie trivia, or preparing for competitive exams, our quizzes offer something for everyone.
              <br><br>
              Our goal is to combine fun with learning, making it easy for students, professionals, and curious minds to challenge themselves and grow. New quizzes are added regularly, so keep coming back to test your brain!
            </p>
          </div>

          <!-- Logout Option -->
          <div class="col-sm-4 offset-md-1 py-4 text-end">
            <h4>Account</h4>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
          </div>

          <!-- View Past Results Collapsible -->
          <div class="col-12 mt-4">
            <button class="btn btn-outline-light w-100" type="button" data-bs-toggle="collapse" data-bs-target="#pastResults" aria-expanded="false" aria-controls="pastResults">
              📊 View Past Results
            </button>

            <div class="collapse mt-3" id="pastResults">
              <div class="card card-body bg-light text-dark">
                <?php
                  include 'db_connect.php';
                  $user_id = $_SESSION['user_id'];

                  $sql = "SELECT exams.exam_name, results.total_questions, results.correct_answers, results.timestamp
                          FROM results
                          JOIN exams ON results.exam_id = exams.id
                          WHERE results.user_id = ?
                          ORDER BY results.timestamp DESC";

                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $user_id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows > 0) {
                    echo '<ul class="list-group">';
                    while ($row = $result->fetch_assoc()) {
                        $score = ($row['correct_answers'] / $row['total_questions']) * 100;
                        $formattedDate = date("M d, Y", strtotime($row['timestamp']));
                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                        echo "<span><strong>{$row['exam_name']}</strong>: {$row['correct_answers']}/{$row['total_questions']} (" . round($score) . "%)</span>";
                        echo "<span class='badge bg-primary rounded-pill'>{$formattedDate}</span>";
                        echo '</li>';
                    }
                    echo '</ul>';
                  } else {
                    echo "<p class='mb-0'>No past results found.</p>";
                  }

                  $stmt->close();
                ?>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Welcome Section -->
    <section class="py-5 text-center container welcome-text">
      <div class="row py-lg-5">
        <div class="col-lg-6 col-md-8 mx-auto">
            <br>

                </br>
          <h1 class="fw-light">Test Your Knowledge!</h1>
          <p class="lead text-body-secondary">
          <br>

</br>
            Welcome to our fun and interactive quiz zone! Challenge yourself across a variety of topics like movies, music, sports, general knowledge, and more. Whether you're here to learn or just have fun, there's something for everyone.
          </p>
        </div>
      </div>
    </section>

    <!-- Card Section -->
    <div class="container card-section">
      <div class="row">
        <!-- Card 1 -->
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="moviepic.jpg" class="card-img-top" alt="Movie Quiz">
            <div class="card-body">
              <h5 class="card-title">Movie Quiz</h5>
              <p class="card-text">Have fun by guessing movie names, heroes, heroines, and directors. Test your Bollywood and Hollywood knowledge!</p>
              <a href="start_exam.php?exam_id=1" class="btn btn-primary">Start Quiz</a>
              <small class="text-body-secondary d-block mt-2">10 mins</small>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="mathpic.jpg" class="card-img-top" alt="Algebra Test">
            <div class="card-body">
              <h5 class="card-title">Algebra Test</h5>
              <p class="card-text">Challenge your math skills! Solve algebraic equations, simplify expressions, and boost your problem-solving power.</p>
              <a href="start_exam.php?exam_id=2" class="btn btn-primary">Start Quiz</a>
              <small class="text-body-secondary d-block mt-2">10 mins</small>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="gkpic.jpg" class="card-img-top" alt="General Knowledge Quiz">
            <div class="card-body">
              <h5 class="card-title">General Knowledge</h5>
              <p class="card-text">Test your knowledge about the world! From history to science, challenge yourself with amazing GK questions.</p>
              <a href="start_exam.php?exam_id=3" class="btn btn-primary">Start Quiz</a>
              <small class="text-body-secondary d-block mt-2">10 mins</small>
            </div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="phy.jpg" class="card-img-top" alt="physics">
            <div class="card-body">
              <h5 class="card-title">Physics</h5>
              <p class="card-text">Unlock the mysteries of the universe! Explore forces, motion, energy, and more with our exciting physics challenges.</p>
              <a href="start_exam.php?exam_id=4" class="btn btn-primary">Start Quiz</a>
              <small class="text-body-secondary d-block mt-2">10 mins</small>
            </div>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="history.jpg" class="card-img-top" alt="History">
            <div class="card-body">
              <h5 class="card-title">History</h5>
              <p class="card-text">Journey through time! Test your knowledge of historic events, great leaders, ancient civilizations, and epic battles.</p>
              <a href="start_exam.php?exam_id=5" class="btn btn-primary">Start Quiz</a>
              <small class="text-body-secondary d-block mt-2">10 mins</small>
            </div>
          </div>
        </div>

        <!-- Card 6 -->
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="sports.jpg" class="card-img-top" alt="Sports">
            <div class="card-body">
              <h5 class="card-title">Sports</h5>
              <p class="card-text">Show off your sports smarts! Answer questions about famous athletes, legendary matches, records, and game rules.</p>
              <a href="start_exam.php?exam_id=6" class="btn btn-primary">Start Quiz</a>
              <small class="text-body-secondary d-block mt-2">10 mins</small>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
