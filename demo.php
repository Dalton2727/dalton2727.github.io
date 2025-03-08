<?php
session_start();
include 'dbconnection.php';

//calls to the user id and budget for the budget tracker
$userid = $_SESSION['userid'];

if (!isset($_SESSION['budget'])) {
    $_SESSION['budget'] = 500; 
}
if (!isset($_SESSION['spent'])) {
    $_SESSION['spent'] = 0; 
}
if (!isset($_SESSION['remaining'])) {
    $_SESSION['remaining'] = $_SESSION['budget'] - $_SESSION['spent'];
}

//calls on the menu SQL table in order to extract prices, location, ctegory and item to be able to reference the unique item purhcased
$query = "SELECT DISTINCT location FROM Menu";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$locations = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT DISTINCT category FROM Menu";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);

$query = "SELECT item, price FROM Menu";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$menu_items = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['budget'])) {
        $_SESSION['budget'] = $_POST['budget']; 
        $_SESSION['remaining'] = $_SESSION['budget'] - $_SESSION['spent']; 
    }
    if (isset($_POST['spent'])) {
        $_SESSION['spent'] = $_POST['spent'];
        $_SESSION['remaining'] = $_SESSION['budget'] - $_SESSION['spent']; 
    }
    if (isset($_POST['menu_item'])) {
        $selected_item = $_POST['menu_item'];
        foreach ($menu_items as $item) {
            if ($item['item'] === $selected_item) {
                $_SESSION['remaining'] -= $item['price'];
                break;
            }
        }
    }
}

$budget = $_SESSION['budget'];
$spent = $_SESSION['spent'];
$remaining = $_SESSION['remaining'];
$percentageSpent = ($spent / $budget) * 100;
$percentageSpent = min($percentageSpent, 100); 
?>
<!-- creates a progress bar tracker and ability to insert budget for functionality of demo-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div id="navbar">
        <ul>
        <li style="color: white;">User: <?php echo $userid?></li>
                    <li> <?php echo '<a href="demo.php?userid=' . urlencode($userid) . '">Start</a>'?> </li>
                    <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
                    <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                    <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
        </ul>
      </div>
    <title>Budget Progress with Menu Purchases</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            width: 60%;
            margin: auto;
        }
        

        .progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 20px;
        }
        .progress-bar {
            height: 30px;
            width: <?php echo $percentageSpent; ?>%;
            background-color: #76c7c0;
            text-align: center;
            color: white;
            line-height: 30px;
            font-weight: bold;
            border-radius: 20px;
        }

        .budget-info {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Track Your Budget Progress</h1>

    <div class="progress-container">
        <div class="progress-bar">
            <?php echo round($percentageSpent, 2); ?>% Spent
        </div>
    </div>

    <div class="budget-info">
        <p>Budget: $<?php echo $budget; ?></p>
        <p>Spent: $<?php echo $spent; ?></p>
        <p>Remaining: $<?php echo $remaining; ?></p>
    </div>

    <form action="" method="POST">
        <label for="budget">Set your monthly budget: </label>
        <input type="number" id="budget" name="budget" value="<?php echo $budget; ?>" min="0">
        <br><br>
        <label for="spent">Amount spent: </label>
        <input type="number" id="spent" name="spent" value="<?php echo $spent; ?>" min="0">
        <br><br>

        <label for="menu_item">Purchase an item: </label>
        <select id="menu_item" name="menu_item">
        <option value="" disabled selected>Select an item</option> 
            <?php foreach ($menu_items as $item): ?>
        <option value="<?php echo htmlspecialchars($item['item']); ?>">
            <?php echo htmlspecialchars($item['item']) . " - $" . number_format($item['price'], 2); ?>
        </option>
    <?php endforeach; ?>
</select>
<br><br>

        <input type="submit" value="Update">
    </form>
</div>

</body>
</html>
