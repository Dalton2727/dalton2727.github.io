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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    </head>
<body id="demo_font"  style="background-color:  #f4f4f4">
            <div id="navbar" class="light">
                <ul>
                  <li>User: <?php echo $userid?></li>
                  <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                  <li> <?php echo '<a href="ratings.php?userid=' . urlencode($userid) . '">Ratings</a>'?> </li>
                  <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit</a>'?> </li>
                  <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write</a>'?> </li>
                  <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                  <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
              <div style = "color: black">
              <?php 
            if ($showReviews == 'my') {
              $sql = "SELECT * FROM reviews WHERE username = ?";
            if ($stmt = $db->prepare($sql)) {
              $stmt->bind_param("s", $userid); 
              $stmt->execute();
              $result = $stmt->get_result();
              $num = $result->num_rows;
              while ($row = $result->fetch_assoc()) {
                echo '<div class="review">';
                echo '<div class="review-info">';
                echo "Review ID: " . $row['id'] . " &nbsp;&nbsp;&nbsp; User: " . htmlspecialchars($row['username']) . " &nbsp;&nbsp;&nbsp; Location: " . htmlspecialchars($row['location']) . " &nbsp;&nbsp;&nbsp; Meal: " . htmlspecialchars($row['meal']) . " &nbsp;&nbsp;&nbsp; Date Created: " . htmlspecialchars($row['created_at']);
                echo '</div>';
                echo '<div class="star-rating">';
                for ($i = 1; $i <= 10; $i++) {
                    if ($i <= $row['rating']) {
                        echo '<span class="star" style="color: #FFD700;">★</span>';
                    } else {
                        echo '<span class="star" style="color: #ddd;">☆</span>';
                    }
                }
                echo '</div>';
                if (!empty($row['review_text'])) {
                    echo '<div class="review-content">';
                    echo "Review: " . htmlspecialchars($row['review_text']);
                    echo '</div>';
                }
                echo '</div>';
            }
            $stmt->close();
          }
        } elseif ($showReviews == 'all') {
              $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
              $result = mysqli_query($db, $sql);
              $num = mysqli_num_rows($result);
              while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="review">';
                echo '<div class="review-info">';
                echo "Review ID: " . $row['id'] . " | User: " . htmlspecialchars($row['username']) . " | Location: " . htmlspecialchars($row['location']) . " | Meal: " . htmlspecialchars($row['meal']) . " | Date: " . htmlspecialchars($row['created_at']);
                echo '</div>';
                echo '<div class="star-rating">';
                for ($i = 1; $i <= 10; $i++) {
                    if ($i <= $row['rating']) {
                        echo '<span class="star" style="color: #FFD700;">★</span>';
                    } else {
                        echo '<span class="star" style="color: #ddd;">☆</span>';
                    }
                }
                echo '</div>';
                if (!empty($row['review_text'])) {
                    echo '<div class="review-content">';
                    echo "Review: " . htmlspecialchars($row['review_text']);
                    echo '</div>';
                }
                echo '</div>';
            }
        }
              ?></div>

</body>
<div style="text-align: center; margin-top: 20px;">
        <form action="" method="get">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
            <button type="submit" name="reviews" value="my">My Reviews</button>
            <button type="submit" name="reviews" value="all">All Reviews</button>
        </form>
    </div>
</html>
