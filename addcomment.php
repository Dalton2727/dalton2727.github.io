<?php 
session_start();
include 'dbconnection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$revid = isset($_POST['revid']) ? intval($_POST['revid']) : 0;
$comment = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';

if ($revid <= 0 || $comment == '') {
    echo "Please input a valid review ID and comment.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
    exit;
}

// Step 1: Fetch the existing comment_text for the review
$sql = "SELECT comment_text FROM reviews WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $revid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) < 1) {
    echo "No review found with that ID.<br>";
    echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
    exit;
}

$row = mysqli_fetch_assoc($result);
$existing_comment = $row['comment_text'] ?? '';  // Use empty string if null

// Step 2: Prepare new comment with timestamp
$appended_comment = $existing_comment . "\n[" . $userid . "]: " . $comment;

// Step 3: Update the comment_text field in the review
$update_sql = "UPDATE reviews SET comment_text = ? WHERE id = ?";
$update_stmt = mysqli_prepare($db, $update_sql);
mysqli_stmt_bind_param($update_stmt, "si", $appended_comment, $revid);
mysqli_stmt_execute($update_stmt);

echo "Comment has been added.<br>";
echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Return to reviews</a>';
?>
