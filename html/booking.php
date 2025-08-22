<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['userId'])) {
    echo json_encode(["status" => "error", "message" => "You must login first"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['userId'];
    $chaletId = intval($_POST['chalet_id']);
    $bookingDate = $_POST['date'];
    $slot = $_POST['slot'];

    $currentDate = date('Y-m-d');
    if ($bookingDate < $currentDate) {
        echo json_encode(["status" => "error", "message" => "Cannot book dates in the past. Please select a current or future date."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT price FROM chalet WHERE chaletId = ?");
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    if ($slot === "FULL_DAY") {
        $price = $price * 2;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE chalet_id = ? AND booking_date = ? AND slot = ?");
    $stmt->bind_param("iss", $chaletId, $bookingDate, $slot);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        echo json_encode(["status" => "error", "message" => "This slot is already booked."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, chalet_id, booking_date, slot) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $userId, $chaletId, $bookingDate, $slot);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Booking confirmed!", "price" => $price]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to book. Try again."]);
    }
    $stmt->close();
}
?>