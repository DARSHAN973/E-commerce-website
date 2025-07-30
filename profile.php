<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user details from database (fresh data)
$user_query = "SELECT * FROM login_data WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Check if user exists
if (!$user) {
    session_destroy();
    header('Location: index.php?error=user_not_found');
    exit;
}

// Get user's recent orders (fixed query - removed order_number)
$orders_query = "SELECT o.id, o.total_amount, o.status, o.created_at,
                 COUNT(oi.id) as item_count
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 WHERE o.user_id = ? 
                 GROUP BY o.id 
                 ORDER BY o.created_at DESC 
                 LIMIT 5";
$stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$recent_orders = mysqli_stmt_get_result($stmt);

// Get total orders count
$total_orders_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $total_orders_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$total_orders_result = mysqli_stmt_get_result($stmt);
$total_orders_data = mysqli_fetch_assoc($total_orders_result);
$total_orders = $total_orders_data['total'] ?? 0;

// Get cart count
$cart_count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $cart_count_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_result = mysqli_stmt_get_result($stmt);
$cart_data = mysqli_fetch_assoc($cart_result);
$cart_count = $cart_data['total'] ?? 0;

$activePage = 'profile';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Stylique</title>
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
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <!-- User Info Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-4x text-muted"></i>
                        </div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                        <a href="cart.php" class="btn btn-outline-dark btn-sm w-100 mb-2">
                            <i class="fas fa-shopping-cart me-2"></i>My Cart (<?php echo $cart_count; ?>)
                        </a>
                        <button class="btn btn-danger btn-sm w-100" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Profile Overview -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fas fa-edit me-1"></i>Edit Profile
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <i class="fas fa-shopping-bag fa-2x text-primary mb-2"></i>
                                <h4 class="mb-1"><?php echo $total_orders; ?></h4>
                                <p class="small text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <i class="fas fa-shopping-cart fa-2x text-success mb-2"></i>
                                <h4 class="mb-1"><?php echo $cart_count; ?></h4>
                                <p class="small text-muted mb-0">Items in Cart</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                <h4 class="mb-1">0</h4>
                                <p class="small text-muted mb-0">Wishlist Items</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-history me-2"></i>Recent Orders</h5>
                        <?php if (mysqli_num_rows($recent_orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                        <tr>
                                            <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo $order['item_count']; ?> items</td>
                                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $order['status'] == 'delivered' ? 'success' : 
                                                        ($order['status'] == 'shipped' ? 'info' : 
                                                        ($order['status'] == 'cancelled' ? 'danger' : 'warning')); 
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h6>No orders yet</h6>
                                <p class="text-muted">Start shopping to see your orders here!</p>
                                <a href="index.php" class="btn btn-dark">Start Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editName" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="editPhone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="editAddress" name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateProfile()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Alert -->
    <div class="position-fixed top-0 start-50 translate-middle-x" style="z-index: 9999; margin-top: 20px;">
        <div id="profileAlert" class="alert alert-dismissible fade" role="alert" style="display: none;">
            <span id="profileAlertMessage"></span>
            <button type="button" class="btn-close" onclick="hideProfileAlert()"></button>
        </div>
    </div>

    <script>
    function showProfileAlert(message, type) {
        const alert = document.getElementById('profileAlert');
        const alertMessage = document.getElementById('profileAlertMessage');
        
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alertMessage.textContent = message;
        alert.style.display = 'block';
        
        setTimeout(() => {
            hideProfileAlert();
        }, 4000);
    }

    function hideProfileAlert() {
        const alert = document.getElementById('profileAlert');
        alert.style.display = 'none';
    }

    function updateProfile() {
        const name = document.getElementById('editName').value;
        const phone = document.getElementById('editPhone').value;
        const address = document.getElementById('editAddress').value;
        
        if (!name.trim() || !phone.trim() || !address.trim()) {
            showProfileAlert('Please fill in all fields', 'danger');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'update_profile');
        formData.append('name', name);
        formData.append('phone', phone);
        formData.append('address', address);
        
        fetch('includes/profile-update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showProfileAlert(data.message, 'success');
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                modal.hide();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showProfileAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showProfileAlert('Something went wrong. Please try again.', 'danger');
        });
    }

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'includes/logout.php';
        }
    }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>