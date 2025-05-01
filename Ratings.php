<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : ''; 
$location = isset($_GET['location']) ? $_GET['location'] : '';
$meal = isset($_GET['meal']) ? $_GET['meal'] : '';
$showReviews = isset($_GET['reviews']) ? $_GET['reviews'] : 'all'; 
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
<body id="demo_font"  style="background-color:  #f4f4f4">
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
              <div style = "color: black">
              <form method="get" action="">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>">
                <p>
                    <label>Get ratings for meal:</label>
                    <select name="meal">
                    <option value="">Select a meal</option>
                    <?php foreach ($meals as $mealOption): ?>
                        <option value="<?php echo htmlspecialchars($mealOption); ?>" 
                            <?php if (isset($_GET['meal']) && $_GET['meal'] === $mealOption) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($mealOption); ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label>And location:</label>
                    <select name="location">
                    <option value="">Select a location</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo htmlspecialchars($loc); ?>"
                            <?php if (isset($_GET['location']) && $_GET['location'] === $loc) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($loc); ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </p>
                <input type="submit" value="Filter Reviews">
                </form>
                            
                <?php
                $avg_conditions = [];
                $avg_params = [];
                $avg_types = '';

                if (!empty($location)) {
                    $avg_conditions[] = 'location = ?';
                    $avg_params[] = $location;
                    $avg_types .= 's';
                }
                if (!empty($meal)) {
                    $avg_conditions[] = 'meal = ?';
                    $avg_params[] = $meal;
                    $avg_types .= 's';
                }

                $avg_sql = "SELECT COUNT(*) AS count, AVG(rating) AS avg_rating FROM reviews";
                if (!empty($avg_conditions)) {
                    $avg_sql .= ' WHERE ' . implode(' AND ', $avg_conditions);
                }

                $avg_stmt = $db->prepare($avg_sql);
                if ($avg_types !== '') {
                    $avg_stmt->bind_param($avg_types, ...$avg_params);
                }
                $avg_stmt->execute();
                $avg_result = $avg_stmt->get_result();

                if ($avg_row = $avg_result->fetch_assoc()) {
                    $count = $avg_row['count'];
                    $avg_rating = $avg_row['avg_rating'];

                    if ($count > 0 && $avg_rating !== null) {
                        $rounded = round($avg_rating, 2);
                        echo "<p><strong>Average Rating: $rounded / 10</strong> based on $count review(s).</p>";
                    } else {
                        echo "<p><strong>No ratings found for the selected filter.</strong></p>";
                    }
                }

                $avg_stmt->close();
                ?>
              </div>
              <div>
                <?php
                $top_query = "
                    SELECT r.meal AS item, ROUND(AVG(r.rating), 2) AS avg_rating, COUNT(r.rating) AS total_reviews
                    FROM reviews r
                    GROUP BY r.meal
                    HAVING total_reviews > 0
                    ORDER BY avg_rating DESC
                    LIMIT 5;
                ";

                $top_result = mysqli_query($db, $top_query);

                if ($top_result && mysqli_num_rows($top_result) > 0) {
                    echo "<h3>Top Rated Meals</h3>";
                    while ($row = mysqli_fetch_assoc($top_result)) {
                        echo '<div class="review">';
                        echo "<strong>" . htmlspecialchars($row['item']) . "</strong>: " . 
                            $row['avg_rating'] . "/10 (" . $row['total_reviews'] . " reviews)";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No top rated meals found.</p>";
                }
                ?>
                
                </div>

<div style="text-align: center; margin-top: 20px;">
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <form action="personal_stats.php" method="get" style="display: inline;">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
            <button type="submit">My Stats</button>
        </form>
        <form action="Ratings.php" method="get" style="display: inline;">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
            <button type="submit"">Public Stats</button>
        </form>
    </div>
</body>
</html>
