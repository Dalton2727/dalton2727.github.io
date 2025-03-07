<?php 
session_start();
include 'dbconnection.php';
session_destroy();

echo "You have been logged out <br>";
echo '<a href="index.php">return to login page</a>'; 
?>