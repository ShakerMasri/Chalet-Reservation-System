<?php
require_once '../config.php';
session_start();
$email = $_SESSION['email'];
$chalets = [];
$query = "
    SELECT chaletId, name 
    FROM chalet
    INNER JOIN users ON chalet.ownerId = users.userId
    WHERE users.email = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $chalets[] = $row;
}

$reviews = [];
$query = "
    SELECT reviews.reviewId, reviews.rating, reviews.comment, reviews.created_at AS date, chalet.name AS chalet_name, reviewers.FirstName AS reviewer_name
    FROM reviews
    INNER JOIN chalet ON reviews.chaletId = chalet.chaletId
    INNER JOIN users AS reviewers ON reviews.userId = reviewers.userId
    WHERE chalet.ownerId = (SELECT userId FROM users WHERE email = ?)
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
function updateChaletRating($chaletId) {
    global $conn;

    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE chaletId = ?");
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $stmt->bind_result($avg_rating);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE chalet SET avg_rating = ? WHERE chaletId = ?");
    $stmt->bind_param("di", $avg_rating, $chaletId);
    $stmt->execute();
    $stmt->close();
}
if (isset($_GET['delete'])) {
    $reviewId = intval($_GET['delete']);

    $stmt = $conn->prepare("SELECT chaletId FROM reviews WHERE reviewId = ?");
    $stmt->bind_param("i", $reviewId);
    $stmt->execute();
    $stmt->bind_result($chaletId);
    $stmt->fetch();
    $stmt->close();

    if ($chaletId) {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE reviewId = ?");
        $stmt->bind_param("i", $reviewId);

        if ($stmt->execute()) {
            $stmt->close();
            updateChaletRating($chaletId);

            $_SESSION['message'] = "Review deleted successfully.";
        } else {
            $_SESSION['message'] = "Error deleting review: " . $conn->error;
        }
    }
    header("Location:Reviews.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reviews</title>
    <link rel="stylesheet" href="../css/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <div class="main-container">
      <aside class="sidebar">
        <img class="logo" src="../images/beach-hut.png" alt="logo" />
        <hr />
        <nav>
          <a href="ownerDashboard.php"
            ><i class="fas fa-tachometer-alt"></i> Dashboard</a
          >
          <a href="BookingMange.php"><i class="fas fa-calendar-alt"></i> Bookings</a>
          <a href="ownerDashboard.php"><i class="fas fa-home"></i> My Chalets</a>
          <a href="#"><i class="fas fa-star"></i> Reviews</a>
        </nav>
        <button class="logout" onclick="logout()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>
     <section id="reviews" class="reviews">
        <div class="section-header">
          <h2>Customer Reviews</h2>
          <div class="filter-control">
            <select id="rating-filter">
              <option value="all">All Ratings</option>
              <option value="4">+4 Stars</option>
              <option value="3">+3 Stars</option>
              <option value="2">+2 Stars</option>
              <option value="1">+1 Star</option>
            </select>
              <button id="apply-filter-btn">Apply Filter</button>

          </div>
        </div>
        <div class="reviews-list" id="reviews-list">
          <?php foreach ($reviews as $review): ?>
            <div class="review-card" data-rating="<?= $review['rating'] ?>">
              <div class="review-header">
                <div class="reviewer-info">
                  <h4><?= htmlspecialchars($review['reviewer_name']) ?></h4>
                  <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                    <?php endfor; ?>
                    <span><?= number_format($review['rating'], 1) ?></span>
                  </div>
                </div>
                <span class="review-date"><?= htmlspecialchars($review['date']) ?></span>
              </div>
              <div class="review-content">
                <h5><?= htmlspecialchars($review['chalet_name']) ?></h5>
                <p><?= htmlspecialchars($review['comment']) ?></p>
              </div>
              <div class="review-actions">
                <a href="Reviews.php?delete=<?= $review['reviewId']; ?>" class="delete-btn" style="text-decoration:none;"
   onclick="return confirm('Are you sure you want to delete this review?');">
   <i class="fas fa-trash-alt"></i> Delete
</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
    
    <script src="../js/owner.js"></script>
  </body>
</html>
