<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$revid = isset($_POST['revid']) ? $_POST['revid'] : '';


if ($revid == ''){
    echo "Please input the id of the review you want to delete";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    $sql = "SELECT * FROM reviews WHERE username = '$userid' AND id = $revid";
    $result = mysqli_query($db, $sql);
    $num = mysqli_num_rows($result);
}
if ($num < 1){
    echo "Please choose one of your reviews";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
} else {
    $sql = "DELETE FROM reviews WHERE id=$revid";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    echo "Review has been deleted.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
}

?>