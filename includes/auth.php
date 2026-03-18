<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $name             = trim($_POST['name']);
        $email            = trim($_POST['email']);
        $phone            = trim($_POST['phone']);
        $address          = trim($_POST['address']);
        $password         = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

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

        $stmt = $conn->prepare("SELECT id FROM login_data WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered!']);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO login_data (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $phone, $address, $hashed_password])) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully! Please login.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        }

    } elseif ($action === 'login') {
        $email    = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required!']);
            exit;
        }

        $stmt = $conn->prepare("SELECT id, name, email, phone, address, password FROM login_data WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['user_name']    = $user['name'];
            $_SESSION['user_email']   = $user['email'];
            $_SESSION['user_phone']   = $user['phone'];
            $_SESSION['user_address'] = $user['address'];
            $_SESSION['logged_in']    = true;
            echo json_encode(['success' => true, 'message' => 'Login successful! Welcome back, ' . $user['name']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password!']);
        }

    } elseif ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully!']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
}
?>