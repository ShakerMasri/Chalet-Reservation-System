<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$ownerId = $_SESSION['userId'];

if (isset($_GET['delete'])) {
    $bookingId = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM bookings WHERE bookingId = ?");
    $stmt->bind_param("i", $bookingId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Booking deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting booking: " . $conn->error;
    }
    $stmt->close();
    header("Location: BookingMange.php");
    exit();
}
 $filter = $_GET['filter'] ?? 'all'; 

$sql = "SELECT b.bookingId, c.name AS chaletName, b.booking_date, b.slot,
               CONCAT(u.FirstName, ' ', u.LastName) AS userName, c.price
        FROM bookings b
        JOIN chalet c ON b.chalet_id = c.chaletId
        JOIN users u ON b.user_id = u.userId
        WHERE c.ownerId = ?";

if ($filter === 'upcoming') {
    $sql .= " AND b.booking_date >= CURDATE()";
} elseif ($filter === 'past') {
    $sql .= " AND b.booking_date < CURDATE()";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage-Booking</title>
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
          <a href="ownerDashboard.php"
            ><i class="fas fa-tachometer-alt"></i> Dashboard</a
          >
          <a href="#"><i class="fas fa-calendar-alt"></i> Bookings</a>
          <a href="ownerDashboard.php"
            ><i class="fas fa-home"></i> My Chalets</a
          >
          <a href="Reviews.php"><i class="fas fa-star"></i> Reviews</a>
        </nav>
        <button class="logout" onclick="logout()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>
      <div class="dashboard-container">
        <section class="chalets-table-section">
          <div class="table-header">
            <h2>Booking Management</h2>
            <div class="table-controls">
                <form method="GET" id="filterForm">
  <div class="filter-control">
    <select name="filter" onchange="document.getElementById('filterForm').submit()">
      <option value="all"     <?= ($_GET['filter'] ?? '') === 'all' ? 'selected' : '' ?>>All Bookings</option>
      <option value="upcoming" <?= ($_GET['filter'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Upcoming Bookings</option>
      <option value="past"     <?= ($_GET['filter'] ?? '') === 'past' ? 'selected' : '' ?>>Past Bookings</option>
    </select>
  </div>
</form>
             
              <div class="search-control">
                <input
                  type="search"
                  id="table-search"
                  placeholder="Search User..."
                  aria-label="Search User"
                />
                <button class="search-btn" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="chalets-table">
              <thead>
                <tr>
                  <th class="sortable">
                    Booking ID <i class="fas fa-sort sort-icon"></i>
                  </th>
                  <th class="sortable">
                    Chalet Name <i class="fas fa-sort sort-icon"></i>
                  </th>
                  <th class="sortable">
                  Date<i class="fas fa-sort sort-icon"></i>
                  </th>
                  <th class="sortable">
                  Slot<i class="fas fa-sort sort-icon"></i>
                  </th>
                  <th class="sortable">
                    User Name <i class="fas fa-sort sort-icon"></i>
                  </th>
                  <th class="sortable">
                    Total Price<i class="fas fa-sort sort-icon"></i>
                  </th>
                    <th>Actions</th>    
                </tr>
              </thead>
                  <tbody>
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price = ($row['slot'] === 'FULL_DAY') ? $row['price'] * 2 : $row['price'];
        echo "<tr>
                <td>#{$row['bookingId']}</td>
                <td>{$row['chaletName']}</td>
                <td>{$row['booking_date']}</td>
                <td>{$row['slot']}</td>
                <td>{$row['userName']}</td>
                <td>\${$price}</td>
                <td>
                      <a href='BookingMange.php?delete=" . $row['bookingId'] . "' 
               class='delete-btn' style='text-decoration:none;'
               onclick=\"return confirm('Are you sure you want to delete this booking?');\">
               <i class='fas fa-trash-alt'></i> Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No bookings found</td></tr>";
}
?>
</tbody>
             
            </table>
          </div>
        </section>
      </div>
    </div>
       <script src="../js/owner.js"></script>
  </body>
</html>
