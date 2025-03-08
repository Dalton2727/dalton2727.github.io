<?php
session_start();
include 'dbconnection.php';
$userid = $_POST['userid'];
$password = $_POST['password'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
//verifys login, tells user if the user id and password don't match
//redirects them to start id they can log in, if not, the site tells them that the user id and password don't match (can try again)
if ($row){
  $hashed_password = $row['password'];
  if (password_verify($password,$hashed_password)) {
    $_SESSION["loggedin"] = true;
    header("Location: start2.php?userid=" . urlencode($userid));
    exit();
} else {
  echo "Wrong User id or password";
} 
}
else {
  echo "Wrong User id or password";
}



// "start.html" is the page that a successful login that will redirect the user to

?>