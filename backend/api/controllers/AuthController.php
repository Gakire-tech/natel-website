<?php
// backend/api/controllers/AuthController.php

require_once 'models/User.php';

class AuthController {
    
    public function login() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }
        
        $email = $input['email'];
        $password = $input['password'];
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            return;
        }
        
        // Authenticate user
        $user = new User();
        if ($user->authenticate($email, $password)) {
            // Get user details
            $user->getByEmail($email);
            
            // Create JWT token
            $token_data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ];
            
            $token = JwtHandler::generateToken($token_data);
            
            // Return success response with token
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]);
        } else {
            // Invalid credentials
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password']);
        }
    }
}