<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="Swings points balance" content="" />
        <meta name="description" content="Wesleyan University Point budgeter"/>
        <link rel="stylesheet" href="CSScode.css" />
    </head>
<body id = "font" style="background-color:rgb(32, 31, 31);">
            <div id="navbar">
                <ul>
                    <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write a review</a>'?> </li>
                    <li style="color: white;">User: <?php echo $userid?></li>
                    <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
                    <li><?php if ($_SESSION['loggedin']){ echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Start</a>';} else {echo '<a href="index.php">Start</a>';}?></li>
                </ul>
              </div>
              <div style = "color: white"><?php $sql = "SELECT * FROM reviews";
              $result = mysqli_query($db, $sql);
              $num = mysqli_num_rows($result);
              $i = 1;
              while ($i <= $num){
                $row = mysqli_fetch_assoc($result);
                echo "Review number: " . $row['id'] . " User: " . htmlspecialchars($row['username']) . " Location: " . htmlspecialchars($row['location']) . " Meal: " . htmlspecialchars($row['meal']) . " Rating: " . $row['rating'] . "<br><br>";
                $i = $i +1;
            }
              ?></div>

</body>


</html>