<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        echo json_encode(['success' => false, 'message' => 'Please login to place an order']);
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if ($action === 'place_order') {
        // Get form data - matching frontend field names
        $full_name = mysqli_real_escape_string($conn, $_POST['firstName'] ?? '');
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
        $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
        $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');
        $total_amount = floatval($_POST['total_amount'] ?? 0);
        
        // Validate required fields
        if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($payment_method)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
            exit;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
            exit;
        }
        
        // Check if user has items in cart
        $cart_check = "SELECT COUNT(*) as cart_count FROM cart WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $cart_check);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $cart_result = mysqli_stmt_get_result($stmt);
        $cart_data = mysqli_fetch_assoc($cart_result);
        
        if ($cart_data['cart_count'] == 0) {
            echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
            exit;
        }
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Create order - simplified structure
            $order_query = "INSERT INTO orders (user_id, full_name, email, phone, shipping_address, payment_method, total_amount, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
            $stmt = mysqli_prepare($conn, $order_query);
            mysqli_stmt_bind_param($stmt, "isssssd", $user_id, $full_name, $email, 
                                 $phone, $address, $payment_method, $total_amount);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Failed to create order');
            }
            
            $order_id = mysqli_insert_id($conn);
            
            // Get cart items and add to order_items
            $cart_items_query = "SELECT c.product_id, c.quantity, p.name, p.price, p.discount, p.stock 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                WHERE c.user_id = ?";
            $stmt = mysqli_prepare($conn, $cart_items_query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $cart_items = mysqli_stmt_get_result($stmt);
            
            while ($item = mysqli_fetch_assoc($cart_items)) {
                // Check if enough stock is available
                if ($item['stock'] < $item['quantity']) {
                    throw new Exception('Insufficient stock for product: ' . $item['name']);
                }
                
                $final_price = $item['price'] - $item['discount'];
                $item_total = $final_price * $item['quantity'];
                
                $order_item_query = "INSERT INTO order_items (order_id, product_id, product_name, 
                                   quantity, original_price, discount, final_price, item_total, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $order_item_query);
                mysqli_stmt_bind_param($stmt, "iisidddd", $order_id, $item['product_id'], $item['name'], 
                                     $item['quantity'], $item['price'], $item['discount'], $final_price, $item_total);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Failed to add order items');
                }
                
                // Update product stock
                $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
                $stmt = mysqli_prepare($conn, $update_stock);
                mysqli_stmt_bind_param($stmt, "iii", $item['quantity'], $item['product_id'], $item['quantity']);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Failed to update product stock');
                }
                
                // Check if stock was actually updated (in case of race condition)
                if (mysqli_affected_rows($conn) == 0) {
                    throw new Exception('Stock not available for product: ' . $item['name']);
                }
            }
            
            // Clear user's cart
            $clear_cart = "DELETE FROM cart WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $clear_cart);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Failed to clear cart');
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Order placed successfully! Your order ID is #' . str_pad($order_id, 6, '0', STR_PAD_LEFT),
                'order_id' => $order_id
            ]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>