<?php
// backend/api/controllers/ProjectsController.php

require_once 'models/Project.php';

class ProjectsController {
    
    public function getAll() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $project = new Project();
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $results = $project->getAll($limit, $offset);
        
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
            echo json_encode(['error' => 'Project ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        $project = new Project();
        $result = $project->getById($id);
        
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
                'message' => 'Project not found'
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
        
        if (!$input || !isset($input['name']) || !isset($input['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and description are required']);
            return;
        }
        
        $project = new Project();
        $project->name = $input['name'];
        $project->name_fr = $input['name_fr'] ?? '';
        $project->description = $input['description'];
        $project->description_fr = $input['description_fr'] ?? '';
        $project->client = $input['client'] ?? null;
        $project->status = $input['status'] ?? 'active';
        $project->project_date = $input['project_date'] ?? date('Y-m-d');
        $project->technologies = $input['technologies'] ?? null;
        
        // Handle file upload if image is being provided
        if (isset($_FILES['image'])) {
            $uploadHandler = new UploadHandler();
            $imagePath = $uploadHandler->uploadFile($_FILES['image'], 'projects/');
            
            if ($imagePath) {
                $project->image_path = $imagePath;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Image upload failed', 'details' => $uploadHandler->getErrors()]);
                return;
            }
        } elseif (isset($input['image_path'])) {
            $project->image_path = $input['image_path'];
        }
        
        $result = $project->create();
        
        if ($result) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Project created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create project'
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
            echo json_encode(['error' => 'Project ID is required']);
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
        
        $project = new Project();
        $project->id = $id;
        $project->name = $input['name'] ?? $project->name;
        $project->name_fr = $input['name_fr'] ?? $project->name_fr;
        $project->description = $input['description'] ?? $project->description;
        $project->description_fr = $input['description_fr'] ?? $project->description_fr;
        $project->client = $input['client'] ?? $project->client;
        $project->status = $input['status'] ?? $project->status;
        $project->project_date = $input['project_date'] ?? $project->project_date;
        $project->technologies = $input['technologies'] ?? $project->technologies;
        
        // Handle file upload if image is being updated
        if (isset($_FILES['image'])) {
            $uploadHandler = new UploadHandler();
            $imagePath = $uploadHandler->uploadFile($_FILES['image'], 'projects/');
            
            if ($imagePath) {
                $project->image_path = $imagePath;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Image upload failed', 'details' => $uploadHandler->getErrors()]);
                return;
            }
        } elseif (isset($input['image_path'])) {
            $project->image_path = $input['image_path'];
        }
        
        if ($project->update()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Project updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update project'
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
            echo json_encode(['error' => 'Project ID is required']);
            return;
        }
        
        $id = (int)$params[0];
        $project = new Project();
        $project->id = $id;
        
        if ($project->delete()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Project deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete project'
            ]);
        }
    }
}