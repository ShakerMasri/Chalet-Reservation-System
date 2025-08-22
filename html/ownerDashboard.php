<?php
require_once '../config.php';
session_start();
$ownedChalets = 0;
$totalReviews = 0;
$totalUpcoming = 0;

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $query = "
        SELECT COUNT(*) as chalet_count 
        FROM chalet
        INNER JOIN users ON chalet.ownerId = users.userId 
        WHERE users.email = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $ownedChalets = $row['chalet_count'];
    }
    $query = "
        SELECT COUNT(reviews.reviewId) as total_reviews
        FROM reviews
        INNER JOIN chalet ON reviews.chaletId = chalet.chaletId
        INNER JOIN users ON chalet.ownerId = users.userId
        WHERE users.email = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $totalReviews = $row['total_reviews'];
    }
    $query = "
    SELECT COUNT(bookings.bookingId) AS total_bookings
    FROM bookings
    INNER JOIN chalet ON bookings.chalet_id = chalet.chaletId
    INNER JOIN users ON chalet.ownerId = users.userId
    WHERE users.email = ?
  AND bookings.booking_date >= CURDATE()
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $totalUpcoming = $row['total_bookings'];
}
}


function getChaletRating($chaletId) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE chaletId = ?");
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $stmt->bind_result($avg_rating);
    $stmt->fetch();
    $stmt->close();
    return $avg_rating ? number_format($avg_rating, 1) : 'N/A';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <div class="dashboard">
      <aside class="sidebar">
        <img class="logo" src="../images/beach-hut.png" alt="logo" />
        <hr />
        <nav>
          <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="BookingMange.php"
            ><i class="fas fa-calendar-alt"></i> Bookings</a
          >
          <a href="#chalets"><i class="fas fa-home"></i> My Chalets</a>
          <a href="Reviews.php"><i class="fas fa-star"></i> Reviews</a>
        </nav>
        <button class="logout" onClick="logout()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>

      <main class="main">
        <header>
          <h1>Owner Dashboard</h1>
          <p>Welcome back! <?= ($_SESSION["FirstName"]) ?></p>
        </header>

        <div class="stats">
          <div class="stat-card">
            <i class="fas fa-calendar"></i>
            <p>Upcoming Bookings</p>
            <h2><?= $totalUpcoming ?></h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-home"></i>
            <p>Chalets</p>
            <h2><?= $ownedChalets ?></h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-star"></i>
            <p>total Reviews</p>
            <h2><?= $totalReviews ?></h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-dollar-sign"></i>
            <p>Month Revenue</p>
            <h2>$8450</h2>
          </div>
        </div>

        <section id="chalets" class="chalets">
          <div class="section-header">
            <h2>My Chalets</h2>
          </div>

          
  <div class="cards-grid">
  <?php
$query = "
    SELECT chalet.chaletId, chalet.name, chalet.Location, MIN(chalet_images.image_path) AS image_path
    FROM chalet
    INNER JOIN chalet_images ON chalet.chaletId = chalet_images.chalet_id
    INNER JOIN users ON chalet.ownerId = users.userId
    WHERE users.email = ?
    GROUP BY chalet.chaletId
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<div class="chalet-card">';
    $imageUrl = '../images/golden/' . htmlspecialchars($row['image_path']);
    echo '<div class="chalet-image" style="background-image: url(\'' . $imageUrl . '\')"></div>';
    echo '<div class="chalet-info">';
    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
    echo '<div class="chalet-meta">';
    echo '<span><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row['Location']) . '</span>';
    echo '</div>';
    $avgRating = getChaletRating($row['chaletId']);
    echo 'Rating: <span class="rating" >' . $avgRating . '<i class="fas fa-star" style="margin-left:5px;"></i></span>';
    echo '<div class="chalet-actions">';
    echo '<a href="chaletDetails.php?id=' . htmlspecialchars($row['chaletId']) . '" class="view-btn" style="text-decoration: none; margin-top: 7px;">';
    echo '<i class="fas fa-eye"></i> View-Details';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>
</div>

          </div>
        </section>
      </main>
    </div>
    <script src="../js/owner.js"></script>
  </body>
</html>
