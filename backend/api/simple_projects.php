<?php
// Simple projects API endpoint
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
            // Get all projects
            $language = $_GET['language'] ?? 'en';
            
            if ($language === 'fr') {
                $stmt = $pdo->query("SELECT *, name_fr as name, description_fr as description FROM projects ORDER BY name ASC");
            } else {
                $stmt = $pdo->query("SELECT *, name as name, description as description FROM projects ORDER BY name ASC");
            }
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $projects,
                'count' => count($projects)
            ]);
            break;
            
        case 'POST':
            // Handle both create and update operations
            // Check if this is an update action first
            if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id'])) {
                // Update existing project
                $project_id = $_POST['id'];
                
                if (!is_numeric($project_id) || !isset($_POST['name']) || !isset($_POST['description'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid project ID or missing required fields']);
                    exit();
                }
                
                // Handle image upload if present
                $image_path = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $file = $_FILES['image'];
                    $fileName = uniqid() . '_' . basename($file['name']);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $image_path = $fileName;
                    }
                }
                
                // Prepare update query
                if ($image_path) {
                    // Update with new image
                    $stmt = $pdo->prepare("UPDATE projects SET name = ?, description = ?, name_fr = ?, description_fr = ?, client = ?, technologies = ?, status = ?, project_date = ?, image_path = ?, updated_at = NOW() WHERE id = ?");
                    $result = $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['name_fr'] ?? '',
                        $_POST['description_fr'] ?? '',
                        $_POST['client'] ?? '',
                        $_POST['technologies'] ?? '',
                        $_POST['status'] ?? 'completed',
                        $_POST['project_date'] ?? date('Y-m-d'),
                        $image_path,
                        $project_id
                    ]);
                } else {
                    // Update without changing image
                    $stmt = $pdo->prepare("UPDATE projects SET name = ?, description = ?, name_fr = ?, description_fr = ?, client = ?, technologies = ?, status = ?, project_date = ?, updated_at = NOW() WHERE id = ?");
                    $result = $stmt->execute([
                        $_POST['name'],
                        $_POST['description'],
                        $_POST['name_fr'] ?? '',
                        $_POST['description_fr'] ?? '',
                        $_POST['client'] ?? '',
                        $_POST['technologies'] ?? '',
                        $_POST['status'] ?? 'completed',
                        $_POST['project_date'] ?? date('Y-m-d'),
                        $project_id
                    ]);
                }
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Project updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to update project']);
                }
                exit();
            }
            
            // Create new project
            // Handle both JSON and FormData
            $input = [];
            if ((isset($_POST['name']) || isset($_POST['title'])) && isset($_POST['description'])) {
                // FormData request - handle both 'name' and 'title' for compatibility
                $input = [
                    'name' => $_POST['name'] ?? $_POST['title'],
                    'description' => $_POST['description'],
                    'name_fr' => $_POST['name_fr'] ?? $_POST['title_fr'] ?? '',
                    'description_fr' => $_POST['description_fr'] ?? $_POST['title_fr'] ?? '',
                    'client' => $_POST['client'] ?? '',
                    'technologies' => $_POST['technologies'] ?? '',
                    'status' => $_POST['status'] ?? 'completed',
                    'project_date' => $_POST['project_date'] ?? date('Y-m-d')
                ];
            } else {
                // JSON request
                $json_input = json_decode(file_get_contents('php://input'), true);
                if ($json_input && (isset($json_input['title']) || isset($json_input['name'])) && isset($json_input['description'])) {
                    $input = [
                        'name' => $json_input['title'] ?? $json_input['name'],
                        'description' => $json_input['description'],
                        'name_fr' => $json_input['name_fr'] ?? $json_input['title_fr'] ?? '',
                        'description_fr' => $json_input['description_fr'] ?? $json_input['title_fr'] ?? '',
                        'client' => $json_input['client'] ?? '',
                        'technologies' => $json_input['technologies'] ?? '',
                        'status' => $json_input['status'] ?? 'completed',
                        'project_date' => $json_input['project_date'] ?? date('Y-m-d')
                    ];
                }
            }
            
            if (empty($input['name']) || empty($input['description'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name and description required']);
                exit();
            }
            
            // Handle image upload if present
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $file = $_FILES['image'];
                $fileName = uniqid() . '_' . basename($file['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $image_path = $fileName;
                }
            }
            
            if ($image_path) {
                $stmt = $pdo->prepare("INSERT INTO projects (name, description, name_fr, description_fr, client, technologies, status, project_date, image_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $result = $stmt->execute([
                    $input['name'],
                    $input['description'],
                    $input['name_fr'] ?? '',
                    $input['description_fr'] ?? '',
                    $input['client'],
                    $input['technologies'],
                    $input['status'],
                    $input['project_date'],
                    $image_path
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO projects (name, description, name_fr, description_fr, client, technologies, status, project_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $result = $stmt->execute([
                    $input['name'],
                    $input['description'],
                    $input['name_fr'] ?? '',
                    $input['description_fr'] ?? '',
                    $input['client'],
                    $input['technologies'],
                    $input['status'],
                    $input['project_date']
                ]);
            }
            
            if ($result) {
                $project_id = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'Project created successfully',
                    'data' => ['id' => $project_id]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to create project']);
            }
            break;
            
        case 'PUT':
            // Update project
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $project_id = end($path_parts);
            
            if (!$project_id || !is_numeric($project_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || (!isset($input['title']) && !isset($input['name'])) || !isset($input['description'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Title and description required']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE projects SET name = ?, description = ?, name_fr = ?, description_fr = ?, client = ?, technologies = ?, status = ?, project_date = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([
                $input['title'] ?? $input['name'],
                $input['description'],
                $input['name_fr'] ?? $input['title_fr'] ?? '',
                $input['description_fr'] ?? $input['title_fr'] ?? '',
                $input['client'] ?? '',
                $input['technologies'] ?? '',
                $input['status'] ?? 'completed',
                $input['project_date'] ?? date('Y-m-d'),
                $project_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Project updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update project']);
            }
            break;
            
        case 'DELETE':
            // Delete project
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $project_id = end($path_parts);
            
            if (!$project_id || !is_numeric($project_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $result = $stmt->execute([$project_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Project deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete project']);
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