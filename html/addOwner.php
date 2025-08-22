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
           $stmt->execute([$firstname, $lastname, $email, $phone, $hashedPassword, 'owner']);
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
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="../css/signup.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <title>Add-Owner</title>
  </head>
  <body>
    <div class="dashboard">
      <aside class="sidebar">
        <img class="logo" src="../images/beach-hut.png" alt="logo" />
        <hr />
        <nav>
          <a href="../admin.php"><i class="fas fa-home"></i> Home</a>
          <a href="addChalet.php"
            ><i class="fas fa-plus-circle"></i> Add Chalet</a
          >
          <a href="ViewChalet.php"><i class="fas fa-eye"></i>View chalet</a>
          <a href="#"><i class="fas fa-user-plus"></i> Add Owner</a>
          <a href="manageOwner.php"
            ><i class="fas fa-users-cog"></i> Manage Owners</a
          >
        </nav>
        <button class="logout" onclick="logout2()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>
      <main class="mainsec">
        <div class="admin-form-container">
          <div class="innersec admin-form">
            <div class="form-header">
              <div class="logologin">
                <h3 class="form-title">Create Owner Account</h3>
              </div>
              
    <div class="error-message">
       <?php if (!empty($errors)): ?>
        <?php if (count($errors) > 1): ?>
            <p>Enter valid data</p>
        <?php else: ?>
            <p><?php echo htmlspecialchars($errors[0]); ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

            </div>
            <form class="owner-registration-form" method="POST" action="addOwner.php">
              <div class="name-fields">
                <div class="forminput">
                  <label for="firstname">First Name:</label>
                  <input type="text" id="firstname" name="firstname" required />
                </div>
                <div class="forminput">
                  <label for="lastname">Last Name:</label>
                  <input type="text" id="lastname" name="lastname" required />
                </div>
              </div>

              <div class="forminput">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required />
              </div>

              <div class="forminput">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required />
              </div>

              <div class="forminput">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required />
              </div>

              <div class="forminput">
                <label for="confirmpassword">Confirm Password:</label>
                <input
                  type="password"
                  id="confirmpassword"
                  name="confirmpassword"
                  required
                />
              </div>
              <div class="form-actions">
                <button type="submit" class="register-button">Add Owner</button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
    <?php if ($signup_success): ?>
<script>
    alert("Signup done successfully! Welcome to Chalet Booking System");
    window.location.href = "../admin.php"; 
</script>
<?php endif; ?>
    <script src="../js/admin.js"></script>
  </body>
</html>
