<?php
session_start();
include 'db_connect.php';

// Initialize variables
$name = $email = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered!";
        }
        $stmt->close();
    }

    // If no errors, insert user into DB
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $name, $email, $hashed_password);
        if ($stmt->execute()) {
            // Registration successful → go to login
            echo "<script>alert('Registration successful! Please log in.'); window.location='login.php';</script>";
            exit();
        } else {
            $errors[] = "Something went wrong. Try again!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles1.css"> <!-- Your CSS file -->
</head>
<body>
<div class="registration-container">
    <h2>Create an Account</h2>

    <!-- Message if redirected from start_quiz.php -->
    <?php if (isset($_GET['msg'])): ?>
        <div style="color: blue; margin-bottom: 10px;"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <!-- Show validation errors -->
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="input-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
