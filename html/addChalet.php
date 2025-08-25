<?php
session_start();
require_once '../config.php';

$editMode = false;
$chalet = [
    'ownerId' => '',
    'name' => '',
    'Location' => '',
    'price' => '',
    'capacity' => '',
    'description' => ''
];
$existingImages = [];

if (isset($_GET['id'])) {
    $editMode = true;
    $chaletId = intval($_GET['id']);
    
    // Get chalet details
    $stmt = $conn->prepare("SELECT ownerId, name, Location, price, capacity, description FROM chalet WHERE chaletId=?");
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $stmt->bind_result($chalet['ownerId'], $chalet['name'], $chalet['Location'], $chalet['price'], $chalet['capacity'], $chalet['description']);
    $stmt->fetch();
    $stmt->close();
    
    // Get existing images
    $imgStmt = $conn->prepare("SELECT image_id, image_path FROM chalet_images WHERE chalet_id = ?");
    $imgStmt->bind_param("i", $chaletId);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();
    while ($image = $imgResult->fetch_assoc()) {
        $existingImages[] = $image;
    }
    $imgStmt->close();
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $imageId = intval($_GET['delete_image']);
    $deleteStmt = $conn->prepare("DELETE FROM chalet_images WHERE image_id = ?");
    $deleteStmt->bind_param("i", $imageId);
    if ($deleteStmt->execute()) {
        header("Location: addChalet.php?id=" . $chaletId . "&msg=image_deleted");
        exit;
    }
    $deleteStmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ownerId = intval($_POST['owner_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $price = floatval($_POST['price']);
    $max_capacity = intval($_POST['max_capacity']);
    $description = $conn->real_escape_string($_POST['description']);

    if ($editMode) {
        $stmt = $conn->prepare("UPDATE chalet SET ownerId=?, name=?, Location=?, price=?, capacity=?, description=? WHERE chaletId=?");
        $stmt->bind_param("issdisi", $ownerId, $name, $location, $price, $max_capacity, $description, $chaletId);
        $success = $stmt->execute();
        $stmt->close();
        
        // Handle new image uploads in edit mode
        if (!empty($_FILES['image']['name'][0])) {
            $uploadDir = "../images/golden/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['image']['error'][$key] !== UPLOAD_ERR_OK) {
                    continue;
                }
                $fileName = uniqid() . "_" . basename($_FILES['image']['name'][$key]);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $stmt = $conn->prepare("INSERT INTO chalet_images (chalet_id, image_path) VALUES (?, ?)");
                    $stmt->bind_param("is", $chaletId, $fileName);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
        
        if ($success) {
            header("Location: ViewChalet.php?msg=updated");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        $sql = "INSERT INTO chalet (ownerId, name, Location, price, capacity, description) 
                VALUES ('$ownerId', '$name', '$location', '$price', '$max_capacity', '$description')";
        if ($conn->query($sql) === TRUE) {
            $chaletId = $conn->insert_id;
            if (!empty($_FILES['image']['name'][0])) {
                $uploadDir = "../images/golden/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['image']['error'][$key] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $fileName = uniqid() . "_" . basename($_FILES['image']['name'][$key]);
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($tmp_name, $targetPath)) {
                        $stmt = $conn->prepare("INSERT INTO chalet_images (chalet_id, image_path) VALUES (?, ?)");
                        $stmt->bind_param("is", $chaletId, $fileName);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
            header("Location: ViewChalet.php?msg=added");
            exit;
        } else {
            echo "Error adding chalet: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $editMode ? 'Edit Chalet' : 'Add Chalet' ?></title>
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  </head>
  <body>
    <div class="dashboard">
      <aside class="sidebar">
        <img class="logo" src="../images/beach-hut.png" alt="logo" />
        <hr />
        <nav>
          <a href="../admin.php"><i class="fas fa-home"></i> Home</a>
          <a href="#"><i class="fas fa-plus-circle"></i> Add Chalet</a>
          <a href="ViewChalet.php"><i class="fas fa-eye"></i>View chalet</a>
          <a href="addOwner.php"><i class="fas fa-user-plus"></i> Add Owner</a>
          <a href="manageOwner.php"><i class="fas fa-users-cog"></i> Manage Owners</a>
        </nav>
        <button class="logout" onclick="logout2()">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
      </aside>
       <main class="main">
    <form class="add-form" method="POST" action="<?= $editMode ? 'addChalet.php?id=' . $chaletId : 'addChalet.php' ?>" enctype="multipart/form-data">
      <h1 class="form-title"><?= $editMode ? 'Edit Chalet' : 'Add New Chalet' ?></h1>

      <div class="form-grid">
        <div class="form-group">
          <label for="owner_id">Owner ID</label>
          <input
            type="number"
            id="owner_id"
            name="owner_id"
            placeholder="Enter Owner ID"
            required
            value="<?= htmlspecialchars($chalet['ownerId']) ?>"
          />
        </div>

        <div class="form-group">
          <label for="name">Chalet Name</label>
          <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter Chalet Name"
            required
            value="<?= htmlspecialchars($chalet['name']) ?>"
          />
        </div>

        <div class="form-group full-width">
          <label for="location">Location</label>
          <input
            type="text"
            id="location"
            name="location"
            placeholder="Enter Location"
            required
            value="<?= htmlspecialchars($chalet['Location']) ?>"
          />
        </div>

        <div class="form-group">
          <label for="price">Price ($)</label>
          <input
            type="number"
            id="price"
            name="price"
            step="1"
            placeholder="Enter Price"
            min="0"
            required
            value="<?= htmlspecialchars($chalet['price']) ?>"
          />
        </div>

        <div class="form-group">
          <label for="max_capacity">Max Capacity</label>
          <input
            type="number"
            id="max_capacity"
            name="max_capacity"
            placeholder="Enter Max Guests"
            min="1"
            required
            value="<?= htmlspecialchars($chalet['capacity']) ?>"
          />
        </div>

        <div class="form-group full-width">
          <label for="description">Description</label>
          <textarea
            id="description"
            name="description"
            placeholder="Enter Description"
          ><?= htmlspecialchars($chalet['description']) ?></textarea>
        </div>

        <div class="form-group full-width">
          <label>Chalet Images</label>
          <div class="image-upload">
            <label for="image-upload" class="image-upload-label">
              <i class="fas fa-cloud-upload-alt"></i>
              <span>Click to upload or drag and drop</span>
            </label>
            <input
              type="file"
              id="image-upload"
              name="image[]"
              accept="image/*"
              multiple 
            />
          </div>
          
          <?php if ($editMode && !empty($existingImages)): ?>
          <div class="existing-images">
            <h3>Existing Images</h3>
            <div class="image-gallery">
              <?php foreach ($existingImages as $image): ?>
              <div class="image-item">
                <img src="../images/golden/<?= htmlspecialchars($image['image_path']) ?>" 
                     alt="Chalet Image">
                <button type="button" class="delete-image" 
                        onclick="if(confirm('Delete this image?')) { window.location.href='addChalet.php?id=<?= $chaletId ?>&delete_image=<?= $image['image_id'] ?>'; }">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="submit-btn"><?= $editMode ? 'Update Chalet' : 'Add Chalet' ?></button>
    </form>
  </main>
</div>
<script src="../js/admin.js"></script>
  </body>
</html>