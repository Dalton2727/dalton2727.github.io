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
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="Swings points balance" content="" />
        <meta name="description" content="Wesleyan University Point budgeter"/>
        <link rel="stylesheet" href="CSScode.css" />
    </head>
<body id = "demo_font" style="background-color: #f4f4f4;">
            <div id="navbar" class="light">
                <ul>
                  <li>User: <?php echo $userid?></li>
                  <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                  <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit</a>'?> </li>
                  <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write</a>'?> </li>
                  <li> <?php echo '<a href="demo.php?userid=' . urlencode($userid) . '">Start</a>'?> </li>
                  <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                  <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
              <div class = "demo-container">
              <h1>Edit Review</h1>
                <form name="form" action="editrev.php" method="POST">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p style="margin: 20px 0;">
                    <label> ID of review you want to edit: </label>
                    <input type="number" name="revid" />
                    </p>

                    <p style="margin: 20px 0;">
                    <label> Location (leave blank for no change): </label>
                    <select name="location">
                      <option value="">Select a location</option>
                      <?php foreach ($locations as $location): ?>
                        <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                        <?php endforeach; ?>
                    </select>
                    </p>

                    <p style="margin: 20px 0;">
                    <label> Meal (leave blank for no change): </label>
                    <select name="meal">
                      <option value="">Select a meal</option>
                      <?php foreach ($meals as $meal): ?>
                          <option value="<?php echo htmlspecialchars($meal); ?>"><?php echo htmlspecialchars($meal); ?></option>
                      <?php endforeach; ?>
                    </select>
                    </p>

                    <p style="margin: 20px 0;">
                    <label> Rating (leave blank for no change): </label>
                    <input type="number" name="rating" min="1" max="10" />
                    </p>

                    <p style="margin: 20px 0;">
                    <input type="submit" value="Finalize" />
                    </p>
            </form>
            <form name="form" action="delete.php" method="POST">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p style="margin: 20px 0;">
                    <label> If you want to delete a review, type its id here: </label>
                    <input type="number" name="revbar" min ="1" max="99999" />
                    </p>

                    <p style="margin: 20px 0;">
                    <input type="submit" value="Delete" />
                    </p>
        </form>
            
            
            </div>


</body>
</html>