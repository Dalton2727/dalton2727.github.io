//shows all reviews posted
<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : ''; 
$showReviews = isset($_GET['reviews']) ? $_GET['reviews'] : 'all'; //to switch between "my" and "all" reviews - "all" default
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
                  <li style="color: white;">User: <?php echo $userid?></li>
                  <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                  <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit</a>'?> </li>
                  <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write</a>'?> </li>
                  <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                  <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
              <div style = "color: white">
              <?php //only shows the users reviews if they click the my reviews button
            if ($showReviews == 'my') {
              $sql = "SELECT * FROM reviews WHERE username = ?";
            if ($stmt = $db->prepare($sql)) {
              $stmt->bind_param("s", $userid); 
              $stmt->execute();
              $result = $stmt->get_result();
              $num = $result->num_rows;
              while ($row = $result->fetch_assoc()) {
                echo '<div class="review">';
                echo "Review ID: " . $row['id'] . " &nbsp;&nbsp;&nbsp; User: " . htmlspecialchars($row['username']) . " &nbsp;&nbsp;&nbsp; Location: " . htmlspecialchars($row['location']) . " &nbsp;&nbsp;&nbsp; Meal: " . htmlspecialchars($row['meal']) . " &nbsp;&nbsp;&nbsp; Rating: " . $row['rating'];
                echo '</div>';
            }
            $stmt->close();
          }
          
        } elseif ($showReviews == 'all') {
              $sql = "SELECT * FROM reviews";
              $result = mysqli_query($db, $sql);
              $num = mysqli_num_rows($result);
              $i=1;
              while ($i <= $num){
                $row = mysqli_fetch_assoc($result);
                echo '<div class="review">';
                echo "Review ID: " . $row['id'] . " &nbsp;&nbsp;&nbsp; User: " . htmlspecialchars($row['username']) . " &nbsp;&nbsp;&nbsp; Location: " . htmlspecialchars($row['location']) . " &nbsp;&nbsp;&nbsp; Meal: " . htmlspecialchars($row['meal']) . " &nbsp;&nbsp;&nbsp; Rating: " . $row['rating'] . "<br><br>";
                echo '</div>';
                $i = $i+1;
            }
        }
              ?></div>
<!-- option for my reviews or all reviews option to make it easier for user to filter to their reviews -->
</body>
<div style="text-align: center; margin-top: 20px;">
        <form action="" method="get">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
            <button type="submit" name="reviews" value="my">My Reviews</button>
            <button type="submit" name="reviews" value="all">All Reviews</button>
        </form>
    </div>
</html>