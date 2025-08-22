<?php
session_start();
require_once '../config.php';

if (!isset($_GET['chaletId']) || !is_numeric($_GET['chaletId'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Invalid chalet ID"]);
    exit();
}

$chaletId = intval($_GET['chaletId']);
$bookingsData = [];

$bookingSql = "SELECT booking_date, slot FROM bookings WHERE chalet_id = ?";
$bookingStmt = $conn->prepare($bookingSql);
$bookingStmt->bind_param("i", $chaletId);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();

while ($row = $bookingResult->fetch_assoc()) {
    $dateKey = $row['booking_date']; 
    if (!isset($bookingsData[$dateKey])) {
        $bookingsData[$dateKey] = [
            'MORNING' => true,
            'EVENING' => true,
            'FULL_DAY' => true
        ];
    }
    
    $bookingsData[$dateKey][$row['slot']] = false;
    
    if ($row['slot'] === 'FULL_DAY') {
        $bookingsData[$dateKey]['MORNING'] = false;
        $bookingsData[$dateKey]['EVENING'] = false;
    }
    
    $bookingsData[$dateKey]['FULL_DAY'] = 
        $bookingsData[$dateKey]['MORNING'] && $bookingsData[$dateKey]['EVENING'];
}

$bookingStmt->close();

header('Content-Type: application/json');
echo json_encode($bookingsData);
?>