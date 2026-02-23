<?php
// backend/api/controllers/ServicesController.php

require_once 'models/Service.php';

class ServicesController {
    
    public function getAll() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $service = new Service();
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $language = isset($_GET['language']) ? $_GET['language'] : 'en';
        
        $results = $service->getAllWithLanguage($limit, $offset, $language);
        
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
        
        if (!isset($params[0])) {
            http_response_code(400);
            echo json_encode(['error' => 'Service ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        $language = $_GET['language'] ?? 'en';
        $service = new Service();
        $result = $service->getByIdWithLanguage($id, $language);
        
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
                'message' => 'Service not found'
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
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['title']) || !isset($input['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and description are required']);
            return;
        }
        
        $service = new Service();
        $service->title = $input['title'];
        $service->description = $input['description'];
        $service->title_fr = $input['title_fr'] ?? '';
        $service->description_fr = $input['description_fr'] ?? '';
        $service->status = $input['status'] ?? 'active';
        
        // Handle file upload if icon is being provided
        if (isset($_FILES['icon'])) {
            $uploadHandler = new UploadHandler();
            $iconPath = $uploadHandler->uploadFile($_FILES['icon'], 'services/');
            
            if ($iconPath) {
                $service->icon_path = $iconPath;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Icon upload failed', 'details' => $uploadHandler->getErrors()]);
                return;
            }
        } elseif (isset($input['icon_path'])) {
            $service->icon_path = $input['icon_path'];
        }
        
        $result = $service->create();
        
        if ($result) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Service created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create service'
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
            echo json_encode(['error' => 'Service ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided']);
            return;
        }
        
        $service = new Service();
        $service->id = $id;
        $service->title = $input['title'] ?? $service->title;
        $service->description = $input['description'] ?? $service->description;
        $service->title_fr = $input['title_fr'] ?? $service->title_fr;
        $service->description_fr = $input['description_fr'] ?? $service->description_fr;
        $service->status = $input['status'] ?? $service->status;
        
        // Handle file upload if icon is being updated
        if (isset($_FILES['icon'])) {
            $uploadHandler = new UploadHandler();
            $iconPath = $uploadHandler->uploadFile($_FILES['icon'], 'services/');
            
            if ($iconPath) {
                $service->icon_path = $iconPath;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Icon upload failed', 'details' => $uploadHandler->getErrors()]);
                return;
            }
        } elseif (isset($input['icon_path'])) {
            $service->icon_path = $input['icon_path'];
        }
        
        if ($service->update()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Service updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update service'
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
            echo json_encode(['error' => 'Service ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        $service = new Service();
        $service->id = $id;
        
        if ($service->delete()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete service'
            ]);
        }
    }
}