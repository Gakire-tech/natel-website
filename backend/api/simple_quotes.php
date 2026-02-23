<?php
// Simple quotes API endpoint
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
            // Get all quotes or specific quote
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            
            if (count($path_parts) > 2 && is_numeric(end($path_parts))) {
                // Get specific quote
                $quote_id = end($path_parts);
                $stmt = $pdo->prepare("SELECT q.*, u.username as assigned_user FROM quotes q LEFT JOIN users u ON q.assigned_to = u.id WHERE q.id = ?");
                $stmt->execute([$quote_id]);
                $quote = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($quote) {
                    echo json_encode([
                        'success' => true,
                        'data' => $quote
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Quote not found']);
                }
            } else {
                // Get all quotes with optional filters
                $status = $_GET['status'] ?? null;
                $priority = $_GET['priority'] ?? null;
                
                $sql = "SELECT q.*, u.username as assigned_user FROM quotes q LEFT JOIN users u ON q.assigned_to = u.id";
                $params = [];
                
                if ($status) {
                    $sql .= " WHERE q.status = ?";
                    $params[] = $status;
                }
                
                if ($priority && $status) {
                    $sql .= " AND q.priority = ?";
                    $params[] = $priority;
                } elseif ($priority) {
                    $sql .= " WHERE q.priority = ?";
                    $params[] = $priority;
                }
                
                $sql .= " ORDER BY q.created_at DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $quotes,
                    'count' => count($quotes)
                ]);
            }
            break;
            
        case 'POST':
            // Create new quote (from frontend form)
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['client_name']) || !isset($input['client_email']) || !isset($input['project_type']) || !isset($input['project_details'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Required fields missing']);
                exit();
            }
            
            $stmt = $pdo->prepare("INSERT INTO quotes (client_name, client_email, client_phone, company_name, project_type, budget_range, timeline, project_details, additional_requirements, status, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'medium')");
            $result = $stmt->execute([
                $input['client_name'],
                $input['client_email'],
                $input['client_phone'] ?? null,
                $input['company_name'] ?? null,
                $input['project_type'],
                $input['budget_range'] ?? null,
                $input['timeline'] ?? null,
                $input['project_details'],
                $input['additional_requirements'] ?? null
            ]);
            
            if ($result) {
                $quote_id = $pdo->lastInsertId();
                
                // Send confirmation email to the visitor
                require_once 'utils/EmailSender.php';
                try {
                    // Get the confirmation message from settings
                    $settings_stmt = $pdo->query("SELECT confirmation_message, site_title FROM settings LIMIT 1");
                    $settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);
                    $confirmationMessage = $settings['confirmation_message'] ?? 'Thank you for contacting us. We have received your message and will respond shortly.';
                    
                    // Prepare additional info for the email
                    $additionalInfo = [
                        'project_type' => $input['project_type'],
                        'budget_range' => $input['budget_range'] ?? 'Not specified',
                        'timeline' => $input['timeline'] ?? 'Not specified',
                        'date' => date('Y-m-d H:i:s'),
                        'project_preview' => substr($input['project_details'], 0, 100) . (strlen($input['project_details']) > 100 ? '...' : '')
                    ];
                    
                    // Send confirmation email
                    $emailSent = EmailSender::sendConfirmationEmail(
                        $input['client_email'],
                        $input['client_name'],
                        'Confirmation: Your Quote Request Has Been Received',
                        $confirmationMessage,
                        $additionalInfo
                    );
                    
                    // Log if email sending failed, but don't fail the whole operation
                    if (!$emailSent) {
                        error_log("Failed to send confirmation email to: " . $input['client_email']);
                    }
                } catch (Exception $e) {
                    // Log the error but don't fail the operation
                    error_log("Error sending confirmation email: " . $e->getMessage());
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quote submitted successfully',
                    'data' => ['id' => $quote_id]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to submit quote']);
            }
            break;
            
        case 'PUT':
            // Update quote (admin management)
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $quote_id = end($path_parts);
            
            if (!$quote_id || !is_numeric($quote_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid quote ID']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("UPDATE quotes SET client_name = ?, client_email = ?, client_phone = ?, company_name = ?, project_type = ?, budget_range = ?, timeline = ?, project_details = ?, additional_requirements = ?, status = ?, priority = ?, assigned_to = ?, notes = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([
                $input['client_name'] ?? '',
                $input['client_email'] ?? '',
                $input['client_phone'] ?? null,
                $input['company_name'] ?? null,
                $input['project_type'] ?? '',
                $input['budget_range'] ?? null,
                $input['timeline'] ?? null,
                $input['project_details'] ?? '',
                $input['additional_requirements'] ?? null,
                $input['status'] ?? 'pending',
                $input['priority'] ?? 'medium',
                $input['assigned_to'] ?? null,
                $input['notes'] ?? null,
                $quote_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Quote updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update quote']);
            }
            break;
            
        case 'DELETE':
            // Delete quote
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $quote_id = end($path_parts);
            
            if (!$quote_id || !is_numeric($quote_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid quote ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM quotes WHERE id = ?");
            $result = $stmt->execute([$quote_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Quote deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete quote']);
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