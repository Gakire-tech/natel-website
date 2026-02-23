<?php
// backend/api/controllers/AboutController.php

require_once 'models/AboutPage.php';

class AboutController {
    
    public function getAbout() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $about = new AboutPage();
        $result = $about->getAboutPage();
        
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
                'message' => 'About page content not found'
            ]);
        }
    }
    
    public function updateAbout() {
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
        
        // Only allow admin or editor users to update about page
        if ($decoded->data->role !== 'admin' && $decoded->data->role !== 'editor') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied. Insufficient privileges.']);
            return;
        }
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided']);
            return;
        }
        
        $about = new AboutPage();
        $about->id = 1; // We only have one about page record
        $about->main_content = $input['main_content'] ?? $about->main_content;
        $about->main_content_fr = $input['main_content_fr'] ?? $about->main_content_fr;
        $about->mission = $input['mission'] ?? $about->mission;
        $about->mission_fr = $input['mission_fr'] ?? $about->mission_fr;
        $about->vision = $input['vision'] ?? $about->vision;
        $about->vision_fr = $input['vision_fr'] ?? $about->vision_fr;
        $about->values_content = $input['values_content'] ?? $about->values_content;
        $about->values_content_fr = $input['values_content_fr'] ?? $about->values_content_fr;
        
        // Handle file upload if image is being updated
        if (isset($_FILES['image'])) {
            $uploadHandler = new UploadHandler();
            $imagePath = $uploadHandler->uploadFile($_FILES['image'], 'about/');
            
            if ($imagePath) {
                $about->image_path = $imagePath;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Image upload failed', 'details' => $uploadHandler->getErrors()]);
                return;
            }
        } elseif (isset($input['image_path'])) {
            $about->image_path = $input['image_path'];
        }
        
        if ($about->update()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'About page updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update about page'
            ]);
        }
    }
}