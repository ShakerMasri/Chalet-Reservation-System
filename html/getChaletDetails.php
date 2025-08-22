<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $chaletId = intval($_GET['id']);
    $sql = "SELECT * FROM chalet WHERE chaletId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $chalet = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($chalet);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Chalet not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Chalet ID required']);
}
?>