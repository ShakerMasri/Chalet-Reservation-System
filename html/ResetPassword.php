<?php
session_start();
require_once "../config.php";

$message = "";
$showForm = true;
$code = isset($_GET['code']) ? $_GET['code'] : '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'] ?? $code;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } else {
        $hashed_password = sha1($password); 

        $stmt = $conn->prepare("SELECT userId FROM password_resets WHERE code = ? AND expires_at > NOW()");
        $stmt->bind_param("i", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userId = $user['userId'];

            $update = $conn->prepare("UPDATE users SET password = ? WHERE userId = ?");
            $update->bind_param("si", $hashed_password, $userId);

            if ($update->execute()) {
                $delete = $conn->prepare("DELETE FROM password_resets WHERE code = ?");
                $delete->bind_param("i", $code);
                $delete->execute();

                $message = "Password updated successfully. <a href='login.php'>Click here to login</a>.";
                $showForm = false;
            } else {
                $message = "Error updating password. Please try again.";
            }

            $update->close();
        } else {
            $message = "Invalid or expired code.";
            $showForm = false;
        }

        $stmt->close();
    }
} elseif (!empty($code)) {
    $stmt = $conn->prepare("SELECT userId FROM password_resets WHERE code = ? AND expires_at > NOW()");
    $stmt->bind_param("i", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "Invalid or expired code.";
        $showForm = false;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/password.css">
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <p>Enter a new password for your account.</p>

        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($showForm): ?>
        <form method="post">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Update Password</button>
        </form>
        <?php endif; ?>

        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
