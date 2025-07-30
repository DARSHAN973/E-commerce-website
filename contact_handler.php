<?php
// contact_handler.php - Separate backend handler for AJAX requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'includes/db.php';

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function validateContactData($data) {
    $errors = [];
    
    if (empty($data['name']) || strlen($data['name']) < 2) {
        $errors[] = 'Name must be at least 2 characters long.';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($data['phone']) || strlen($data['phone']) < 10) {
        $errors[] = 'Please enter a valid phone number.';
    }
    
    if (empty($data['subject']) || strlen($data['subject']) < 5) {
        $errors[] = 'Subject must be at least 5 characters long.';
    }
    
    if (empty($data['message']) || strlen($data['message']) < 10) {
        $errors[] = 'Message must be at least 10 characters long.';
    }
    
    return $errors;
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Handle contact form submission
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $errors = validateContactData($input);
        
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }
        
        // Sanitize input
        $id = generateUUID();
        $name = trim($input['name']);
        $email = trim($input['email']);
        $phone = trim($input['phone']);
        $subject = trim($input['subject']);
        $message = trim($input['message']);
        
        // Insert into database
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO contact_submissions (id, name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())"
        );
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $id, $name, $email, $phone, $subject, $message);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you for contacting Stylique! We\'ll get back to you soon.',
                    'submission_id' => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error: ' . mysqli_error($conn)
                ]);
            }
            mysqli_stmt_close($stmt);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database connection error.'
            ]);
        }
        break;
        
    case 'GET':
        // Handle getting contact submissions (admin)
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
        
        $query = "SELECT * FROM contact_submissions";
        $params = [];
        $types = "";
        
        if ($status) {
            $query .= " WHERE status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $submissions = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $submissions[] = $row;
            }
            
            echo json_encode($submissions);
            mysqli_stmt_close($stmt);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error.'
            ]);
        }
        break;
        
    case 'PUT':
        // Handle updating contact status (admin)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        
        if (count($pathParts) >= 2) {
            $submissionId = $pathParts[count($pathParts) - 2]; // Get submission ID
            $newStatus = isset($_GET['status']) ? $_GET['status'] : 'read';
            
            if (!in_array($newStatus, ['new', 'read', 'replied'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid status. Must be: new, read, or replied'
                ]);
                exit;
            }
            
            $stmt = mysqli_prepare($conn, "UPDATE contact_submissions SET status = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $newStatus, $submissionId);
                
                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Status updated successfully.'
                        ]);
                    } else {
                        http_response_code(404);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Contact submission not found.'
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update status.'
                    ]);
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request format.'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed.'
        ]);
        break;
}

mysqli_close($conn);
?>