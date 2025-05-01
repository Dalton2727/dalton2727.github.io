<?php 
session_start();

include 'dbconnection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session and POST data
error_log("Update_history.php - Session userid: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : 'not set'));
error_log("Update_history.php - Session data: " . print_r($_SESSION, true));
error_log("Update_history.php - POST data: " . print_r($_POST, true));

// Get username from session
$user_id = $_SESSION['userid'] ?? '';
if (empty($user_id)) {
    error_log("Error: User not logged in");
    echo json_encode(['success' => false, 'error' => 'You must be logged in to make a purchase.']);
    exit;
}

$name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
$price = isset($_POST['item_price']) ? floatval($_POST['item_price']) : 0;
$budget = isset($_SESSION['budget']) ? floatval($_SESSION['budget']) : 500.00;

// Debug: Print individual values
error_log("Final values - user_id: " . $user_id);
error_log("Final values - item_name: " . $name);
error_log("Final values - item_price: " . $price);
error_log("Final values - budget: " . $budget);

if (empty($name) || $price <= 0) {
    error_log("Error: Invalid item name or price");
    echo json_encode(['success' => false, 'error' => 'Item name and price are required.']);
    exit;
}

try {
    // Start transaction
    if (!mysqli_begin_transaction($db)) {
        throw new Exception("Could not start transaction: " . mysqli_error($db));
    }
    
    // Get current total spent from purchases table
    $spent_sql = "SELECT COALESCE(SUM(item_price), 0) as total_spent FROM purchases WHERE user_id = ?";
    $spent_stmt = mysqli_prepare($db, $spent_sql);
    if (!$spent_stmt) {
        throw new Exception("Prepare failed for total spent: " . mysqli_error($db));
    }
    
    if (!mysqli_stmt_bind_param($spent_stmt, "s", $user_id)) {
        throw new Exception("Bind failed for total spent: " . mysqli_stmt_error($spent_stmt));
    }
    
    if (!mysqli_stmt_execute($spent_stmt)) {
        throw new Exception("Execute failed for total spent: " . mysqli_stmt_error($spent_stmt));
    }
    
    $result = mysqli_stmt_get_result($spent_stmt);
    $row = mysqli_fetch_assoc($result);
    $current_spent = floatval($row['total_spent']);
    $current_remaining = $budget - $current_spent;
    
    error_log("Current budget: " . $budget);
    error_log("Current spent: " . $current_spent);
    error_log("Current remaining: " . $current_remaining);
    error_log("Purchase price: " . $price);
    
    // Check if user has enough remaining balance for this purchase
    if ($current_remaining < $price) {
        mysqli_rollback($db);
        echo json_encode(['success' => false, 'error' => 'Insufficient funds. You need $' . $price . ' but only have $' . $current_remaining . ' remaining.']);
        exit;
    }
    
    // Get purchase date and time
    $purchase_date = $_POST['purchase_date'] ?? date('Y-m-d');
    $purchase_time = $_POST['purchase_time'] ?? '00:00:00';
    
    // Format the datetime properly for MySQL
    $created_at = $purchase_date;
    if ($purchase_time) {
        // Ensure time is in HH:MM:SS format
        if (strlen($purchase_time) <= 5) { // If only HH:MM is provided
            $purchase_time .= ':00'; // Add seconds
        }
        $created_at .= ' ' . $purchase_time;
    } else {
        $created_at .= ' 00:00:00'; // Default to midnight if no time provided
    }

    // Validate the datetime format
    if (!strtotime($created_at)) {
        error_log("Invalid datetime format: " . $created_at);
        mysqli_rollback($db);
        echo json_encode(['success' => false, 'error' => 'Invalid date format']);
        exit;
    }

    error_log("Attempting to insert purchase with datetime: " . $created_at);

    // Insert the purchase into the database
    $insert_sql = "INSERT INTO purchases (user_id, item_name, item_price, created_at) VALUES (?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($db, $insert_sql);
    if (!$insert_stmt) {
        throw new Exception("Prepare failed for insert: " . mysqli_error($db));
    }
    
    if (!mysqli_stmt_bind_param($insert_stmt, "ssds", $user_id, $name, $price, $created_at)) {
        throw new Exception("Bind failed for insert: " . mysqli_stmt_error($insert_stmt));
    }
    
    if (!mysqli_stmt_execute($insert_stmt)) {
        throw new Exception("Execute failed for insert: " . mysqli_stmt_error($insert_stmt));
    }
    
    mysqli_stmt_close($insert_stmt);
    
    // Get new total spent after the purchase
    $new_spent_sql = "SELECT COALESCE(SUM(item_price), 0) as total_spent FROM purchases WHERE user_id = ?";
    $new_spent_stmt = mysqli_prepare($db, $new_spent_sql);
    if (!$new_spent_stmt) {
        throw new Exception("Prepare failed for new total spent: " . mysqli_error($db));
    }
    
    if (!mysqli_stmt_bind_param($new_spent_stmt, "s", $user_id)) {
        throw new Exception("Bind failed for new total spent: " . mysqli_stmt_error($new_spent_stmt));
    }
    
    if (!mysqli_stmt_execute($new_spent_stmt)) {
        throw new Exception("Execute failed for new total spent: " . mysqli_stmt_error($new_spent_stmt));
    }
    
    $result = mysqli_stmt_get_result($new_spent_stmt);
    $row = mysqli_fetch_assoc($result);
    $new_spent = floatval($row['total_spent']);
    $remaining = $budget - $new_spent;
    
    error_log("New total spent: " . $new_spent);
    error_log("New remaining: " . $remaining);
    
    mysqli_stmt_close($new_spent_stmt);
    
    // Update users table with remaining budget
    $update_sql = "UPDATE users SET budget = ? WHERE username = ?";
    error_log("Update SQL: " . $update_sql);
    error_log("Update values: budget=" . $remaining . ", username=" . $user_id);
    
    $update_stmt = mysqli_prepare($db, $update_sql);
    if (!$update_stmt) {
        throw new Exception("Prepare failed for update: " . mysqli_error($db));
    }
    
    if (!mysqli_stmt_bind_param($update_stmt, "ds", $remaining, $user_id)) {
        throw new Exception("Bind failed for update: " . mysqli_stmt_error($update_stmt));
    }
    
    if (!mysqli_stmt_execute($update_stmt)) {
        throw new Exception("Execute failed for update: " . mysqli_stmt_error($update_stmt));
    }
    
    mysqli_stmt_close($update_stmt);
    
    // Update session variables
    $_SESSION['spent'] = $new_spent;
    $_SESSION['remaining'] = $remaining;
    
    // Commit transaction
    if (!mysqli_commit($db)) {
        throw new Exception("Commit failed: " . mysqli_error($db));
    }
    
    // Return success with updated values
    echo json_encode([
        'success' => true,
        'budget' => $budget,
        'total_spent' => $new_spent,
        'remaining' => $remaining
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($db) && mysqli_rollback($db)) {
        error_log("Transaction rolled back");
    }
    
    error_log("Error in update_history.php: " . $e->getMessage());
    error_log("MySQL Error: " . mysqli_error($db));
    echo json_encode(['success' => false, 'error' => 'An error occurred while processing your purchase. Please try again.']);
}

// Close database connection
if (isset($db)) {
    mysqli_close($db);
}
?>