<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$revid = isset($_POST['revid']) ? $_POST['revid'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$meal = isset($_POST['meal']) ? $_POST['meal'] : '';
$rating = isset($_POST['rating']) ? $_POST['rating'] : '';
$review_text = isset($_POST['review_text']) ? $_POST['review_text'] : '';

if ($revid == ''){
    echo "Please input the id of the review you want to edit";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}else {
    $sql = "SELECT * FROM reviews WHERE username = ? AND id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "si", $userid, $revid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num = mysqli_num_rows($result);
}
//makes sure review matches with user, if not, user then has to choose one of their reviews
//option to not change parts of review
if ($num < 1){
    echo "Please choose one of your reviews";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    if ($location != ''){
        $sql = "UPDATE reviews SET location= ? WHERE id= ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "si", $location, $revid);
        mysqli_stmt_execute($stmt);
    }
    if ($meal != ''){
        $sql = "UPDATE reviews SET meal= ? WHERE id= ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "si", $meal, $revid); 
        mysqli_stmt_execute($stmt);
    }
    if ($rating != ''){
        $sql = "UPDATE reviews SET rating=? WHERE id=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "di", $rating, $revid);
        mysqli_stmt_execute($stmt);
    }
    if ($review_text != ''){
        $sql = "UPDATE reviews SET review_text=? WHERE id=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "si", $review_text, $revid);
        mysqli_stmt_execute($stmt);
    }
    echo "Review has been updated.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}
?>