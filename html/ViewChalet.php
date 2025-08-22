<?php
session_start();  
require_once '../config.php'; 
$sql = "SELECT c.chaletId, c.name AS chaletName, 
               CONCAT(u.FirstName, ' ', u.LastName) AS ownerName,
               c.Location, c.capacity, c.price, c.description
        FROM chalet c
        LEFT JOIN users u ON c.ownerId = u.userId
        ORDER BY c.chaletId ASC";

$result = $conn->query($sql);
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
if (isset($_GET['delete'])) {
    $chaletId = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM chalet WHERE chaletId = ?");
    $stmt->bind_param("i", $chaletId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Chalet deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting chalet: " . $conn->error;
    }
    $stmt->close();
    header("Location: ViewChalet.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View-Chalet</title>
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
          <a href="../admin.php"><i class="fas fa-home"></i> Home</a>
           <a href="addChalet.php"
            ><i class="fas fa-plus-circle"></i> Add Chalet</a
          >
          <a href="#"><i class="fas fa-eye"></i>View chalet</a>
          <a href="addOwner.php"><i class="fas fa-user-plus"></i> Add Owner</a>
          <a href="manageOwner.php"><i class="fas fa-users-cog"></i> Manage Owners</a>
        </nav>
        <button class="logout" onclick="logout2()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>
     <div class="dashboard-container">
    <section class="chalets-table-section">
      <div class="table-header">
        <h2>Chalet Management</h2>
        <div class="table-controls">
          <div class="search-control">
  <input type="search" id="table-search" placeholder="Search chalets..." aria-label="Search chalets">
  <button class="search-btn" type="submit"><i class="fas fa-search"></i></button>
</div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="chalets-table">
          <thead>
            <tr>
              <th class="sortable">Chalet ID <i class="fas fa-sort sort-icon"></i></th>
              <th class="sortable">Chalet Name</th>
              <th class="sortable">Owner Nmae </th>
              <th class="sortable">Location <i class="fas fa-sort sort-icon"></i></th>
              <th class="sortable">Capacity <i class="fas fa-sort sort-icon"></i></th>
              <th class="sortable">Price <i class="fas fa-sort sort-icon"></i></th>
              <th class="sortable">Rating <i class="fas fa-sort sort-icon"></i></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>#<?= htmlspecialchars($row['chaletId']); ?></td>
            <td><?= htmlspecialchars($row['chaletName']); ?></td>
            <td><?= htmlspecialchars($row['ownerName']); ?></td>
            <td><?= htmlspecialchars($row['Location']); ?></td>
            <td><?= htmlspecialchars($row['capacity']); ?></td>
            <td><?= htmlspecialchars($row['price']); ?></td>
            <td>
                <?php
                $rating = getChaletRating($row['chaletId']);
                echo htmlspecialchars($rating);
                ?>
            </td>
            <td class="actions">
                <a href="addChalet.php?id=<?= $row['chaletId']; ?>" class="edit-btn" style="text-decoration:none;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                 <a href="ViewChalet.php?delete=<?= $row['chaletId']; ?>" 
                      class="delete-btn" style="text-decoration:none;"
                     onclick="return confirm('Are you sure you want to delete this chalet?');">
                     <i class="fas fa-trash-alt"></i> Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="8">No chalets found.</td>
    </tr>
<?php endif; ?>
<script>
  const searchInput = document.getElementById('table-search');
  const table = document.querySelector('.chalets-table tbody');
  searchInput.addEventListener('input', function() {
    const filter = searchInput.value.toLowerCase();
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
      const cells = rows[i].getElementsByTagName('td')[1];
      let match = false;
      for (let j = 0; j < cells.length - 1; j++) { 
        if (cells[j].textContent.toLowerCase().includes(filter)) {
          match = true;
          break;
        }
      }
      rows[i].style.display = match ? '' : 'none';
    }
  });
</script>
          </tbody>
        </table>
      </div>
      
    </section>
  </div>
    </div>

    <script src="../js/admin.js"></script>
  </body>
</html>
