<?php
session_start();
require_once '../config.php';
$isLoggedIn = false;
if (!isset($_SESSION['email']) || $_SESSION['Role'] !== 'user') {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
}

// Handle delete action
if (isset($_GET['delete'])) {
    $bookingId = intval($_GET['delete']);
    $deleteSql = "DELETE FROM bookings WHERE bookingId = $bookingId";
    if ($conn->query($deleteSql)) {
        // Redirect to refresh the page after deletion
        header("Location: reservations.php");
        exit();
    }
}

$userId = $_SESSION['userId'] ?? null;

$sql = "
    SELECT 
        b.bookingId,
        b.booking_date,
        b.slot,
        u.userId,
        u.FirstName,
        u.LastName,
        u.email,
        u.phoneNumber,
        u.Role,
        c.name AS chaletName,
        c.location AS chaletLocation
    FROM bookings b
    JOIN users u ON b.user_id = u.userId
    JOIN chalet c ON b.chalet_id = c.chaletId
    WHERE u.userId = $userId
    ORDER BY b.booking_date DESC
    LIMIT 5
";

$result = $conn->query($sql);
$reservations = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chalet Reservation System</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/nav.css" />
    <link rel="stylesheet" href="../css/reservations.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
  </head>
  <body>
    <header>
      <nav class="navbar">
        <div class="logodiv">
          <a href="../index.php">
            <img class="logo" src="../images/beach-hut.png" alt="logo" />
          </a>
          <h1>Chalet</h1>
          <span>Booking</span>
        </div>
        <div class="navfill"></div>

        <div class="navlist">
          <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../index.php">About Us</a></li>
            <li><a href="../index.php">Chalets</a></li>
            <li><a href="../index.php">Highest rating</a></li>
           
    <?php if ($isLoggedIn):  ?>
         <li class="dropdown">
  <button class="dropbtn">
    <i class="fas fa-user"></i> <?= $_SESSION['FirstName']?> <span class="arrow">▼</span>
  </button>
  <div class="dropdown-content">
    <a href="#">Wishlist</a>
    <a href="#">Bookings</a>
    <a href="logout.php">Logout</a>
  </div>
</li>
    <?php else: ?>
        <li>
          <button id="sign-in" class="signin" onclick="signin()">Sign in</button>
        </li>
        <li>
          <button id="sign-up" class="signup" onclick="signup()">Sign up</button>
        </li>
    <?php endif; ?>
    
          </ul>
        </div>
      </nav>
    </header>

    <main>
        <section class="bookings">
          <h2>My Recent Bookings</h2>
          <table>
            <thead>
              <tr>
                <th>Reservation ID</th>
                <th>Chalet Name</th>
                <th>Location</th>
                <th>Booking Date</th>
                <th>Time Slot</th>
                <th>User Name</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservations as $reservation): ?>
<tr>
    <td>#<?= $reservation['bookingId'] ?></td>
    <td><?= htmlspecialchars($reservation['chaletName']) ?></td>
    <td><?= htmlspecialchars($reservation['chaletLocation']) ?></td>
    <td><?= htmlspecialchars($reservation['booking_date']) ?></td>
    <td><?= htmlspecialchars($reservation['slot']) ?></td>
    <td><?= htmlspecialchars($reservation['FirstName'] . ' ' . $reservation['LastName']) ?></td>
    <td class="actions">
                      <a href="reservations.php?delete=<?= $reservation['bookingId']; ?>" 
                      class="delete-btn" style="text-decoration:none;"
                     onclick="return confirm('Are you sure you want to delete this booking?');">
                     <i class="fas fa-trash-alt"></i> Delete</a>
                    </td>
</tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </section>
    </main>

    <footer class="footerEnd">
      <p>&copy; 2025 ChaletBooking.</p>
    </footer>
     <script>
        const dbChalets = <?php echo $chalets_json; ?>;
    </script>
    <script src="../js/nav.js"></script>
  </body>
</html>