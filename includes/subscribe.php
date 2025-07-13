<?php
include 'db.php'; 

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address!'); window.history.back();</script>";
        exit;
    }

    // Check if already subscribed
    $check = mysqli_query($conn, "SELECT * FROM subscribers WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('You are already subscribed!'); window.history.back();</script>";
    } else {
        $sql = "INSERT INTO subscribers (email) VALUES ('$email')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('🎉 Subscribed to Stylique!'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Something went wrong!'); window.history.back();</script>";
        }
    }
} else {
    echo "<script>alert('No email received!'); window.history.back();</script>";
}
?>
