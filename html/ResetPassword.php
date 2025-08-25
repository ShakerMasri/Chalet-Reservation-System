<?php
session_start();
require_once "../config.php";

$message = "";
$messageClass = "";
$showForm = isset($_SESSION['verified_userId']);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $showForm) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $userId = $_SESSION['verified_userId'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $messageClass = "alert";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
         $messageClass = "alert";
    } else {
        $hashed_password = sha1($password);

        $update = $conn->prepare("UPDATE users SET password = ? WHERE userId = ?");
        $update->bind_param("si", $hashed_password, $userId);

        if ($update->execute()) {
            $conn->query("DELETE FROM password_resets WHERE userId = $userId");
            unset($_SESSION['verified_userId']);
            $message = "Password successfully updated!";
            $messageClass = "success";
            $showForm = false;
        } else {
            $message = "Failed to update password. Please try again.";
                    $messageClass = "alert";

        }
        $update->close();
    }
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
    <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
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
