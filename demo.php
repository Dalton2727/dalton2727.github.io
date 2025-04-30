<?php
session_start();
include 'dbconnection.php';

// Debug session
error_log("Demo.php - Session userid: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : 'not set'));
error_log("Demo.php - Session data: " . print_r($_SESSION, true));

// Ensure userid is set in session
if (!isset($_SESSION['userid']) && isset($_GET['userid'])) {
    $_SESSION['userid'] = $_GET['userid'];
    error_log("Demo.php - Set userid from GET: " . $_GET['userid']);
}

$userid = $_SESSION['userid'];

if (!isset($_SESSION['budget'])) {
    $_SESSION['budget'] = 500.00; 
}
if (!isset($_SESSION['spent'])) {
    $_SESSION['spent'] = 0.00; 
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
                $_SESSION['spent'] += $item['price'];
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

        <form action="update_history.php" method="POST" class="demo-form">
            <?php 
            error_log("Session userid: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : 'not set'));
            ?>
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['userid']); ?>">
            <label for="budget">Set your monthly budget: </label>
            <input type="number" id="budget" name="budget" value="<?php echo number_format($budget, 2); ?>" min="0.00" step="0.01">
            
            <label for="spent">Amount spent: </label>
            <input type="number" id="spent" name="spent" value="<?php echo number_format($spent, 2); ?>" min="0.00" step="0.01">

            <label for="menu_item">Purchase an item: </label>
            <select id="menu_item" name="menu_item">
                <option value="" disabled selected>Select an item</option> 
                <?php foreach ($menu_items as $item): ?>
                    <option value="<?php echo htmlspecialchars($item['item']); ?>" data-price="<?php echo $item['price']; ?>">
                        <?php echo htmlspecialchars($item['item']) . " - $" . number_format($item['price'], 2); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="item_name" id="item_name">
            <input type="hidden" name="item_price" id="item_price">

            <input type="submit" value="Update">
        </form>
    </div>

    <script>
        // Function to update the page with new values
        function updatePageValues(data) {
            try {
                // Convert values to numbers to ensure proper calculations
                const budget = parseFloat(data.budget) || 500.00;
                const totalSpent = parseFloat(data.total_spent) || 0.00;
                const remaining = parseFloat(data.remaining) || budget;
                
                console.log('Updating page with values:', { budget, totalSpent, remaining });
                
                // Update budget info
                const budgetInfo = document.querySelector('.budget-info');
                if (budgetInfo) {
                    const paragraphs = budgetInfo.querySelectorAll('p');
                    if (paragraphs.length >= 3) {
                        paragraphs[0].textContent = 'Budget: $' + budget.toFixed(2);
                        paragraphs[1].textContent = 'Spent: $' + totalSpent.toFixed(2);
                        paragraphs[2].textContent = 'Remaining: $' + remaining.toFixed(2);
                    }
                }
                
                // Update progress bar
                const progressBar = document.querySelector('.progress-bar');
                if (progressBar) {
                    const percentageSpent = (totalSpent / budget) * 100;
                    progressBar.style.width = percentageSpent + '%';
                    progressBar.textContent = percentageSpent.toFixed(2) + '% Spent';
                }
                
                // Update session variables in the form
                const budgetInput = document.getElementById('budget');
                const spentInput = document.getElementById('spent');
                if (budgetInput) budgetInput.value = budget.toFixed(2);
                if (spentInput) spentInput.value = totalSpent.toFixed(2);
            } catch (error) {
                console.error('Error in updatePageValues:', error);
                throw error;
            }
        }

        // Function to check for updates
        function checkForUpdates() {
            fetch('get_budget_info.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updatePageValues(data);
                    }
                })
                .catch(error => {
                    console.error('Error checking for updates:', error);
                });
        }

        // Load initial values immediately when page loads
        document.addEventListener('DOMContentLoaded', function() {
            checkForUpdates();
        });

        // Check for updates every 5 seconds
        setInterval(checkForUpdates, 5000);

        document.getElementById('menu_item').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                document.getElementById('item_name').value = selectedOption.value;
                document.getElementById('item_price').value = selectedOption.dataset.price;
                console.log('Selected item:', selectedOption.value, 'Price:', selectedOption.dataset.price);
            }
        });

        // Handle form submission with AJAX
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission
            
            const menuItem = document.getElementById('menu_item');
            const selectedOption = menuItem.options[menuItem.selectedIndex];
            
            if (!selectedOption.value) {
                alert('Please select an item to purchase');
                return;
            }
            
            // Set the hidden fields
            const itemName = selectedOption.value;
            const itemPrice = selectedOption.dataset.price;
            
            document.getElementById('item_name').value = itemName;
            document.getElementById('item_price').value = itemPrice;
            
            console.log('Submitting purchase:', {
                item_name: itemName,
                item_price: itemPrice
            });

            // Create FormData object
            const formData = new FormData(this);

            // Send AJAX request
            fetch('update_history.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                if (!data.success) {
                    console.error('Unexpected response format:', data);
                    alert('An error occurred while processing your purchase. Please try again.');
                    return;
                }
                
                try {
                    // Update the page with new values
                    updatePageValues(data);
                    
                    // Reset form
                    this.reset();
                } catch (error) {
                    console.error('Error updating page:', error);
                    alert('An error occurred while updating the page. The purchase was successful, but the display may not be updated.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating your purchase. Please try again.');
            });
        });
    </script>
</body>
</html>
