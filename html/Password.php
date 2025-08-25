<?php
session_start();
require_once '../config.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $stmt = $conn->prepare("SELECT userId FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['userId'];

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt2 = $conn->prepare("
            INSERT INTO password_resets (userId, code)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE code = VALUES(code)
        ");
        $stmt2->bind_param("is", $userId, $code);
        $stmt2->execute();

    
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mohyeess2004@gmail.com';
            $mail->Password   = 'ahxs avyv bplb yyln';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('mohyeess2004@gmail.com', 'Chalet Management');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "<p>Your password reset code is: <b>$code</b></p><p>This code will expire in 15 minutes.</p>";

            $mail->send();

            $_SESSION['reset_userId'] = $userId;
            header("Location: verfiyPage.php");
            exit();
        } catch (Exception $e) {
            $error = "Failed to send email. Please try again.";
        }

        $stmt2->close();
    } else {
        $error = "No account found with that email.";
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/password.css">
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <p>Enter your email to receive a 6-digit reset code.</p>

        <?php if ($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="email" name="email" placeholder="name@gmail.com" required>
            <button type="submit">Send Reset Code</button>
        </form>

        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
