<?php
require_once '../config.php';
session_start();
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
          <a href="BookingMange.html"
            ><i class="fas fa-calendar-alt"></i> Bookings</a
          >
          <a href="#chalets"><i class="fas fa-home"></i> My Chalets</a>
          <a href="Reviews.html"><i class="fas fa-star"></i> Reviews</a>
        </nav>
        <button class="logout">
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
            <h2>12</h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-home"></i>
            <p>Chalets</p>
            <h2>2</h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-star"></i>
            <p>Average Rating</p>
            <h2>4.8</h2>
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
            <div class="chalet-card">
              <div
                class="chalet-image"
                style="background-image: url('../images/Home.jpg')"
              ></div>
              <div class="chalet-info">
                <h3>Almoluk-Chalet</h3>
                <div class="chalet-meta">
                  <span><i class="fas fa-map-marker-alt"></i> Nablus</span>
                </div>
                <div class="chalet-actions">
                  <button class="view-btn">
                    <i class="fas fa-eye"></i> View-Details
                  </button>
                </div>
              </div>
            </div>
            <div class="chalet-card">
              <div
                class="chalet-image"
                style="background-image: url('../images/Home.jpg')"
              ></div>
              <div class="chalet-info">
                <h3>Almoluk-Chalet</h3>
                <div class="chalet-meta">
                  <span><i class="fas fa-map-marker-alt"></i> Nablus</span>
                </div>
                <div class="chalet-actions">
                  <button class="view-btn">
                    <i class="fas fa-eye"></i> View-Details
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </body>
</html>
