<?php
// contact_handler.php - Backend handler for contact form AJAX requests
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
    if (empty($data['name']) || strlen($data['name']) < 2)
        $errors[] = 'Name must be at least 2 characters long.';
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Please enter a valid email address.';
    if (empty($data['phone']) || strlen($data['phone']) < 10)
        $errors[] = 'Please enter a valid phone number.';
    if (empty($data['subject']) || strlen($data['subject']) < 5)
        $errors[] = 'Subject must be at least 5 characters long.';
    if (empty($data['message']) || strlen($data['message']) < 10)
        $errors[] = 'Message must be at least 10 characters long.';
    return $errors;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $input  = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $errors = validateContactData($input);

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        $id      = generateUUID();
        $name    = trim($input['name']);
        $email   = trim($input['email']);
        $phone   = trim($input['phone']);
        $subject = trim($input['subject']);
        $message = trim($input['message']);

        try {
            $stmt = $conn->prepare(
                "INSERT INTO contact_submissions (id, name, email, phone, subject, message, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())"
            );
            $stmt->execute([$id, $name, $email, $phone, $subject, $message]);
            echo json_encode([
                'success'       => true,
                'message'       => "Thank you for contacting Stylique! We'll get back to you soon.",
                'submission_id' => $id
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'GET':
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $limit  = isset($_GET['limit'])  ? intval($_GET['limit']) : 50;

        $query  = "SELECT * FROM contact_submissions";
        $params = [];
        if ($status) {
            $query   .= " WHERE status = ?";
            $params[] = $status;
        }
        $query .= " ORDER BY created_at DESC LIMIT " . $limit;

        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll());
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        $path      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));

        if (count($pathParts) >= 2) {
            $submissionId = $pathParts[count($pathParts) - 2];
            $newStatus    = isset($_GET['status']) ? $_GET['status'] : 'read';

            if (!in_array($newStatus, ['new', 'read', 'replied'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid status. Must be: new, read, or replied']);
                exit;
            }

            try {
                $stmt = $conn->prepare("UPDATE contact_submissions SET status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$newStatus, $submissionId]);
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Contact submission not found.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request format.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        break;
}
