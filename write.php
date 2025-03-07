
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
<body id = "font" style="background-color:rgb(243, 70, 70);">
            <div id="navbar">
                <ul>
                    <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit review</a>'?> </li>
                    <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write a review</a>'?> </li>
                    <li style="color: white;">User: <?php echo $userid?></li>
                    <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
                    <li><?php if ($_SESSION['loggedin']){ echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Start</a>';} else {echo '<a href="index.php">Start</a>';}?></li>
                </ul>
              </div>
    <div id="form">
      <h1> Write your review for a specific meal offered at a Wesleyan dining location</h1>
      <form name="form" action="uploadreview.php" method="POST">
      <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
        <p>
          <label> Location: </label>
          <input type="text" id="location" name="location" />
        </p>

        <p>
          <label> Meal: </label>
          <input type="text" id="meal" name="meal" />
        </p>

        <p>
          <label> Rating (a number from 1-10:) </label>
          <input type="number" id="rating" name="rating" />
        </p>

        <p>
          <input type="submit" id="button" value="Post" />
        </p>
</form>
</div>
  </body>
</html>