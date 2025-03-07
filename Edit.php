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
              </div>
              <div style = "color: white">
                <?php $sql = "SELECT * FROM reviews WHERE username = '$userid'";
              $result = mysqli_query($db, $sql);
              $num = mysqli_num_rows($result);
              $i = 1;
              while ($i <= $num){
                $row = mysqli_fetch_assoc($result);
                echo '<div class="review">';
                echo "Review ID: " . $row['id'] . " &nbsp;&nbsp;&nbsp; User: " . htmlspecialchars($row['username']) . " &nbsp;&nbsp;&nbsp; Location: " . htmlspecialchars($row['location']) . " &nbsp;&nbsp;&nbsp; Meal: " . htmlspecialchars($row['meal']) . " &nbsp;&nbsp;&nbsp; Rating: " . $row['rating'];
                echo '</div>';
                $i = $i +1;
            }
              ?>
                <h1> Type in the id of the review you want to edit and what you want to change for the location, meal, and/or rating</h1>
                <form name="form" action="editrev.php" method="POST">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p>
                    <label> id of review you want to edit: </label>
                    <input type="number" name="revid" />
                    </p>

                    <p>
                    <label> Location (leave blank for no change): </label>
                    <input type="text" name="location" />
                    </p>

                    <p>
                    <label> Meal (leave blank for no change): </label>
                    <input type="text" name="meal" />
                    </p>

                    <p>
                    <label> Rating (leave blank for no change): </label>
                    <input type="number" name="rating" />
                    </p>

                    <p>
                    <input type="submit" value="Finalize" />
                    </p>
            </form>
            <form name="form" action="delete.php" method="POST">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p>
                    <label> If you want to delete a review, type its id here: </label>
                    <input type="number" name="revid" />
                    </p>

                    <p>
                    <input type="submit" value="Delete" />
                    </p>
        </form>
            
            
            </div>


</body>
</html>