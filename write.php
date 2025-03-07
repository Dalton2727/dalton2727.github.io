<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Review writing form</title>
    <link rel="stylesheet" type="text/css" href="CSScode.css" />
  </head>

  <body>
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