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
        $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
        $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
        
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
            // Update user profile
            $update_query = "UPDATE login_data SET name = ?, phone = ?, address = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $address, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Update session variables if needed
                $_SESSION['user_name'] = $name;
                $_SESSION['user_phone'] = $phone;
                $_SESSION['user_address'] = $address;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Profile updated successfully!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()]);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>