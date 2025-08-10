<?php
session_start();

// Only log out if confirmed
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_unset();
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <script>
        // Confirm logout
        window.onload = function () {
            const confirmLogout = confirm("Are you sure you want to logout?");
            if (confirmLogout) {
                window.location.href = "admin_logout.php?confirm=yes";
            } else {
                window.location.href = "admin_dashboard.php";
            }
        }
    </script>
</head>
<body>
</body>
</html>
