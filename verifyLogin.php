<?php
include 'dbconnection.php';
$userid = $_POST['userid'];
$password = $_POST['password'];
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ss", $userid, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$num = mysqli_num_rows($result);

// "start.html" is the page that a successful login that will redirect the user to
if ($num > 0) {
  echo "Login successful" 
} else {
  echo "Wrong User id or password";
}
?>