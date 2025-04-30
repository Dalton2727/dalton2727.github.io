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
                  <li> <?php echo '<a href="ratings.php?userid=' . urlencode($userid) . '">Ratings</a>'?> </li>
                  <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit</a>'?> </li>
                  <li> <?php echo '<a href="comment.php?userid=' . urlencode($userid) . '">Comment</a>'?> </li>
                  <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write</a>'?> </li>
                  <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                  <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
              <div class = "demo-container">
              <h1>Add Comment</h1>
                <form name="form" action="addcomment.php" method="POST">
                    <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    
                    <p style="margin: 20px 0;">
                        <label>ID of review you want to comment on:</label>
                        <input type="number" name="revid" required />
                    </p>

                    <p style="margin: 20px 0;">
                        <label>Comment:</label>
                        <textarea name="comment_text" rows="5" cols="50" placeholder="Write your comment here" required></textarea>
                    </p>

                    <p style="margin: 20px 0;"9>
                        <input type="submit" value="Submit Comment" />
                    </p>
                </form>

            
            
            </div>


</body>
</html>
