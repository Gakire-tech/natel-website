<?php
// backend/api/controllers/MessagesController.php

require_once 'models/Message.php';

class MessagesController {
    
    public function getAll() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Verify JWT token
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$auth_header || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. No token provided.']);
            return;
        }
        
        $token = $matches[1];
        $decoded = JwtHandler::validateToken($token);
        
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. Invalid token.']);
            return;
        }
        
        $message = new Message();
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $results = $message->getAll($limit, $offset);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }
    
    public function getById($params) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Verify JWT token
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$auth_header || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. No token provided.']);
            return;
        }
        
        $token = $matches[1];
        $decoded = JwtHandler::validateToken($token);
        
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. Invalid token.']);
            return;
        }
        
        if (!isset($params[0])) {
            http_response_code(400);
            echo json_encode(['error' => 'Message ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        $message = new Message();
        $result = $message->getById($id);
        
        if ($result) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Message not found'
            ]);
        }
    }
    
    public function create() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['name']) || !isset($input['email']) || !isset($input['message'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, email, and message are required']);
            return;
        }
        
        // Validate email
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            return;
        }
        
        $message = new Message();
        $message->name = $input['name'];
        $message->email = $input['email'];
        $message->subject = $input['subject'] ?? '';
        $message->message = $input['message'];
        
        $result = $message->create();
        
        if ($result) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Message sent successfully',
                'id' => $result
            ]);
            
            // Send confirmation email to the visitor
            require_once 'utils/EmailSender.php';
            try {
                // Get the confirmation message from settings
                require_once 'models/Settings.php';
                $settings = new Settings();
                $settingsData = $settings->getAll();
                $confirmationMessage = $settingsData['confirmation_message'] ?? 'Thank you for contacting us. We have received your message and will respond shortly.';
                
                // Prepare additional info for the email
                $additionalInfo = [
                    'subject' => $message->subject,
                    'date' => date('Y-m-d H:i:s'),
                    'message_preview' => substr($message->message, 0, 100) . (strlen($message->message) > 100 ? '...' : '')
                ];
                
                // Send confirmation email
                $emailSent = EmailSender::sendConfirmationEmail(
                    $message->email,
                    $message->name,
                    'Confirmation: Your Message Has Been Received',
                    $confirmationMessage,
                    $additionalInfo
                );
                
                // Log if email sending failed, but don't fail the whole operation
                if (!$emailSent) {
                    error_log("Failed to send confirmation email to: " . $message->email);
                }
            } catch (Exception $e) {
                // Log the error but don't fail the operation
                error_log("Error sending confirmation email: " . $e->getMessage());
            }
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send message'
            ]);
        }
    }
    
    public function updateStatus($params) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'PUT') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Verify JWT token
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$auth_header || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. No token provided.']);
            return;
        }
        
        $token = $matches[1];
        $decoded = JwtHandler::validateToken($token);
        
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. Invalid token.']);
            return;
        }
        
        if (!isset($params[0])) {
            http_response_code(400);
            echo json_encode(['error' => 'Message ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Status is required']);
            return;
        }
        
        $message = new Message();
        $message->id = $id;
        $message->status = $input['status'];
        
        if ($message->updateStatus()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Message status updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update message status'
            ]);
        }
    }
    
    public function delete($params) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Verify JWT token
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$auth_header || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. No token provided.']);
            return;
        }
        
        $token = $matches[1];
        $decoded = JwtHandler::validateToken($token);
        
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. Invalid token.']);
            return;
        }
        
        if (!isset($params[0])) {
            http_response_code(400);
            echo json_encode(['error' => 'Message ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        $message = new Message();
        $message->id = $id;
        
        if ($message->delete()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete message'
            ]);
        }
    }
}