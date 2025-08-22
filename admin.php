<?php
require_once 'config.php';
session_start();
$sql = "SELECT COUNT(*) AS total_chalets FROM chalet";
$sql2 = "SELECT COUNT(*) AS total_users FROM users WHERE Role IN ('owner', 'user')";
$sql3 ="SELECT COUNT(*) AS total_bookings FROM bookings";

$result = $conn->query($sql);
$result2 = $conn->query($sql2);
$result3=$conn->query($sql3);

$totalChalets = 0;
$totalUsers = 0;
$totalBookings = 0;
if ($result3 && $row = $result3->fetch_assoc()) {
    $totalBookings = $row['total_bookings'];
}
if ($result && $row = $result->fetch_assoc()) {
    $totalChalets = $row['total_chalets'];
}
if ($result2 && $row = $result2->fetch_assoc()) {
    $totalUsers = $row['total_users'];
}
$sqlRecentUsers = "SELECT userId, CONCAT(FirstName,' ',LastName) AS name, email, phoneNumber, Role, registeredAt 
                   FROM users 
                   ORDER BY registeredAt DESC 
                   LIMIT 5";

$resultRecentUsers = $conn->query($sqlRecentUsers);
$recentUsers = [];

if ($resultRecentUsers) {
    while ($row = $resultRecentUsers->fetch_assoc()) {
        $recentUsers[] = $row;
    }
}
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
          <a href="./html/addChalet.php"
            ><i class="fas fa-plus-circle"></i> Add Chalet</a
          >
          <a href="./html/ViewChalet.php"
            ><i class="fas fa-eye"></i>View chalet</a
          >
          <a href="./html/addOwner.php"
            ><i class="fas fa-user-plus"></i> Add Owner</a
          >
          <a href="./html/manageOwner.php"
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
            <h2><?=$totalUsers?></h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-home"></i>
            <p>Chalets</p>
            <h2><?= $totalChalets ?></h2>
          </div>
          <div class="stat-card">
            <i class="fas fa-calendar-alt"></i>
            <p>Total Bookings</p>
            <h2><?= $totalBookings?></h2>
          </div>
        </section>

        <section class="bookings">
          <h2>Latest 5 Registered</h2>
          <table>
            <thead>
              <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Registered Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $user): ?>
<tr>
    <td>#<?= $user['userId'] ?></td>
    <td><?= htmlspecialchars($user['name']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= htmlspecialchars($user['phoneNumber']) ?></td>
    <td><?= htmlspecialchars($user['Role']) ?></td>
    <td><?= htmlspecialchars($user['registeredAt']) ?></td>
</tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
    <script src="./js/admin.js"></script>
  </body>
</html>
