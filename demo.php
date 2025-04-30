<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if dbconnection.php exists and include it
if (!file_exists('dbconnection.php')) {
    die("Error: dbconnection.php file not found");
}

include 'dbconnection.php';

// Check if database connection is successful
if (!$db) {
    die("Error: Could not connect to database. " . mysqli_connect_error());
}

// Debug session
error_log("Demo.php - Session userid: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : 'not set'));
error_log("Demo.php - Session data: " . print_r($_SESSION, true));

// Ensure userid is set in session
if (!isset($_SESSION['userid']) && isset($_GET['userid'])) {
    $_SESSION['userid'] = $_GET['userid'];
    error_log("Demo.php - Set userid from GET: " . $_GET['userid']);
}

if (!isset($_SESSION['userid'])) {
    die("Error: User not logged in");
}

$userid = $_SESSION['userid'];

// Initialize session variables if not set
if (!isset($_SESSION['budget'])) {
    $_SESSION['budget'] = 500.00; 
}
if (!isset($_SESSION['spent'])) {
    $_SESSION['spent'] = 0.00; 
}
if (!isset($_SESSION['remaining'])) {
    $_SESSION['remaining'] = $_SESSION['budget'] - $_SESSION['spent'];
}

// Get menu items from database
$query = "SELECT item, price FROM Menu";
$stmt = mysqli_prepare($db, $query);
if (!$stmt) {
    error_log("Error preparing menu query: " . mysqli_error($db));
    die("Error preparing menu query: " . mysqli_error($db));
}

if (!mysqli_stmt_execute($stmt)) {
    error_log("Error executing menu query: " . mysqli_stmt_error($stmt));
    die("Error executing menu query: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    error_log("Error getting result: " . mysqli_error($db));
    die("Error getting result: " . mysqli_error($db));
}

$menu_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$budget = $_SESSION['budget'];
$spent = $_SESSION['spent'];
$remaining = $_SESSION['remaining'];
$percentageSpent = ($budget > 0) ? ($spent / $budget) * 100 : 0;
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
            <li style= "color: black" >User: <?php echo htmlspecialchars($userid); ?></li>
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
            <p>Budget: $<?php echo number_format($budget, 2); ?></p>
            <p>Spent: $<?php echo number_format($spent, 2); ?></p>
            <p>Remaining: $<?php echo number_format($remaining, 2); ?></p>
        </div>

        <form action="update_history.php" method="POST" class="demo-form">
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
                const remaining = budget - totalSpent; // Calculate remaining based on budget and total spent
                
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
                
                // Update form inputs with current values
                const budgetInput = document.getElementById('budget');
                const spentInput = document.getElementById('spent');
                if (budgetInput) {
                    budgetInput.value = budget.toFixed(2);
                    // Store the current value as the default value for future comparisons
                    budgetInput.defaultValue = budget.toFixed(2);
                }
                if (spentInput) {
                    spentInput.value = totalSpent.toFixed(2);
                    // Store the current value as the default value for future comparisons
                    spentInput.defaultValue = totalSpent.toFixed(2);
                }
            } catch (error) {
                console.error('Error in updatePageValues:', error);
                throw error;
            }
        }

        // Function to check for updates
        function checkForUpdates() {
            fetch('get_budget_info.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updatePageValues(data);
                    } else {
                        console.error('Error in response:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error checking for updates:', error);
                });
        }

        // Load initial values when page loads
        document.addEventListener('DOMContentLoaded', function() {
            checkForUpdates();
        });

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
            const newBudget = document.getElementById('budget').value;
            const newSpent = document.getElementById('spent').value;
            
            // Create FormData object
            const formData = new FormData(this);
            
            // If no item is selected but budget or spent is changed, update those values
            if (!selectedOption.value && (newBudget !== this.querySelector('[name="budget"]').defaultValue || 
                                        newSpent !== this.querySelector('[name="spent"]').defaultValue)) {
                console.log('Updating budget/spent values:', { newBudget, newSpent });
                
                // Send AJAX request to update budget
                fetch('update_budget.php', {
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
                    console.log('Budget update response:', data);
                    
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    if (data.success) {
                        // Update the display with new values
                        updatePageValues(data);
                        // Reset form
                        this.reset();
                        // Show success message
                        alert('Budget updated successfully!');
                    }
                })
                .catch(error => {
                    console.error('Error updating budget:', error);
                    alert('An error occurred while updating the budget. Please try again.');
                });
                return;
            }
            
            // If an item is selected, proceed with purchase
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

            // Send AJAX request for purchase
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
                console.log('Purchase response:', data);
                
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
