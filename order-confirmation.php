<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

$activePage = 'order-confirmation';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Stylique</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assests/css/home-page.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .confirmation-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .order-id {
            background: linear-gradient(135deg, #c2185b, #e91e63);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <h2 class="mb-3">Order Confirmed!</h2>
                    <p class="lead mb-4">Thank you for your purchase. Your order has been successfully placed.</p>
                    
                    <div class="order-id">
                        Order ID: #<?php echo htmlspecialchars($order_id); ?>
                    </div>
                    
                    <p class="text-muted mb-4">
                        We've sent a confirmation email with your order details. 
                        You'll receive updates about your order status via email.
                    </p>
                    
                    <div class="row mt-5">
                        <div class="col-md-6 mb-3">
                            <a href="index.php" class="btn btn-dark w-100">
                                <i class="fas fa-home me-2"></i>Continue Shopping
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="profile.php" class="btn btn-outline-dark w-100">
                                <i class="fas fa-user me-2"></i>View My Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 