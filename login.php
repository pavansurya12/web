<?php
session_start();
include 'db_connect.php'; // Make sure this connects to your database

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required!";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, is_blocked FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $is_blocked);
            $stmt->fetch();

            if ($is_blocked) {
                $errors[] = "Your account has been blocked by the admin.";
            } elseif (password_verify($password, $hashed_password)) {
                // ✅ Set session variables
                $_SESSION["user_id"] = $id;
                $_SESSION["user_name"] = $name;

                // ✅ Redirect to page2.php
                header("Location: page2.php");
                exit();
            } else {
                $errors[] = "Incorrect password!";
            }
        } else {
            $errors[] = "No account found with this email!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Your CSS file -->
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php
        if (!empty($errors)) {
            echo '<div style="color: red; margin-bottom: 10px;">';
            foreach ($errors as $err) {
                echo htmlspecialchars($err) . "<br>";
            }
            echo '</div>';
        }
        ?>

        <form action="login.php" method="POST">
            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
