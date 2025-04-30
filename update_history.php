<?php 
session_start();

include 'dbconnection.php';

// Debug session
error_log("Update_history.php - Session userid: " . (isset($_SESSION['userid']) ? $_SESSION['userid'] : 'not set'));
error_log("Update_history.php - Session data: " . print_r($_SESSION, true));

// Get username from session
$user_id = $_SESSION['userid'] ?? '';
if (empty($user_id)) {
    echo "Error: You must be logged in to make a purchase.";
    exit;
}

$name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
$price = isset($_POST['item_price']) ? $_POST['item_price'] : '';

// Debug: Print individual values
error_log("Final values - user_id: " . $user_id);
error_log("Final values - item_name: " . $name);
error_log("Final values - item_price: " . $price);

if (empty($name) || empty($price)) {
    echo "Error: Item name and price are required.";
    exit;
}

try {
    $sql = "INSERT INTO purchases (user_id, item_name, item_price) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($db));
    }
    
    // Bind parameters with correct types - using string for user_id
    mysqli_stmt_bind_param($stmt, "ssd", $user_id, $name, $price);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }
    
    echo "Purchase recorded successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

mysqli_stmt_close($stmt);
?>