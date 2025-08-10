<?php
include 'db_connect.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}
$user_id = intval($_GET['id']);

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, is_blocked, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $is_blocked = isset($_POST['is_blocked']) ? 1 : 0;
    $role = $_POST['role'];

    if (empty($name) || empty($email) || empty($role)) {
        echo "<script>alert('All fields except password are required!');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!');</script>";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, is_blocked = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssisi", $name, $email, $hashed_password, $is_blocked, $role, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, is_blocked = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $name, $email, $is_blocked, $role, $user_id);
        }

        if ($stmt->execute()) {
            echo "<script>alert('User updated successfully!'); window.location='manage_users.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error updating user!');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
<div class="edit-container">
    <h2>Edit User</h2>
    <form method="POST">
        <label>ID:</label>
        <input type="text" value="<?php echo $user_id; ?>" disabled>
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label>Password (Leave blank to keep current):</label>
        <input type="password" name="password">
        <label>Is Blocked:</label>
        <input type="checkbox" name="is_blocked" <?php echo $user['is_blocked'] ? 'checked' : ''; ?>>
        <label>Role:</label>
        <select name="role">
            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
        </select>
        <button type="submit">Update User</button>
    </form>
    <br>
    <a href="manage_users.php">Back to Manage Users</a>
</body>
</html>