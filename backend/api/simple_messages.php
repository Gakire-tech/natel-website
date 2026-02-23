<?php
// Simple messages API endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Database connection
    $host = 'localhost';
    $dbname = 'natel_enterprise';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Get all messages
            $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $messages,
                'count' => count($messages)
            ]);
            break;
            
        case 'POST':
            // Create new message (typically from contact form)
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['name']) || !isset($input['email']) || !isset($input['subject']) || !isset($input['message'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name, email, subject, and message required']);
                exit();
            }
            
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, company, service, contact_method, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())");
            $result = $stmt->execute([
                $input['name'],
                $input['email'],
                $input['phone'] ?? null,
                $input['company'] ?? null,
                $input['service'] ?? null,
                $input['contact_method'] ?? $input['contactMethod'] ?? null,
                $input['subject'],
                $input['message']
            ]);
            
            if ($result) {
                $message_id = $pdo->lastInsertId();
                
                // Send confirmation email to the visitor
                try {
                    // Load EmailSender
                    require_once 'utils/EmailSender.php';
                    
                    // Get the confirmation message from settings
                    $settings_stmt = $pdo->query("SELECT confirmation_message, site_title FROM settings LIMIT 1");
                    $settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);
                    $confirmationMessage = $settings['confirmation_message'] ?? 'Thank you for contacting us. We have received your message and will respond shortly.';
                    
                    // Prepare additional info for the email
                    $additionalInfo = [
                        'subject' => $input['subject'],
                        'date' => date('Y-m-d H:i:s'),
                        'message_preview' => substr($input['message'], 0, 100) . (strlen($input['message']) > 100 ? '...' : '')
                    ];
                    
                    // Send confirmation email
                    $emailSent = EmailSender::sendConfirmationEmail(
                        $input['email'],
                        $input['name'],
                        'Confirmation: Your Message Has Been Received',
                        $confirmationMessage,
                        $additionalInfo
                    );
                    
                    // Log if email sending failed, but don't fail the whole operation
                    if (!$emailSent) {
                        error_log('Failed to send confirmation email to: ' . $input['email']);
                    }
                } catch (Exception $e) {
                    // Log the error but don't fail the operation
                    error_log('Error sending confirmation email: ' . $e->getMessage());
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Message created successfully',
                    'data' => ['id' => $message_id]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to create message']);
            }
            break;
            
        case 'PUT':
            // Update message (status change)
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            
            if (end($path_parts) === 'status') {
                // Update status
                array_pop($path_parts); // Remove 'status'
                $message_id = end($path_parts);
                
                if (!$message_id || !is_numeric($message_id)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid message ID']);
                    exit();
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['status'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Status required']);
                    exit();
                }
                
                $stmt = $pdo->prepare("UPDATE messages SET status = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([
                    $input['status'],
                    $message_id
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Message status updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to update message status']);
                }
            } else {
                // Update entire message
                $message_id = end($path_parts);
                
                if (!$message_id || !is_numeric($message_id)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid message ID']);
                    exit();
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                
                $stmt = $pdo->prepare("UPDATE messages SET name = ?, email = ?, phone = ?, company = ?, service = ?, contact_method = ?, subject = ?, message = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([
                    $input['name'] ?? '',
                    $input['email'] ?? '',
                    $input['phone'] ?? null,
                    $input['company'] ?? null,
                    $input['service'] ?? null,
                    $input['contact_method'] ?? $input['contactMethod'] ?? null,
                    $input['subject'] ?? '',
                    $input['message'] ?? '',
                    $input['status'] ?? 'new',
                    $message_id
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Message updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to update message']);
                }
            }
            break;
            
        case 'DELETE':
            // Delete message
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $message_id = end($path_parts);
            
            if (!$message_id || !is_numeric($message_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid message ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
            $result = $stmt->execute([$message_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete message']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>