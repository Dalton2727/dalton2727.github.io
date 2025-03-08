<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$meal = isset($_POST['meal']) ? $_POST['meal'] : '';
$rating = isset($_POST['rating']) ? $_POST['rating'] : '';

//checks that we can upload reviews if all fields have been filled
if ($location == '' or $meal == '' or $rating == '') {
    echo "All forms must be filled in to write a review. Please try again.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
  }
  else {
    $sql = "INSERT INTO reviews (username, location, meal, rating) VALUES (?,?,?,?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $userid, $location, $meal, $rating);
    mysqli_stmt_execute($stmt);
    echo "Review has been posted.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
  }
  ?>