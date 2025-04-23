<?php
session_start();
include 'dbconnection.php';


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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Progress with Menu Purchases</title>
    <link rel="stylesheet" href="CSScode.css" />
</head>
<body id="demo_body">
    <div id="navbar" class="light">
        <ul>
            <li style= "color: black" >User: <?php echo $userid?></li>
            <li> <?php echo '<a href="demo.php?userid=' . urlencode($userid) . '">Start</a>'?> </li>
            <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
            <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
            <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
            <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
            <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
            <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
        </ul>
    </div>

    <div class="demo-container">
        <h1>Track Your Budget Progress</h1>

        <div class="progress-container">
            <div class="progress-bar" style="width: <?php echo $percentageSpent; ?>%">
                <?php echo round($percentageSpent, 2); ?>% Spent
            </div>
        </div>

        <div class="budget-info">
            <p>Budget: $<?php echo $budget; ?></p>
            <p>Spent: $<?php echo $spent; ?></p>
            <p>Remaining: $<?php echo $remaining; ?></p>
        </div>

        <form action="" method="POST" class="demo-form">
            <label for="budget">Set your monthly budget: </label>
            <input type="number" id="budget" name="budget" value="<?php echo $budget; ?>" min="0">
            
            <label for="spent">Amount spent: </label>
            <input type="number" id="spent" name="spent" value="<?php echo $spent; ?>" min="0">

            <label for="menu_item">Purchase an item: </label>
            <select id="menu_item" name="menu_item">
                <option value="" disabled selected>Select an item</option> 
                <?php foreach ($menu_items as $item): ?>
                    <option value="<?php echo htmlspecialchars($item['item']); ?>">
                        <?php echo htmlspecialchars($item['item']) . " - $" . number_format($item['price'], 2); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
