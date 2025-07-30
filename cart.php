<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_query = "SELECT c.id as cart_id, c.quantity, p.id, p.name, p.price, p.discount, p.image, p.stock 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = ? 
               ORDER BY c.added_at DESC";
$stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_items = mysqli_stmt_get_result($stmt);

$activePage = 'cart';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Stylique</title>
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
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .quantity-btn:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h2>
        
        <?php if (mysqli_num_rows($cart_items) > 0): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <?php 
                            $total = 0;
                            while ($item = mysqli_fetch_assoc($cart_items)): 
                                $discounted_price = $item['price'] - $item['discount'];
                                $item_total = $discounted_price * $item['quantity'];
                                $total += $item_total;
                            ?>
                            <div class="cart-item border-bottom py-3" data-cart-id="<?php echo $item['cart_id']; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-item-img">
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                        <p class="text-muted small mb-0">In Stock: <?php echo $item['stock']; ?></p>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="quantity-controls">
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>₹<?php echo number_format($discounted_price, 2); ?></strong>
                                        <?php if ($item['discount'] > 0): ?>
                                            <br><small class="text-muted"><del>₹<?php echo $item['price']; ?></del></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-3">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">₹<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span><?php echo $total >= 999 ? 'Free' : '₹50'; ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong id="total">₹<?php echo $total >= 999 ? number_format($total, 2) : number_format($total + 50, 2); ?></strong>
                            </div>
                            <a href="checkout.php" class="btn btn-dark w-100 mb-2">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary w-100">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4>Your cart is empty</h4>
                <p class="text-muted mb-4">Start adding some stylish items to your cart!</p>
                <a href="index.php" class="btn btn-dark">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success/Error Alert -->
    <div class="position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999; margin-top: 20px;">
        <div id="cartAlert" class="alert alert-dismissible fade" role="alert" style="display: none;">
            <span id="cartAlertMessage"></span>
            <button type="button" class="btn-close" onclick="hideCartAlert()"></button>
        </div>
    </div>

    <script>
    function showCartAlert(message, type) {
        const alert = document.getElementById('cartAlert');
        const alertMessage = document.getElementById('cartAlertMessage');
        
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alertMessage.textContent = message;
        alert.style.display = 'block';
        
        setTimeout(() => {
            hideCartAlert();
        }, 3000);
    }

    function hideCartAlert() {
        const alert = document.getElementById('cartAlert');
        alert.style.display = 'none';
    }

    function updateQuantity(cartId, newQuantity) {
        const formData = new FormData();
        formData.append('action', 'update_cart');
        formData.append('cart_id', cartId);
        formData.append('quantity', newQuantity);
        
        fetch('includes/cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showCartAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showCartAlert('Something went wrong. Please try again.', 'danger');
        });
    }

    function removeFromCart(cartId) {
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            const formData = new FormData();
            formData.append('action', 'remove_from_cart');
            formData.append('cart_id', cartId);
            
            fetch('includes/cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showCartAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showCartAlert('Something went wrong. Please try again.', 'danger');
            });
        }
    }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>