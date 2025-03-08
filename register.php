<?php
include 'dbconnection.php';
$userid = $_POST['userid'];
$password = $_POST['password'];
$password2 = $_POST['password2'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$num = mysqli_num_rows($result);

//makes sure all forms are filled in including entering password twice
if ($userid == '' or $password == '' or $password2 == '') {
  echo "All forms must be filled in to register a username and password. Please try again.<br>";
  echo "<a href=\"index.php\">Return to register page</a>";
} else {
  //if user has already been created with this user name
  if ($num > 0){
    echo "This username is taken, please select a different one <br>";
    echo "<a href=\"index.php\">Return to register page</a>";
  }
  //checks that passwords match
  elseif($password != $password2){
    echo "Both passwords do not match. Please try again.<br>";
    echo "<a href=\"index.php\">Return to register page</a>";
  }
  else{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $userid, $hashed_password);
    mysqli_stmt_execute($stmt);

    echo "Username and password registered. Return to register page to login.<br>";
    echo "<a href=\"index.php\">Return to register page</a>";
  }
}

mysqli_close($db);
?>