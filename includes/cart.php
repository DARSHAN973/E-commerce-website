<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        echo json_encode(['success' => false, 'message' => 'Please login to add items to cart', 'login_required' => true]);
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if ($action === 'add_to_cart') {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product or quantity!']);
            exit;
        }
        
        // Check if product exists and has enough stock
        $stmt = $conn->prepare("SELECT id, name, stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found!']);
            exit;
        }
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available!']);
            exit;
        }
        
        // Check if item already exists in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Update existing cart item
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock']) {
                echo json_encode(['success' => false, 'message' => 'Cannot add more items. Stock limit reached!']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            if ($stmt->execute([$new_quantity, $cart_item['id']])) {
                echo json_encode(['success' => true, 'message' => 'Cart updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart!']);
            }
        } else {
            // Add new item to cart
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            if ($stmt->execute([$user_id, $product_id, $quantity])) {
                echo json_encode(['success' => true, 'message' => 'Item added to cart successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add item to cart!']);
            }
        }
        
    } elseif ($action === 'update_cart') {
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            // Remove item if quantity is 0 or less
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$cart_id, $user_id])) {
                echo json_encode(['success' => true, 'message' => 'Item removed from cart!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove item!']);
            }
        } else {
            // Update quantity
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$quantity, $cart_id, $user_id])) {
                echo json_encode(['success' => true, 'message' => 'Cart updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart!']);
            }
        }
        
    } elseif ($action === 'remove_from_cart') {
        $cart_id = intval($_POST['cart_id']);
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$cart_id, $user_id])) {
            echo json_encode(['success' => true, 'message' => 'Item removed from cart!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item!']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
}

// Helper function to get cart count
function getCartCount($user_id, $conn) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return $row['total'] ?? 0;
}
?>