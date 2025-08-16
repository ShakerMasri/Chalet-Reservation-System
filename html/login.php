<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare("SELECT email, password, Role ,FirstName, LastName FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (sha1($password)=== $row['password']) {
                $_SESSION['email'] = $row['email'];
                $_SESSION['Role'] = $row['Role'];
                $_SESSION['FirstName'] = $row['FirstName'];
                $_SESSION['LastName']  = $row['LastName'];

                if ($row['Role'] === 'admin') {
                
                    header("Location: ../admin.php");
                } elseif ($row['Role'] === 'owner') {
                    header("Location: ownerDashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "email or password is wrong!";
        }
    } else {
        $error = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chalet Reservation System</title>
    <link rel="stylesheet" href="../css/login.css" />
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

        <div class="topform">
          <h3>Sign in</h3>
          <?php if (!empty($error)): ?>
  <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
          <form class="inform" action="login.php" method="post">
            <div class="forminput">
              <label for="email">Email: </label>
              <input type="email" id="email" name="email" required />
            </div>
            <div class="forminput">
              <label for="password">Password: </label>
              <input type="password" id="password" name="password" required />
            </div>
            <div class="passlink">
              <a href="Password">Forgot password?</a>
            </div>
            <button type="submit">Sign in</button>
          </form>
        
        </div>

        <div class="signupLink">
          Don't have an account? <a href="signup.php">Sign up</a>
        </div>
      </div>
    </section>
  </body>
</html>
