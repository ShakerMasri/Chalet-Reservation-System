<?php
session_start();
require_once '../config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = trim($_POST['code']);  // keep as string
    $email = $_SESSION['email'] ?? '';

    if (!$email) {
        $message = "Session expired. Please request a new code.";
    } else {
        $stmt = $conn->prepare("SELECT userId FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $userResult = $stmt->get_result();
        
        if ($userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
            $userId = $user['userId'];
            
            $stmt = $conn->prepare("SELECT userId FROM password_resets 
                                    WHERE userId = ? AND code = ? AND expires_at > NOW()
                                    LIMIT 1");
            $stmt->bind_param("is", $userId, $code);  
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['reset_verified'] = true;
                $_SESSION['reset_user_id'] = $userId;

                $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE userId = ? AND code = ?");
                $deleteStmt->bind_param("is", $userId, $code);
                $deleteStmt->execute();
                $deleteStmt->close();
                
                header("Location: ResetPassword.php");
                exit();
            } else {
                $message = "Invalid or expired code.";
            }
        } else {
            $message = "User not found. Please request a new code.";
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link rel="stylesheet" href="../css/password.css">
</head>
<body>
    <div class="container">
        <h1>Verify Code</h1>
        <p>Enter the 6-digit code sent to your email.</p>
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" required>
            <button type="submit">Verify</button>
        </form>
        <a href="password.php" class="back-link">Back</a>
    </div>
</body>
</html>
