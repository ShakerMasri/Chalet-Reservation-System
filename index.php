<?php
session_start();
require_once 'config.php';
$isLoggedIn = false;
if (!isset($_SESSION['email']) || $_SESSION['Role'] !== 'user') {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
}

$sql = "SELECT c.chaletId, c.name, c.Location, c.price, c.avg_rating, c.review_count,
               (SELECT image_path FROM chalet_images ci 
                WHERE ci.chalet_id = c.chaletId 
                LIMIT 1) AS image_path
        FROM chalet c
        ORDER BY c.avg_rating DESC, c.review_count DESC
        LIMIT 5";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ChaletBooking</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/nav.css" />
    <link rel="stylesheet" href="./css/home.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
  </head>
  <body>
    <header>
      <nav class="navbar">
        <div class="logodiv">
          <a href="index.php">
            <img class="logo" src="./images/beach-hut.png" alt="logo" />
          </a>
          <h1>Chalet</h1>
          <span>Booking</span>
        </div>
        <div class="navfill"></div>

        <div class="navlist">
          <ul>
            <li><a href="./index.php">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#Highest-Rating">Chalets</a></li>
            <li><a href="#Highest-Rating">Highest rating</a></li>
           
    <?php if ($isLoggedIn): ?>
         <li class="dropdown">
  <button class="dropbtn">
    <i class="fas fa-user"></i> <?= $_SESSION['FirstName']?> <span class="arrow">▼</span>
  </button>
  <div class="dropdown-content">
    <a href="#">Wishlist</a>
    <a href="#">Bookings</a>
    <a href="./html/logout.php">Logout</a>
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

    <main id="main-content">
      <section id="swiper-section" class="swiper-section">
        <div class="swiper-container">
          <div id="swiper-wrapper" class="swiper-wrapper">
            <div class="swiper-slide">
              <img src="./images/interiorpics/int4.jpg" alt="Slide 1" />
            </div>
            <div class="swiper-slide">
              <img src="./images/poolpics/pool5.jpg" alt="Slide 2" />
            </div>
            <div class="swiper-slide">
              <img src="./images/poolpics/pool4.jpg" alt="Slide 3" />
            </div>
          </div>

          <div id="next" class="arrWrap prev">
            <div class="swiper-button next"></div>
          </div>
          <div id="prev" class="arrWrap next">
            <div class="swiper-button prev"></div>
          </div>
      
        
        <div class="slider-overlay">
          <div class="slider-content">
            <h2>Your Perfect Getaway Starts Here</h2>
            <p>Book beautiful chalets in seconds.</p>
            <a href="./html/chaletList.php" class="modern-btn">
              Explore Chalets
            </a>
          </div>
        </div>
      </section>
      <section id="about" class="about">
        <div class="main">
          <h2>Why Choose Our Chalet?</h2>
          <p class="subtitle">
            We offer the finest selection of chalet with premium amenities.
          </p>
          <div class="inner-section">
            <div class="grid">
              <div class="card">
                <div class="icon">
                  <i class="fas fa-home"></i>
                </div>
                <h3>Premium Properties</h3>
                <p>
                  Carefully selected chalets with stunning views and top-rated
                  amenities.
                </p>
              </div>

              <div class="card">
                <div class="icon">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Booking</h3>
                <p>
                  Your reservation is 100% secure with our verified booking
                  system.
                </p>
              </div>

              <div class="card">
                <div class="icon">
                  <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>
                  Our team is always available to assist with your booking
                  needs.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="Highest-Rating" class="chalets-section">
      <div class="container">
        <h2>Featured Chalets</h2>
        <p class="subtitle">Discover our most Highest Rating Chalet</p>

        <div class="chalets-slider">
          <div class="chalets-track">
           
          <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="chalet-card">
            <div class="chalet-image">
               <img src="<?= htmlspecialchars('./images/golden/' . ($row['image_path'] )) ?>" 
     alt="<?= htmlspecialchars($row['name']) ?>" />
            </div>
            <div class="chalet-info">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <div class="rating">
                    <?php
                    $fullStars = floor($row['avg_rating']);
                    $halfStar = ($row['avg_rating'] - $fullStars) >= 0.5;
                    for ($i = 0; $i < $fullStars; $i++) {
                        echo '<i class="fas fa-star"></i>';
                    }
                    if ($halfStar) {
                        echo '<i class="fas fa-star-half-alt"></i>';
                    }
                    for ($i = $fullStars + $halfStar; $i < 5; $i++) {
                        echo '<i class="far fa-star"></i>';
                    }
                    ?>
                    <span><?= number_format($row['avg_rating'], 1) ?> (<?= $row['review_count'] ?>)</span>
                </div>
                <p class="location">
                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['Location']) ?>
                </p>
                <p class="price">$<?= number_format($row['price'], 2) ?> <span>/ night</span></p>
                <a href="./html/chaletDetails.php?id=<?= $row['chaletId'] ?>" class="btn-secondary">View Details</a>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No chalets available yet.</p>
<?php endif; ?>
            

          </div>
        </div>
      </div>
    </section>


    </main>

    <footer class="footerEnd">
      <p>&copy; 2025 ChaletBooking.</p>
    </footer>
    <script src="./js/main.js"></script>
  </body>
</html>
