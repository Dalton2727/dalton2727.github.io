<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';

//creates lists from the query for menu items and locations
$locations = [];
$meals = [];

//locations
$query = "SELECT DISTINCT location FROM Menu";
$result = mysqli_query($db, $query); 
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row['location'];
}

//menu
$query = "SELECT DISTINCT item FROM Menu";
$result = mysqli_query($db, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $meals[] = $row['item'];
}
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
                    <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write review</a>'?> </li>
                    <li style="color: white;">User <?php echo $userid?></li>
                    <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                    <!--
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
                    -->
                    <li><?php if ($_SESSION['loggedin']){ echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Start</a>';} else {echo '<a href="index.php">Start</a>';}?></li>
                </ul>
              </div>
    <div id="form">
      <h1> Write your review for a specific meal offered at a Wesleyan dining location</h1>
      <form name="form" action="uploadreview.php" method="POST">
      <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
        <p>
          <label> Location: </label>
          <select id="location" name="location">
            <option value="">Select a location</option>
            <?php foreach ($locations as $location): ?>
              <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                    <?php endforeach; ?>
          </select>
        </p>

        <p>
          <label> Meal: </label>
          <select id="meal" name="meal">
            <option value="">Select a meal</option>
            <?php foreach ($meals as $meal): ?>
              <option value="<?php echo htmlspecialchars($meal); ?>"><?php echo htmlspecialchars($meal); ?></option>
                    <?php endforeach; ?>
          </select>
        </p>

        <p>
          <label> Rating (1-10:) </label>
          <input type="number" name="rating" min ="1" max="10" />
        </p>

        <p>
          <input type="submit" value="Post" />
        </p>
</form>
</div>
  </body>
</html>