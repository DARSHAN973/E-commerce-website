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
        $full_name = trim($_POST['firstName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? '');
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
        $stmt = $conn->prepare("SELECT COUNT(*) as cart_count FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_data = $stmt->fetch();
        if ($cart_data['cart_count'] == 0) {
            echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
            exit;
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        try {
            // Create order - use RETURNING id for PostgreSQL
            $order_query = "INSERT INTO orders (user_id, full_name, email, phone, shipping_address, payment_method, total_amount, status, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW()) RETURNING id";
            $stmt = $conn->prepare($order_query);
            if (!$stmt->execute([$user_id, $full_name, $email, $phone, $address, $payment_method, $total_amount])) {
                throw new Exception('Failed to create order');
            }
            $order_id = $stmt->fetchColumn();
            
            // Get cart items and add to order_items
            $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.discount, p.stock
                                    FROM cart c
                                    JOIN products p ON c.product_id = p.id
                                    WHERE c.user_id = ?");
            $stmt->execute([$user_id]);
            $cart_items = $stmt->fetchAll();

            foreach ($cart_items as $item) {
                if ($item['stock'] < $item['quantity']) {
                    throw new Exception('Insufficient stock for product: ' . $item['name']);
                }

                $final_price = $item['price'] - $item['discount'];
                $item_total  = $final_price * $item['quantity'];

                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name,
                                        quantity, original_price, discount, final_price, item_total, created_at)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                if (!$stmt->execute([$order_id, $item['product_id'], $item['name'], $item['quantity'],
                                     $item['price'], $item['discount'], $final_price, $item_total])) {
                    throw new Exception('Failed to add order items');
                }

                // Update product stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                if ($stmt->rowCount() == 0) {
                    throw new Exception('Stock not available for product: ' . $item['name']);
                }
            }
            
            // Clear user's cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            if (!$stmt->execute([$user_id])) {
                throw new Exception('Failed to clear cart');
            }

            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Order placed successfully! Your order ID is #' . str_pad($order_id, 6, '0', STR_PAD_LEFT),
                'order_id' => $order_id
            ]);
            
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>