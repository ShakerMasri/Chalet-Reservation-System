<?php
session_start();
require_once '../config.php';

if (isset($_GET['delete'])) {
    $ownerId = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM users WHERE userId = ?");
    $stmt->bind_param("i", $ownerId);
    
   if ($stmt->execute()) {
    $_SESSION['message'] = "Owner deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting owner: " . $conn->error;
}
$stmt->close();
header("Location: manageOwner.php");
}
$sql = "SELECT 
    u.userId,
    CONCAT(u.FirstName, ' ', u.LastName) AS owner_name,
    u.email,
    u.phoneNumber,
    c.chaletId
    FROM users u
    LEFT JOIN chalet c ON u.userId = c.ownerId
    WHERE u.Role = 'owner'";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <title>Manage-Owner</title>
  </head>
  <body>
    <div class="dashboard">
      <aside class="sidebar">
        <img class= "logo" src="../images/beach-hut.png" alt="logo" />
        <hr />
        <nav>
          <a href="../admin.php"><i class="fas fa-home"></i> Home</a>
          <a href="addChalet.php"
            ><i class="fas fa-plus-circle"></i> Add Chalet</a
          >
          <a href="ViewChalet.php"><i class="fas fa-eye"></i>View chalet</a>
          <a href="addOwner.php"><i class="fas fa-user-plus"></i> Add Owner</a>
          <a href="#"><i class="fas fa-users-cog"></i> Manage Owners</a>
        </nav>
        <button class="logout" onclick="logout2()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>
      <div class="dashboard-container">
        <section class="chalets-table-section">
          <div class="table-header">
            <h2>Owner Management</h2>
            <div class="table-controls">
              <div class="search-control">
                <input
                  type="search"
                  id="table-search"
                  placeholder="Search Owners..."
                  aria-label="Search Owner"
                  oninput="filterTable()"
                />
                <button class="search-btn" type="button">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="chalets-table">
              <thead>
                <tr>
                  <th class="sortable" onclick="sortTable(0)">
                    Owner ID <i class="fas fa-sort sort-icon" id="sort-icon-0"></i>
                  </th>
                  <th class="sortable" onclick="sortTable(1)">
                    Owner Name <i class="fas fa-sort sort-icon" id="sort-icon-1"></i>
                  </th>
                  <th>
                    E-mail 
                  </th>
                  <th>
                    Phone Number 
                  </th>
                  <th>
                    Chalet ID 
                  </th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="owner-table-body">
                <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td data-sort-value="<?= $row['userId']; ?>">#<?= htmlspecialchars($row['userId']); ?></td>
                    <td><?= htmlspecialchars($row['owner_name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['phoneNumber']); ?></td>
                    <td>#<?= htmlspecialchars($row['chaletId']); ?></td>
                    <td class="actions">
                      <a href="manageOwner.php?delete=<?= $row['userId']; ?>" 
                      class="delete-btn" style="text-decoration:none;"
                     onclick="return confirm('Are you sure you want to delete this owner?');">
                     <i class="fas fa-trash-alt"></i> Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">No owners found.</td>
                </tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
    
    <script src="../js/admin.js"></script>
  </body>
</html>