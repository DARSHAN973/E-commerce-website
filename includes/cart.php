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
        $product_check = "SELECT id, name, stock FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $product_check);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found!']);
            exit;
        }
        
        $product = mysqli_fetch_assoc($result);
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available!']);
            exit;
        }
        
        // Check if item already exists in cart
        $cart_check = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $cart_check);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $cart_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($cart_result) > 0) {
            // Update existing cart item
            $cart_item = mysqli_fetch_assoc($cart_result);
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock']) {
                echo json_encode(['success' => false, 'message' => 'Cannot add more items. Stock limit reached!']);
                exit;
            }
            
            $update_cart = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_cart);
            mysqli_stmt_bind_param($stmt, "ii", $new_quantity, $cart_item['id']);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Cart updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart!']);
            }
        } else {
            // Add new item to cart
            $add_to_cart = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $add_to_cart);
            mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $quantity);
            
            if (mysqli_stmt_execute($stmt)) {
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
            $remove_item = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $remove_item);
            mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Item removed from cart!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove item!']);
            }
        } else {
            // Update quantity
            $update_cart = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $update_cart);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Cart updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart!']);
            }
        }
        
    } elseif ($action === 'remove_from_cart') {
        $cart_id = intval($_POST['cart_id']);
        
        $remove_item = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $remove_item);
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
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
    $cart_count = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $cart_count);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result);
    return $count['total'] ?? 0;
}
?>