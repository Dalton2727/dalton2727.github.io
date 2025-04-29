<?php 
session_start();
include 'dbconnection.php';

$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$meal = isset($_POST['meal']) ? $_POST['meal'] : '';
$rating = isset($_POST['rating']) ? $_POST['rating'] : '';
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

// Check that required fields are filled
if ($location == '' || $meal == '' || $rating == '') {
    echo "All required fields must be filled in to write a review. Please try again.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    // Insert including review_text (even if it's empty)
    $sql = "INSERT INTO reviews (username, location, meal, rating, review_text) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $userid, $location, $meal, $rating, $review_text);
    mysqli_stmt_execute($stmt);
    
    echo "Review has been posted.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}
?>
