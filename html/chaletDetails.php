<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email']) || $_SESSION['Role'] !== 'user') {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: chaletList.php');
    exit();
}

$reviewMessage = '';
$messageType = '';
if (isset($_GET['review'])) {
    if ($_GET['review'] === 'success') {
        $reviewMessage = 'Review submitted successfully!';
        $messageType = 'success';
    } elseif ($_GET['review'] === 'error') {
        $reviewMessage = isset($_GET['message']) ? urldecode($_GET['message']) : 'Failed to submit review';
        $messageType = 'error';
    }
}

$chaletId = intval($_GET['id']);
$chalet = null;
$images = [];
$reviews = [];
$owner = null;

$sql = "SELECT * FROM chalet WHERE chaletId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $chaletId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $chalet = $result->fetch_assoc();
    
    $ownerSql = "SELECT FirstName, LastName, email, phoneNumber 
                 FROM users 
                 WHERE userId = ? AND Role = 'owner'";
    $ownerStmt = $conn->prepare($ownerSql);
    $ownerStmt->bind_param("i", $chalet['ownerId']);
    $ownerStmt->execute();
    $ownerResult = $ownerStmt->get_result();
    
    if ($ownerResult->num_rows > 0) {
        $owner = $ownerResult->fetch_assoc();
    }
    
    $imageSql = "SELECT image_path FROM chalet_images WHERE chalet_id = ?";
    $imageStmt = $conn->prepare($imageSql);
    $imageStmt->bind_param("i", $chaletId);
    $imageStmt->execute();
    $imageResult = $imageStmt->get_result();
    
    while ($row = $imageResult->fetch_assoc()) {
        $images[] = '../images/golden/' . basename($row['image_path']);
    }
    
    $reviewSql = "SELECT r.*, u.FirstName, u.LastName 
                  FROM reviews r 
                  JOIN users u ON r.userId = u.userId 
                  WHERE r.chaletId = ? 
                  ORDER BY r.created_at DESC";
    $reviewStmt = $conn->prepare($reviewSql);
    $reviewStmt->bind_param("i", $chaletId);
    $reviewStmt->execute();
    $reviewResult = $reviewStmt->get_result();
    
    while ($row = $reviewResult->fetch_assoc()) {
        $reviews[] = $row;
    }
    
    if (empty($images)) {
        $images = [
            '../images/golden/pool1.jpg',
            '../images/golden/pool2.jpg',
            '../images/golden/pool3.jpg'
        ];
    }
    
} else {
    header('Location: chaletList.php');
    exit();
}

$stmt->close();
if (isset($ownerStmt)) $ownerStmt->close();
if (isset($imageStmt)) $imageStmt->close();
if (isset($reviewStmt)) $reviewStmt->close();
$bookingsData = [];
$bookingSql = "SELECT booking_date, slot FROM bookings WHERE chalet_id = ?";
$bookingStmt = $conn->prepare($bookingSql);
$bookingStmt->bind_param("i", $chaletId);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();

while ($row = $bookingResult->fetch_assoc()) {
    $dateKey = $row['booking_date']; 
    if (!isset($bookingsData[$dateKey])) {
        $bookingsData[$dateKey] = [
            'MORNING' => true,
            'EVENING' => true,
            'FULL_DAY' => true
        ];
    }
    
    $bookingsData[$dateKey][$row['slot']] = false;
    
    if ($row['slot'] === 'FULL_DAY') {
        $bookingsData[$dateKey]['MORNING'] = false;
        $bookingsData[$dateKey]['EVENING'] = false;
    }
    
    $bookingsData[$dateKey]['FULL_DAY'] = 
        $bookingsData[$dateKey]['MORNING'] && $bookingsData[$dateKey]['EVENING'];
}

$bookingStmt->close();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($chalet['name']); ?>-Chalet Details</title>
    <link rel="stylesheet" href="../css/chaletDetails.css" />
    <link rel="stylesheet" href="../css/nav.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
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
    <a href="reservations.php">Bookings</a>
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

    <div class="container">
      <div class="back-button-container">
        <a href="chaletList.php" class="back-button">
          <i class="fas fa-arrow-left"></i> Back to Chalets List
        </a>
      </div>
          
      <section class="chalet-details">
        <div class="details-left">
          <img id="main-image" src="<?php echo $images[0]; ?>" alt="<?php echo htmlspecialchars($chalet['name']); ?>" class="main-image" />
          <div class="thumbnail-container" id="thumbnail-container">
               <?php foreach ($images as $index => $image): ?>
                        <img src="<?php echo $image; ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="thumbnail" onclick="changeMainImage('<?php echo $image; ?>')" />
                    <?php endforeach; ?>
          </div>
        </div>

        <div class="details-right">
 <h2 id="chalet-name"><?php echo htmlspecialchars($chalet['name']); ?></h2>
 <div class="location-rating">
                    <span class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($chalet['Location']); ?>
                    </span>
                    <span class="rating">
                        <i class="fas fa-star"></i>
                        <?php echo number_format($chalet['avg_rating'], 1); ?> (<?php echo $chalet['review_count']; ?> reviews)
                    </span>
                </div>
           <h3>Description</h3>
           <p id="chalet-description"><?php echo htmlspecialchars($chalet['description']); ?></p>
            <span>Capacity: <?php echo $chalet['capacity']; ?> people</span>
            <div class="price-info">
              <h3>Price per night</h3>
                    <span>$<?php echo $chalet['price']; ?> / night</span>
                </div>
                <div class="owner-info">
        <h3>Owner Information</h3>
        <div class="owner-details">
            <div class="owner-item">
                <i class="fas fa-user"></i>
                <span><?php echo htmlspecialchars($owner['FirstName'] . ' ' . $owner['LastName']); ?></span>
            </div>
            <div class="owner-item">
                <i class="fas fa-phone"></i>
                <span><?php echo htmlspecialchars($owner['phoneNumber']); ?></span>
            </div>
            <div class="owner-item"> 
                <i class="fas fa-envelope"></i>
                <span><?php echo htmlspecialchars($owner['email']); ?></span>
            </div>
        </div>
    </div>
</div>

      </section>

      <section class="reservation-section">
        <h2>Make a Reservation</h2>

        <div class="calendar-header">
          <div class="nav-buttons">
            <button id="prev-month"<?= !$isLoggedIn ? 'disabled' : '' ?>>&lt;</button>
            <button id="today"<?= !$isLoggedIn ? 'disabled' : '' ?>>Today</button>
          </div>
          <div class="month-year" id="month-year">June 2023</div>
          <div class="nav-buttons">
            <button id="next-month"<?= !$isLoggedIn ? 'disabled' : '' ?>>&gt;</button>
          </div>
        </div>

        <div class="calendar-grid" id="calendar-grid">
          <div class="day-header">Sun</div>
          <div class="day-header">Mon</div>
          <div class="day-header">Tue</div>
          <div class="day-header">Wed</div>
          <div class="day-header">Thu</div>
          <div class="day-header">Fri</div>
          <div class="day-header">Sat</div>
        </div>

        <div class="legend">
          <div class="legend-item">
            <div
              class="legend-color"
              style="background-color: #d4edda; border: 1px solid #28a745"
            ></div>
            <span>Available</span>
          </div>
          <div class="legend-item">
            <div
              class="legend-color"
              style="background-color: #fff3cd; border: 1px solid #ffc107"
            ></div>
            <span>Partially Booked</span>
          </div>
          <div class="legend-item">
            <div
              class="legend-color"
              style="background-color: #f8d7da; border: 1px solid #dc3545"
            ></div>
            <span>Fully Booked</span>
          </div>
        </div>

        <div class="booking-panel" id="booking-panel" <?= !$isLoggedIn ? 'style="display:none;"' : '' ?>>
          <h3>Book for <span id="selected-date">June 15, 2023</span></h3>
          <div class="time-slots">
           <div class="time-slot" data-slot="MORNING">
    Morning (8am-8pm) - $<?php echo $chalet['price']; ?>
</div>
<div class="time-slot" data-slot="EVENING">
    Evening (8pm-8am) - $<?php echo $chalet['price']; ?>
</div>
<div class="time-slot" data-slot="FULL_DAY">
    Full Day - $<?php echo $chalet['price'] * 2; ?>
</div>
          </div>
          <div class="booking-details">
                      <?php if ($isLoggedIn): ?>
            <button class="book-btn" id="book-btn">Book Now</button>
            <?php endif; ?>
          </div>
        </div>
      </section>
      <section class="reviews-section">
        <div class="reviews-header">
                <h2>Guest Reviews (<?php echo count($reviews); ?>)</h2>
          <?php if ($isLoggedIn): ?>
          <button class="add-review-btn" id="add-review-btn">
            Add Your Review
          </button>
            <?php endif; ?>
        </div>

        <div class="reviews-container" id="reviews-container">
          <?php if (empty($reviews)): ?>
                    <p class="no-reviews">No reviews yet. Be the first to review this chalet!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
          <div class="review-card">
             <div class="review-user"><?php echo htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']); ?></div>
             <div class="review-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
            
             <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
            <div class="review-content"><?php echo htmlspecialchars($review['comment']); ?></div>
          </div>
            <?php endforeach; ?>
                <?php endif; ?>
        </div>
<?php if ($isLoggedIn): ?>
        <div class="review-form" id="review-form" >
                <h3>Write a Review</h3>
                <form action="AddReview.php" method="POST">
                    <input type="hidden" name="chalet_id" value="<?php echo $chaletId; ?>">
                    <div class="star-rating">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" />
                            <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                        <?php endfor; ?>
                    </div>
                    <textarea class="review-textarea" name="comment" placeholder="Share your experience..." required></textarea>
                    <button type="submit" class="submit-review-btn">Submit Review</button>
                </form>
            </div>
        <?php endif; ?>
      </section>
    </div>

    <footer class="footerEnd">
      <p>&copy; 2025 ChaletBooking.</p>
    </footer>
    <script>
    const chaletData = {
        id: <?php echo $chaletId; ?>,
        name: "<?php echo addslashes($chalet['name']); ?>",
        location: "<?php echo addslashes($chalet['Location']); ?>",
        price: <?php echo $chalet['price']; ?>,
        rating: <?php echo $chalet['avg_rating']; ?>,
        reviewCount: <?php echo $chalet['review_count']; ?>,
        capacity: <?php echo $chalet['capacity']; ?>,
        description: "<?php echo addslashes($chalet['description']); ?>",
        images: <?php echo json_encode($images); ?>,
    };

    const bookings = <?php echo json_encode($bookingsData); ?>;
</script>
    <script>
    <?php if (!empty($reviewMessage)): ?>
        alert("<?php echo addslashes($reviewMessage); ?>");
        const cleanUrl = window.location.pathname + '?id=' + <?php echo $chaletId; ?>;
        window.history.replaceState({}, document.title, cleanUrl);
    <?php endif; ?>
</script>
    <script src="../js/nav.js"></script>
    <script src="../js/chaletDetails.js"></script>
  </body>
</html>
