<?php
require_once 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="./css/admin.css" />
  </head>
  <body>
    <div class="dashboard">
      <aside class="sidebar">
        <img class="logo" src="./images/beach-hut.png" alt="logo" />
        <hr />
        <nav>
          <a href=""><i class="fas fa-home"></i> Home</a>
          <a href="./html/addChalet.html"
            ><i class="fas fa-plus-circle"></i> Add Chalet</a
          >
          <a href="./html/ViewChalet.html"
            ><i class="fas fa-eye"></i>View chalet</a
          >
          <a href="./html/addOwner.html"
            ><i class="fas fa-user-plus"></i> Add Owner</a
          >
          <a href="./html/manageOwner.html"
            ><i class="fas fa-users-cog"></i> Manage Owners</a
          >
        </nav>
        <button class="logout" onClick="logout()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>

      <main class="main">
        <header>
          <h1>Admin Dashboard</h1>
          <p>Welcome back, <?= ($_SESSION['FirstName'])?></p>
        </header>

        <section class="stats">
          <div class="stat-card">
            <i class="fas fa-users"></i>
            <p>Users</p>
            <h2>3</h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-home"></i>
            <p>Chalets</p>
            <h2>10</h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-calendar-alt"></i>
            <p>Total Bookings</p>
            <h2>14</h2>
          </div>
        </section>

        <section class="bookings">
          <h2>Latest Bookings</h2>
          <table>
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Chalet</th>
                <th>User</th>
                <th>Phone</th>
                <th>Booking Date</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#1</td>
                <td>almoluk-Chalet</td>
                <td>test@gmail.com</td>
                <td>0596060606</td>
                <td>2025-07-07 18:08</td>
              </tr>
              <tr>
                <td>#2</td>
                <td>alkoukh-Chalet</td>
                <td>test@gmail.com</td>
                <td>0596090909</td>
                <td>2025-07-10 10:00</td>
              </tr>
              <tr>
                <td>#3</td>
                <td>albaron-Chalet</td>
                <td>test@gmail.com</td>
                <td>0591060235</td>
                <td>2025-07-12 14:30</td>
              </tr>
            </tbody>
          </table>
        </section>
      </main>
    </div>
    <script src="./js/admin.js"></script>
  </body>
</html>
