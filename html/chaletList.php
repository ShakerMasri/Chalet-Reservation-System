<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email']) || $_SESSION['Role'] !== 'user') {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
}

$chalets = [];
$sql = "SELECT c.*, 
               (SELECT image_path FROM chalet_images WHERE chalet_id = c.chaletId LIMIT 1) as primary_image
        FROM chalet c 
        WHERE c.ownerId IN (SELECT userId FROM users WHERE Role = 'owner')";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if (!empty($row['primary_image'])) {
            if (strpos($row['primary_image'], '/') === false) {
                $row['primary_image'] = '../images/golden/' . $row['primary_image'];
            }
            else if (strpos($row['primary_image'], 'golden/') === false) {
                $row['primary_image'] = '../images/golden/' . basename($row['primary_image']);
            }
        } else {
            $row['primary_image'] = '../images/Home.jpg';
        }
        
        $chalets[] = $row;
    }
}

$chalets_json = json_encode($chalets);
?>
 <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chalet Reservation System</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/nav.css" />
    <link rel="stylesheet" href="../css/chaletList.css" />
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

    <main>
      <div class="container">
          <div class="simple-filter">
    <h3>Filter Chalets</h3>
    <div class="filter-controls">
        <div class="filter-group">
            <label for="priceFilter">Price Range:</label>
            <select id="priceFilter" onchange="filterChalets()">
                <option value="all">All Prices</option>
                <option value="price-100">Under $100</option>
                <option value="price-200">$100 - $200</option>
                <option value="price-300">$200 - $300</option>
                <option value="price-300+">$300+</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="ratingFilter">Minimum Rating:</label>
            <select id="ratingFilter" onchange="filterChalets()">
                <option value="all">All Ratings</option>
                <option value="rating-2">2+ Stars</option>
                <option value="rating-3">3+ Stars</option>
                <option value="rating-4">4+ Stars</option>
                <option value="rating-5">5 Stars</option>
            </select>
        </div>
        
        <button class="clear-filter-btn" onclick="clearFilters()">
            <i class="fas fa-times"></i> Clear Filters
        </button>
    </div>
</div>
        <div class="chalets" id="chaletscont"></div>
      </div>
    </main>

    <footer class="footerEnd">
      <p>&copy; 2025 ChaletBooking.</p>
    </footer>
     <script>
        const dbChalets = <?php echo $chalets_json; ?>;
    </script>
    <script src="../js/nav.js"></script>
    <script src="../js/chaletList.js"></script>
  </body>
</html>
