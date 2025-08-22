<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email']) || $_SESSION['Role'] !== 'user') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chaletId = intval($_POST['chalet_id']);
    $userId = $_SESSION['userId'];
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string(trim($_POST['comment']));

    if ($rating < 1 || $rating > 5 || empty($comment)) {
        header("Location: chaletDetails.php?id=$chaletId&review=error&message=Invalid+input");
        exit();
    }

    $bookingCheckSql = "SELECT COUNT(*) as booking_count 
                        FROM bookings 
                        WHERE user_id = ? AND chalet_id = ? 
                        AND booking_date < CURDATE()";
    $bookingCheckStmt = $conn->prepare($bookingCheckSql);
    $bookingCheckStmt->bind_param("ii", $userId, $chaletId);
    $bookingCheckStmt->execute();
    $bookingResult = $bookingCheckStmt->get_result();
    $bookingData = $bookingResult->fetch_assoc();

    if ($bookingData['booking_count'] == 0) {
        header("Location: chaletDetails.php?id=$chaletId&review=error&message=You+can+only+review+chalets+you+have+booked+in+the+past");
        exit();
    }

    $existingReviewSql = "SELECT COUNT(*) as review_count 
                          FROM reviews 
                          WHERE userId = ? AND chaletId = ?";
    $existingReviewStmt = $conn->prepare($existingReviewSql);
    $existingReviewStmt->bind_param("ii", $userId, $chaletId);
    $existingReviewStmt->execute();
    $existingReviewResult = $existingReviewStmt->get_result();
    $existingReviewData = $existingReviewResult->fetch_assoc();
    
    if ($existingReviewData['review_count'] > 0) {
        header("Location: chaletDetails.php?id=$chaletId&review=error&message=You+have+already+reviewed+this+chalet");
        exit();
    }

    $sql = "INSERT INTO reviews (userId, chaletId, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $userId, $chaletId, $rating, $comment);
    
    if ($stmt->execute()) {
        $updateSql = "UPDATE chalet SET 
                     avg_rating = (SELECT AVG(rating) FROM reviews WHERE chaletId = ?),
                     review_count = (SELECT COUNT(*) FROM reviews WHERE chaletId = ?)
                     WHERE chaletId = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("iii", $chaletId, $chaletId, $chaletId);
        $updateStmt->execute();
        
        header("Location: chaletDetails.php?id=$chaletId&review=success");
    } else {
        header("Location: chaletDetails.php?id=$chaletId&review=error&message=Database+error");
    }
    exit();
} else {
    header("Location: chaletDetails.php?id=" . ($_POST['chalet_id'] ?? '') . "&review=error&message=Invalid+request");
}
?>