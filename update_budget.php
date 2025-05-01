<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Debug session
error_log("Update_budget.php - Session status: " . session_status());
error_log("Update_budget.php - Session ID: " . session_id());
error_log("Update_budget.php - Session data: " . print_r($_SESSION, true));
error_log("Update_budget.php - POST data: " . print_r($_POST, true));
error_log("Update_budget.php - Raw input: " . file_get_contents('php://input'));

include 'dbconnection.php';

// Check if database connection is successful
if (!$db) {
    error_log("Update_budget.php - Database connection failed: " . mysqli_connect_error());
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    error_log("Update_budget.php - User not logged in");
    echo json_encode(['error' => 'You must be logged in to update budget.']);
    exit();
}

$user_id = $_SESSION['userid'];
$new_budget = isset($_POST['budget']) ? floatval($_POST['budget']) : null;
$new_spent = isset($_POST['spent']) ? floatval($_POST['spent']) : null;

error_log("Update_budget.php - User ID: " . $user_id);
error_log("Update_budget.php - New budget: " . $new_budget);
error_log("Update_budget.php - New spent: " . $new_spent);

try {
    // Start transaction
    mysqli_begin_transaction($db);
    
    // First, get current total spent
    $current_spent_sql = "SELECT COALESCE(SUM(item_price), 0) as total_spent FROM purchases WHERE user_id = ?";
    $current_spent_stmt = mysqli_prepare($db, $current_spent_sql);
    if (!$current_spent_stmt) {
        throw new Exception("Prepare failed for current spent: " . mysqli_error($db));
    }
    
    if (!mysqli_stmt_bind_param($current_spent_stmt, "s", $user_id)) {
        throw new Exception("Bind failed for current spent: " . mysqli_stmt_error($current_spent_stmt));
    }
    
    if (!mysqli_stmt_execute($current_spent_stmt)) {
        throw new Exception("Execute failed for current spent: " . mysqli_stmt_error($current_spent_stmt));
    }
    
    $result = mysqli_stmt_get_result($current_spent_stmt);
    $row = mysqli_fetch_assoc($result);
    $current_spent = $row['total_spent'];
    mysqli_stmt_close($current_spent_stmt);
    
    error_log("Update_budget.php - Current total spent: " . $current_spent);
    
    // Calculate the difference between budget and spent
    $budget_difference = $new_budget - $current_spent;
    error_log("Update_budget.php - Budget difference: " . $budget_difference);

    // Update budget if provided
    if ($new_budget !== null) {
        $budget_sql = "UPDATE users SET budget = ?, spent = ?, remainder = ? WHERE username = ?";
        $budget_stmt = mysqli_prepare($db, $budget_sql);
        if (!$budget_stmt) {
            throw new Exception("Prepare failed for budget update: " . mysqli_error($db));
        }
        
        // Calculate new values
        $current_spent_sql = "SELECT COALESCE(SUM(item_price), 0) as total_spent FROM purchases WHERE user_id = ?";
        $current_spent_stmt = mysqli_prepare($db, $current_spent_sql);
        if (!$current_spent_stmt) {
            throw new Exception("Prepare failed for current spent: " . mysqli_error($db));
        }
        
        if (!mysqli_stmt_bind_param($current_spent_stmt, "s", $user_id)) {
            throw new Exception("Bind failed for current spent: " . mysqli_stmt_error($current_spent_stmt));
        }
        
        if (!mysqli_stmt_execute($current_spent_stmt)) {
            throw new Exception("Execute failed for current spent: " . mysqli_stmt_error($current_spent_stmt));
        }
        
        $result = mysqli_stmt_get_result($current_spent_stmt);
        $row = mysqli_fetch_assoc($result);
        $current_spent = $row['total_spent'];
        mysqli_stmt_close($current_spent_stmt);
        
        $remainder = $new_budget - $current_spent;
        
        if (!mysqli_stmt_bind_param($budget_stmt, "ddds", $new_budget, $current_spent, $remainder, $user_id)) {
            throw new Exception("Bind failed for budget update: " . mysqli_stmt_error($budget_stmt));
        }
        
        if (!mysqli_stmt_execute($budget_stmt)) {
            throw new Exception("Execute failed for budget update: " . mysqli_stmt_error($budget_stmt));
        }
        
        mysqli_stmt_close($budget_stmt);
        
        // Update session budget
        $_SESSION['budget'] = $new_budget;
        error_log("Update_budget.php - Updated budget in users table and session");
    }
    
    // If updating spent amount, we need to adjust the purchases table
    if ($new_spent !== null) {
        // Calculate the difference
        $difference = $new_spent - $current_spent;
        error_log("Update_budget.php - Spent difference: " . $difference);
        
        // If there's a difference, add a manual adjustment purchase
        if ($difference != 0) {
            $adjustment_sql = "INSERT INTO purchases (user_id, item_name, item_price, created_at) VALUES (?, 'Manual Adjustment', ?, NOW())";
            $adjustment_stmt = mysqli_prepare($db, $adjustment_sql);
            if (!$adjustment_stmt) {
                throw new Exception("Prepare failed for adjustment: " . mysqli_error($db));
            }
            
            if (!mysqli_stmt_bind_param($adjustment_stmt, "sd", $user_id, $difference)) {
                throw new Exception("Bind failed for adjustment: " . mysqli_stmt_error($adjustment_stmt));
            }
            
            if (!mysqli_stmt_execute($adjustment_stmt)) {
                throw new Exception("Execute failed for adjustment: " . mysqli_stmt_error($adjustment_stmt));
            }
            
            mysqli_stmt_close($adjustment_stmt);
            error_log("Update_budget.php - Added manual adjustment purchase");
        }
        
        // Update users table with new spent amount and remainder
        $update_spent_sql = "UPDATE users SET spent = ?, remainder = budget - ? WHERE username = ?";
        $update_spent_stmt = mysqli_prepare($db, $update_spent_sql);
        if (!$update_spent_stmt) {
            throw new Exception("Prepare failed for spent update: " . mysqli_error($db));
        }
        
        if (!mysqli_stmt_bind_param($update_spent_stmt, "dds", $new_spent, $new_spent, $user_id)) {
            throw new Exception("Bind failed for spent update: " . mysqli_stmt_error($update_spent_stmt));
        }
        
        if (!mysqli_stmt_execute($update_spent_stmt)) {
            throw new Exception("Execute failed for spent update: " . mysqli_stmt_error($update_spent_stmt));
        }
        
        mysqli_stmt_close($update_spent_stmt);
        
        // Update session spent amount
        $_SESSION['spent'] = $new_spent;
    }
    
    // Get updated values
    $budget = $new_budget !== null ? $new_budget : $_SESSION['budget'];
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
    $total_spent = $row['total_spent'];
    mysqli_stmt_close($spent_stmt);
    
    $remaining = $budget - $total_spent;
    
    // Update session remaining amount
    $_SESSION['remaining'] = $remaining;
    
    // Commit transaction
    mysqli_commit($db);
    error_log("Update_budget.php - Transaction committed successfully");
    
    // Return success with updated values
    echo json_encode([
        'success' => true,
        'budget' => $budget,
        'total_spent' => $total_spent,
        'remaining' => $remaining
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($db) && mysqli_rollback($db)) {
        error_log("Update_budget.php - Transaction rolled back");
    }
    
    error_log("Error in update_budget.php: " . $e->getMessage());
    error_log("MySQL Error: " . mysqli_error($db));
    echo json_encode(['error' => 'An error occurred while updating the budget. Please try again.']);
}

// Close database connection
if (isset($db)) {
    mysqli_close($db);
}
?> 