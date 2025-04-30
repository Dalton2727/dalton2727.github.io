<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';

$query = "SELECT DISTINCT location FROM Menu";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$locations = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT DISTINCT item FROM Menu";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$meals = $result->fetch_all(MYSQLI_ASSOC);

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
<body id = "demo_font" style="background-color: #f4f4f4;">
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
              <div class="demo-container">
                <h1>Add Review</h1>
                <form name="form" action="uploadreview.php" method="POST">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
                    <p style="margin: 20px 0;">
                    <label>Location:</label>
                    <select name="location">
                      <option value="">Select a location</option>
                      <?php foreach ($locations as $location): ?>
                        <option value="<?php echo htmlspecialchars($location['location']); ?>"><?php echo htmlspecialchars($location['location']); ?></option>
                      <?php endforeach; ?>
                    </select>
                    </p>

                    <p style="margin: 20px 0;">
                    <label>Meal:</label>
                    <select name="meal">
                      <option value="">Select a meal</option>
                      <?php foreach ($meals as $meal): ?>
                        <option value="<?php echo htmlspecialchars($meal['item']); ?>"><?php echo htmlspecialchars($meal['item']); ?></option>
                      <?php endforeach; ?>
                    </select>
                    </p>

                    <p style="margin: 20px 0;">

                    <div class="star-rating">
                        <label>Rating:</label>
                        <span class="star" onclick="setRating(1)">☆</span>
                        <span class="star" onclick="setRating(2)">☆</span>
                        <span class="star" onclick="setRating(3)">☆</span>
                        <span class="star" onclick="setRating(4)">☆</span>
                        <span class="star" onclick="setRating(5)">☆</span>
                        <span class="star" onclick="setRating(6)">☆</span>
                        <span class="star" onclick="setRating(7)">☆</span>
                        <span class="star" onclick="setRating(8)">☆</span>
                        <span class="star" onclick="setRating(9)">☆</span>
                        <span class="star" onclick="setRating(10)">☆</span>
                        <input type="hidden" name="rating" id="ratingInput" value="">
                    </div>
                    <script>
                    function setRating(rating) {
                        document.getElementById('ratingInput').value = rating;
                        const stars = document.querySelectorAll('.star');
                        stars.forEach((star, index) => {
                            if (index < rating) {
                                star.textContent = '★';
                                star.style.color = '#FFD700';
                            } else {
                                star.textContent = '☆';
                                star.style.color = '#ddd';
                            }
                        });
                    }
                    </script>

                    <label>Rating (1-10):</label>
                    <input type="number" name="rating" min="1" max="10" />

                    </p>

                    <p style="margin: 20px 0;">
                    <label>Optional Written Review:</label><br>
                    <textarea name="review_text" rows="5" cols="50" placeholder="Write your review here (optional)"></textarea>
                    </p>

                    <p style="margin: 20px 0;">
                    <input type="submit" value="Post" />
                    </p>
                </form>
            </div>
</body>
</html>