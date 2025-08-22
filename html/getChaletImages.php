<?php
require_once 'config.php';
if (isset($_GET['id'])) {
    $chaletId = intval($_GET['id']);
    $sql = "SELECT image_path FROM chalet_images WHERE chalet_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            if (strpos($row['image_path'], 'golden/') === false) {
                $row['image_path'] = '../images/golden/' . basename($row['image_path']);
            }
        }
        $images[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($images);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Chalet ID required']);
}
?>
