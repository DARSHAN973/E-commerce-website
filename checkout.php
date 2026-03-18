<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user details
$stmt = $conn->prepare("SELECT * FROM login_data WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Check if user exists
if (!$user) {
    // User not found, redirect to login
    session_destroy();
    header('Location: index.php?error=user_not_found');
    exit;
}

// Get cart items
$stmt = $conn->prepare("SELECT c.id as cart_id, c.quantity, p.id, p.name, p.price, p.discount, p.image, p.stock
               FROM cart c
               JOIN products p ON c.product_id = p.id
               WHERE c.user_id = ?
               ORDER BY c.added_at DESC");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
$cart_items_array = [];
foreach ($cart_items as $item) {
    $discounted_price = $item['price'] - $item['discount'];
    $item_total = $discounted_price * $item['quantity'];
    $subtotal += $item_total;
    $cart_items_array[] = $item;
}

$shipping = $subtotal >= 999 ? 0 : 50;
$total = $subtotal + $shipping;

$activePage = 'checkout';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Stylique</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assests/css/home-page.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .checkout-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-control:focus {
            border-color: #c2185b;
            box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.25);
        }
        .btn-checkout {
            background: linear-gradient(135deg, #c2185b, #e91e63);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(194, 24, 91, 0.3);
            color: white;
        }
        .order-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method:hover {
            border-color: #c2185b;
            background: #f8f9fa;
        }
        .payment-method.selected {
            border-color: #c2185b;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Information -->
                <div class="checkout-section">
                    <h4 class="mb-4">
                        <i class="fas fa-shipping-fast text-primary me-2"></i>
                        Shipping Information
                    </h4>
                    <form id="checkoutForm">
                        <div class="mb-3">
                            <label for="firstName" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">Shipping Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>

                    
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section">
                            <h4 class="mb-4">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                Payment Method
                            </h4>
                            <div class="payment-method selected" onclick="selectPayment('cod', event)">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="paymentMethod" value="cod" checked class="me-3">
                                    <div>
                                        <h6 class="mb-1">Cash on Delivery</h6>
                                        <p class="text-muted mb-0 small">Pay when you receive your order</p>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-method" onclick="selectPayment('card', event)">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="paymentMethod" value="card" class="me-3">
                                    <div>
                                        <h6 class="mb-1">Credit/Debit Card</h6>
                                        <p class="text-muted mb-0 small">Secure payment with your card</p>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-method" onclick="selectPayment('upi', event)">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="paymentMethod" value="upi" class="me-3">
                                    <div>
                                        <h6 class="mb-1">UPI Payment</h6>
                                        <p class="text-muted mb-0 small">Pay using UPI apps</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </form>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-4">
                            <i class="fas fa-shopping-bag text-primary me-2"></i>
                            Order Summary
                        </h5>
                        
                        <!-- Order Items -->
                        <?php foreach ($cart_items_array as $item): ?>
                            <?php 
                            $discounted_price = $item['price'] - $item['discount'];
                            $item_total = $discounted_price * $item['quantity'];
                            ?>
                            <div class="order-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <p class="text-muted small mb-0">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="text-end">
                                        <strong>₹<?php echo number_format($item_total, 2); ?></strong>
                                        <?php if ($item['discount'] > 0): ?>
                                            <br><small class="text-muted"><del>₹<?php echo $item['price']; ?></del></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Order Totals -->
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span><?php echo $shipping == 0 ? 'Free' : '₹' . number_format($shipping, 2); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong class="text-primary">₹<?php echo number_format($total, 2); ?></strong>
                        </div>
                                            
                        <!-- Place Order Button -->
                        <button type="button" class="btn btn-checkout w-100 mb-3" onclick="placeOrder()">
                            <i class="fas fa-lock me-2"></i>
                            Place Order - ₹<?php echo number_format($total, 2); ?>
                        </button>
                        
                        <a href="cart.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Alert -->
    <div class="position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999; margin-top: 20px;">
        <div id="checkoutAlert" class="alert alert-dismissible fade shadow-lg" role="alert" style="display: none; border-radius: 15px; border: none; min-width: 300px;">
            <div class="d-flex align-items-center">
                <i id="alertIcon" class="me-3" style="font-size: 1.2rem;"></i>
                <span id="checkoutAlertMessage" class="fw-semibold"></span>
            </div>
            <button type="button" class="btn-close" onclick="hideCheckoutAlert()"></button>
        </div>
    </div>

    <script>
    function selectPayment(method, event) {
        // Remove selected class from all payment methods
        document.querySelectorAll('.payment-method').forEach(el => {
            el.classList.remove('selected');
        });
        
        // Add selected class to clicked method
        event.currentTarget.classList.add('selected');
        
        // Check the radio button
        document.querySelector(`input[value="${method}"]`).checked = true;
    }

    function showCheckoutAlert(message, type) {
        const alert = document.getElementById('checkoutAlert');
        const alertMessage = document.getElementById('checkoutAlertMessage');
        const alertIcon = document.getElementById('alertIcon');
        
        // Set icon based on type
        if (type === 'success') {
            alertIcon.className = 'fas fa-check-circle me-3';
        } else {
            alertIcon.className = 'fas fa-exclamation-circle me-3';
        }
        
        alert.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
        alertMessage.textContent = message;
        alert.style.display = 'block';
        
        setTimeout(() => {
            hideCheckoutAlert();
        }, 5000);
    }

    function hideCheckoutAlert() {
        const alert = document.getElementById('checkoutAlert');
        alert.style.display = 'none';
    }
    function placeOrder() {
    const form = document.getElementById('checkoutForm');
    // Trigger HTML5 validation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    formData.append('action', 'place_order');
    formData.append('payment_method', document.querySelector('input[name="paymentMethod"]:checked').value);
    formData.append('total_amount', <?php echo $total; ?>);

    // Show loading state
    const button = document.querySelector('.btn-checkout');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    button.disabled = true;

    fetch('includes/orders.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCheckoutAlert(data.message, 'success');
            setTimeout(() => {
                window.location.href = 'order-confirmation.php?order_id=' + data.order_id;
            }, 2000);
        } else {
            showCheckoutAlert(data.message, 'danger');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCheckoutAlert('Something went wrong. Please try again.', 'danger');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>