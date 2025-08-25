<?php
session_start();
require_once '../config.php';

function updateChaletStats($chaletId) {
    global $conn;

    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE chaletId = ?");
    $stmt->bind_param("i", $chaletId);
    $stmt->execute();
    $stmt->bind_result($avg_rating, $total_reviews);
    $stmt->fetch();
    $stmt->close();

    if ($avg_rating === null) {
        $avg_rating = 0;
    }

    $stmt = $conn->prepare("UPDATE chalet SET avg_rating = ?, review_count = ? WHERE chaletId = ?");
    $stmt->bind_param("dii", $avg_rating, $total_reviews, $chaletId);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['chalet_id'])) {
    $reviewId = intval($_POST['review_id']);
    $chaletId = intval($_POST['chalet_id']);
    $userId = $_SESSION['userId'];

    $sql = "DELETE FROM reviews WHERE reviewId = ? AND userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reviewId, $userId);
    if ($stmt->execute()) {
        $stmt->close();

        updateChaletStats($chaletId);

header("Location: chaletDetails.php?id=$chaletId&review=deleted&message=" . urlencode("Review deleted successfully!"));
    } else {
        $stmt->close();
        header("Location: chaletDetails.php?id=$chaletId&review=error&message=" . urlencode("Failed to delete review."));
    }
    exit();
}

header('Location: chaletDetails.php');
exit();
