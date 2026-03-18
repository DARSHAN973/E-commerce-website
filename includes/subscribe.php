<?php
include 'db.php'; 

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address!'); window.history.back();</script>";
        exit;
    }

    // Check if already subscribed
    $stmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "<script>alert('You are already subscribed!'); window.history.back();</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
        if ($stmt->execute([$email])) {
            echo "<script>alert('🎉 Subscribed to Stylique!'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Something went wrong!'); window.history.back();</script>";
        }
    }
} else {
    echo "<script>alert('No email received!'); window.history.back();</script>";
}
?>
