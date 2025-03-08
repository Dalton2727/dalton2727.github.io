<?php
//if user wants to delete the post 
session_start();
include 'dbconnection.php';
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$revid = isset($_POST['revid']) ? $_POST['revid'] : '';

//makes sure the user is referencing the right post and that the user created the post in order to delete it
if ($revid == ''){
    echo "Please input the id of the review you want to delete";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    $sql = "SELECT * FROM reviews WHERE username = ? AND id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "si", $userid, $revid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num = mysqli_num_rows($result);
}
if ($num < 1){
    echo "Please choose one of your reviews";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    $sql = "DELETE FROM reviews WHERE id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $revid);
    mysqli_stmt_execute($stmt);
    echo "Review has been deleted.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}

?>