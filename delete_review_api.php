<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

session_start();
include 'dbconnection.php';

$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$revid = isset($_POST['revid']) ? $_POST['revid'] : '';

if (empty($revid) || empty($userid)) {
    http_response_code(400);
    echo json_encode(['error' => 'Review ID and User ID are required']);
    exit;
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in to delete reviews']);
    exit;
}

if ($userid !== $_SESSION['userid']) {
    http_response_code(403);
    echo json_encode(['error' => 'You can only delete your own reviews']);
    exit;
}

try {
    $sql = "SELECT * FROM reviews WHERE username = ? AND id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("si", $userid, $revid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Review not found or you do not have permission to delete it']);
        exit;
    }

    $sql = "DELETE FROM reviews WHERE id = ? AND username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("is", $revid, $userid);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
    } else {
        throw new Exception('Failed to delete review');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 