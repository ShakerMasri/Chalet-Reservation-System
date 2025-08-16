<?php
require_once '../config.php';
session_start();
$errors = [];
$firstname = $lastname = $email = $phone = $password=$confirmpassword='';
$signup_success = false;


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"] ;
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"] ;
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if ($password !== $confirmpassword) $errors[] = "Passwords don't match";
    
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT userId FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Email already registered";
    }
    
    if (empty($errors)) {
        $hashedPassword = sha1($password);
        
        try {
           $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, phoneNumber, password,Role) VALUES (?, ?, ?, ?, ?,?)");
           $stmt->execute([$firstname, $lastname, $email, $phone, $hashedPassword, 'user']);
            $signup_success = true;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chalet Reservation System</title>
    <link rel="stylesheet" href="../css/signup.css" />
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <section class="mainsec">
        <div class="innersec">
            <div class="logologin">
                <a href="../index.php">
                    <img class="logo" src="../images/beach-hut.png" alt="logo" />
                </a>
                <h2>ChaletBooking</h2>
            </div>

    <?php if (!empty($errors)): ?>
    <div class="error-message">
        <?php if (count($errors) > 1): ?>
            <p>Enter valid data</p>
        <?php else: ?>
            <p><?php echo htmlspecialchars($errors[0]); ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

            <div class="topform">
                <h3>Register</h3>
                <form class="inform" method="POST" action="signup.php">
                    <div class="name-fields">
                        <div class="forminput">
                            <label for="firstname">First Name: </label>
                            <input type="text" id="firstname" name="firstname" 
                                   value="<?php echo $firstname; ?>" required />
                        </div>
                        <div class="forminput">
                            <label for="lastname">Last Name: </label>
                            <input type="text" id="lastname" name="lastname" 
                                   value="<?php echo$lastname; ?>" required />
                        </div>
                    </div>

                    <div class="forminput">
                        <label for="email">Email: </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo $email; ?>" required />
                    </div>

                    <div class="forminput">
                        <label for="phone">Phone Number: </label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo $phone; ?>" required />
                    </div>

                    <div class="forminput">
                        <label for="password">Password: </label>
                        <input type="password" id="password" name="password" required />
                    </div>

                    <div class="forminput">
                        <label for="confirmpassword">Confirm Password: </label>
                        <input type="password" id="confirmpassword" name="confirmpassword" required />
                    </div>

                    <button type="submit" class="register-button">Register</button>
                </form>

                <div class="login-link">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </div>
        </div>
    </section>
    <?php if ($signup_success): ?>
<script>
    alert("Signup done successfully! Welcome to Chalet Booking System");
    window.location.href = "login.php";
</script>
<?php endif; ?>
</body>
</html>