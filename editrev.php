<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$revid = isset($_POST['revid']) ? $_POST['revid'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$meal = isset($_POST['meal']) ? $_POST['meal'] : '';
$rating = isset($_POST['rating']) ? $_POST['rating'] : '';


if ($revid == ''){
    echo "Please input the id of the review you want to edit";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}else {
    $sql = "SELECT * FROM reviews WHERE username = '$userid' AND id = $revid";
    $result = mysqli_query($db, $sql);
    $num = mysqli_num_rows($result);
}
if ($num < 1){
    echo "Please choose one of your reviews";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    if ($location != ''){
        $sql = "UPDATE reviews SET location='$location' WHERE id=$revid";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_execute($stmt);
    }
    if ($meal != ''){
        $sql = "UPDATE reviews SET meal='$meal' WHERE id=$revid";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_execute($stmt);
    }
    if ($rating != ''){
        $sql = "UPDATE reviews SET rating='$rating' WHERE id=$revid";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_execute($stmt);
    }
    echo "Review has been updated.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}
?>