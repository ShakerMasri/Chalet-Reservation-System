<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $chaletId = intval($_GET['id']);
    $sql = "SELECT r.*, u.FirstName 
            FROM reviews r 
            JOIN users u ON r.userId = u.userId 
            WHERE r.chaletId = ? 
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($reviews);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Chalet ID required']);
}
?>