<?php
// backend/api/controllers/UsersController.php

require_once 'models/User.php';

class UsersController {
    
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
        
        // Only allow admin users to access this endpoint
        if ($decoded->data->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Admin privileges required.']);
            return;
        }
        
        $user = new User();
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $results = $user->getAll($limit, $offset);
        
        // Don't return passwords
        foreach ($results as &$result) {
            unset($result['password']);
        }
        
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
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        
        // Only allow admin users or the user themselves to access this endpoint
        if ($decoded->data->role !== 'admin' && $decoded->data->id != $id) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Insufficient privileges.']);
            return;
        }
        
        $user = new User();
        if ($user->getById($id)) {
            // Don't return password
            $result = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
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
        
        // Only allow admin users to create new users
        if ($decoded->data->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Admin privileges required.']);
            return;
        }
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, email, and password are required']);
            return;
        }
        
        // Validate email
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            return;
        }
        
        $user = new User();
        
        // Check if email already exists
        if ($user->emailExists($input['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }
        
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = password_hash($input['password'], PASSWORD_DEFAULT);
        $user->role = $input['role'] ?? 'editor';
        $user->status = $input['status'] ?? 'active';
        
        $result = $user->create();
        
        if ($result) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create user'
            ]);
        }
    }
    
    public function update($params) {
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
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        
        // Only allow admin users or the user themselves to update
        if ($decoded->data->role !== 'admin' && $decoded->data->id != $id) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Insufficient privileges.']);
            return;
        }
        
        // Only admin can update role and status
        $allowRoleChange = ($decoded->data->role === 'admin');
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided']);
            return;
        }
        
        $user = new User();
        $user->id = $id;
        
        // Load existing user to get current values
        if (!$user->getById($id)) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        $user->name = $input['name'] ?? $user->name;
        $user->email = $input['email'] ?? $user->email;
        
        // Only update password if provided
        if (isset($input['password']) && !empty($input['password'])) {
            $user->password = password_hash($input['password'], PASSWORD_DEFAULT);
        }
        
        // Only admin can change role and status
        if ($allowRoleChange) {
            $user->role = $input['role'] ?? $user->role;
            $user->status = $input['status'] ?? $user->status;
        }
        
        if ($user->update()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update user'
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
        
        // Only allow admin users to delete users
        if ($decoded->data->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Admin privileges required.']);
            return;
        }
        
        if (!isset($params[0])) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        
        // Prevent admin from deleting themselves
        if ($decoded->data->id == $id) {
            http_response_code(400);
            echo json_encode(['error' => 'You cannot delete your own account']);
            return;
        }
        
        $user = new User();
        $user->id = $id;
        
        if ($user->delete()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete user'
            ]);
        }
    }
}