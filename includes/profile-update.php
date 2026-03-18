<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        echo json_encode(['success' => false, 'message' => 'Please login to update profile']);
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if ($action === 'update_profile') {
        // Get form data
        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        // Validate required fields
        if (empty($name) || empty($phone) || empty($address)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
            exit;
        }
        
        // Validate phone number (basic validation)
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit phone number']);
            exit;
        }
        
        try {
            $stmt = $conn->prepare("UPDATE login_data SET name = ?, phone = ?, address = ? WHERE id = ?");
            if ($stmt->execute([$name, $phone, $address, $user_id])) {
                $_SESSION['user_name']    = $name;
                $_SESSION['user_phone']   = $phone;
                $_SESSION['user_address'] = $address;
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()]);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>