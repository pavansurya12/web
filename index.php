<?php
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>
    <link rel="stylesheet" href="mainstyles.css"> <!-- Link to your external CSS file -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <!-- Project Title and Logo -->
    <header class="project-title">
        <img src="brain-logo.png" alt="Brain Up Logo" class="logo"> <!-- Brain logo image -->
        Brain Up
    </header>

    <div class="container">
        <!-- Welcome message -->
        <div class="welcome-message">
            <h1>Welcome to Brain Up!</h1>
            <p>Test your knowledge with fun and interactive quizzes.</p>
        </div>

<!-- Button to start the quiz -->
<button class="start-btn" onclick="window.location.href='start_quiz.php'" aria-label="Start the quiz">Start Quiz</button>

    </div>

    <div class="buttons">
        <!-- New Contact button positioned to the left of Login -->
        <a href="contact.php" class="contact-btn" aria-label="Contact us">Contact</a>
        <!-- Links for login and register pages -->
        <a href="login.php" aria-label="Login to your account">Login</a>
        <a href="register.php" aria-label="Create a new account">Register</a>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 Quiz App. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JavaScript for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-cuG2bVVV8JEVauJtVRp4vFq5Npxkz1JniS5fVjYHcq6XeqjjIZj09PAwl+o8kD2h" crossorigin="anonymous"></script>

</body>
</html>
