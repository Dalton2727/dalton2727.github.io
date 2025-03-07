<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';

$locations = [];
$meals = [];

$query = "SELECT DISTINCT location FROM Menu";
$result = mysqli_query($db, $query); 
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row['location'];
}

$query = "SELECT DISTINCT item FROM Menu";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $meals[] = $row['item'];
}
?>
//allows user to edit rating, some parts don't have to be changed
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
                  <li style="color: white;">User: <?php echo $userid?></li>
                  <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                  <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit</a>'?> </li>
                  <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write</a>'?> </li>
                  <li> <?php echo '<a href="demo.php?userid=' . urlencode($userid) . '">Start</a>'?> </li>
                  <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                  <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
              <div>
                <h1> Type in the id of the review you want to edit and what you want to change for the location, meal, and/or rating</h1>
                <form name="form" action="editrev.php" method="POST">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p>
                    <label> ID of review you want to edit: </label>
                    <input type="number" name="revid" />
                    </p>

                    <p>
                    <label> Location (leave blank for no change): </label>
                    <select name="location">
                      <option value="">Select a location</option>
                      <?php foreach ($locations as $location): ?>
                        <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                        <?php endforeach; ?>
                    </select>
                    </p>

                    <p>
                    <label> Meal (leave blank for no change): </label>
                    <select name="meal">
                      <option value="">Select a meal</option>
                      <?php foreach ($meals as $meal): ?>
                          <option value="<?php echo htmlspecialchars($meal); ?>"><?php echo htmlspecialchars($meal); ?></option>
                      <?php endforeach; ?>
                    </select>
                    </p>

                    <p>
                    <label> Rating (leave blank for no change): </label>
                    <input type="number" name="rating" min="1" max="10" />
                    </p>

                    <p>
                    <input type="submit" value="Finalize" />
                    </p>
            </form>
            <form name="form" action="delete.php" method="POST">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p>
                      <!-- option to delete reviews, checks if review is created by the current user-->
                    <label> If you want to delete a review, type its id here: </label>
                    <input type="number" name="revid" min ="1" max="10" />
                    </p>

                    <p>
                    <input type="submit" value="Delete" />
                    </p>
        </form>
            
            
            </div>


</body>
</html>
