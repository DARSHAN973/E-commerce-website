<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        // Registration logic
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required!']);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format!']);
            exit;
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters!']);
            exit;
        }
        
        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match!']);
            exit;
        }
        
        // Check if email already exists
        $check_email = "SELECT id FROM login_data WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_email);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already registered!']);
            exit;
        }
        
        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_user = "INSERT INTO login_data (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_user);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $address, $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully! Please login.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        }
        
    } elseif ($action === 'login') {
        // Login logic
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required!']);
            exit;
        }
        
        // Check user credentials
        $login_query = "SELECT id, name, email, phone, address, password FROM login_data WHERE email = ?";
        $stmt = mysqli_prepare($conn, $login_query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_address'] = $user['address'];
                $_SESSION['logged_in'] = true;
                
                echo json_encode(['success' => true, 'message' => 'Login successful! Welcome back, ' . $user['name']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password!']);
        }
        
    } elseif ($action === 'logout') {
        // Logout logic
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully!']);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
}
?>