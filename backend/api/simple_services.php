<?php
// Simple services API endpoint
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
            // Get all services
            $language = $_GET['language'] ?? 'en';
            
            if ($language === 'fr') {
                $stmt = $pdo->query("SELECT *, title_fr as title, description_fr as description FROM services ORDER BY id ASC");
            } else {
                $stmt = $pdo->query("SELECT *, title as title, description as description FROM services ORDER BY id ASC");
            }
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $services,
                'count' => count($services)
            ]);
            break;
            
        case 'POST':
            // Handle both create and update operations
            // Check if this is an update action first
            if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id'])) {
                // Update existing service
                $service_id = $_POST['id'];
                
                // Handle both 'name' and 'title' for compatibility
                $name_value = $_POST['name'] ?? $_POST['title'] ?? null;
                $description_value = $_POST['description'] ?? null;
                
                if (!is_numeric($service_id) || !$name_value || !$description_value) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid service ID or missing required fields']);
                    exit();
                }
                
                // Handle icon upload if present
                $icon_path = null;
                if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $file = $_FILES['icon'];
                    $fileName = uniqid() . '_' . basename($file['name']);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $icon_path = $fileName;
                    }
                }
                
                // Prepare update query
                if ($icon_path) {
                    // Update with new icon
                    $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon_path = ?, status = ?, updated_at = NOW() WHERE id = ?");
                    $result = $stmt->execute([
                        $name_value,
                        $description_value,
                        $icon_path,
                        $_POST['status'] ?? 'active',
                        $service_id
                    ]);
                } else {
                    // Update without changing icon
                    $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?");
                    $result = $stmt->execute([
                        $name_value,
                        $description_value,
                        $_POST['status'] ?? 'active',
                        $service_id
                    ]);
                }
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Service updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to update service']);
                }
                exit();
            }
            
            // Create new service
            // Handle both JSON and FormData
            $input = [];
            if ((isset($_POST['name']) || isset($_POST['title'])) && isset($_POST['description'])) {
                // FormData request - handle both 'name' and 'title' for compatibility
                $input = [
                    'name' => $_POST['name'] ?? $_POST['title'],
                    'description' => $_POST['description'],
                    'title_fr' => $_POST['title_fr'] ?? $_POST['name_fr'] ?? '',
                    'description_fr' => $_POST['description_fr'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];
            } else {
                // JSON request
                $json_input = json_decode(file_get_contents('php://input'), true);
                if ($json_input && (isset($json_input['name']) || isset($json_input['title'])) && isset($json_input['description'])) {
                    // Handle both 'name' and 'title' for compatibility
                    $input = [
                        'name' => $json_input['name'] ?? $json_input['title'],
                        'description' => $json_input['description'],
                        'title_fr' => $json_input['title_fr'] ?? $json_input['name_fr'] ?? '',
                        'description_fr' => $json_input['description_fr'] ?? '',
                        'status' => $json_input['status'] ?? 'active'
                    ];
                }
            }
            
            if (empty($input['name']) || empty($input['description'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name and description required']);
                exit();
            }
            
            // Handle icon upload if present
            $icon_path = null;
            if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $file = $_FILES['icon'];
                $fileName = uniqid() . '_' . basename($file['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $icon_path = $fileName;
                }
            }
            
            if ($icon_path) {
                $stmt = $pdo->prepare("INSERT INTO services (title, description, title_fr, description_fr, icon_path, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $result = $stmt->execute([
                    $input['name'],
                    $input['description'],
                    $input['title_fr'] ?? '',
                    $input['description_fr'] ?? '',
                    $icon_path,
                    $input['status']
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO services (title, description, title_fr, description_fr, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
                $result = $stmt->execute([
                    $input['name'],
                    $input['description'],
                    $input['title_fr'] ?? '',
                    $input['description_fr'] ?? '',
                    $input['status']
                ]);
            }
            
            if ($result) {
                $service_id = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'Service created successfully',
                    'data' => ['id' => $service_id]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to create service']);
            }
            break;
            
        case 'PUT':
            // Update service
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $service_id = end($path_parts);
            
            if (!$service_id || !is_numeric($service_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid service ID']);
                exit();
            }
            
            // Handle both JSON and FormData
            $input = [];
            if ((isset($_POST['name']) || isset($_POST['title'])) && isset($_POST['description'])) {
                // FormData request - handle both 'name' and 'title' for compatibility
                $input = [
                    'name' => $_POST['name'] ?? $_POST['title'],
                    'description' => $_POST['description'],
                    'title_fr' => $_POST['title_fr'] ?? $_POST['name_fr'] ?? '',
                    'description_fr' => $_POST['description_fr'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];
            } else {
                // JSON request
                $json_input = json_decode(file_get_contents('php://input'), true);
                if ($json_input && (isset($json_input['name']) || isset($json_input['title'])) && isset($json_input['description'])) {
                    // Handle both 'name' and 'title' for compatibility
                    $input = [
                        'name' => $json_input['name'] ?? $json_input['title'],
                        'description' => $json_input['description'],
                        'title_fr' => $json_input['title_fr'] ?? $json_input['name_fr'] ?? '',
                        'description_fr' => $json_input['description_fr'] ?? '',
                        'status' => $json_input['status'] ?? 'active'
                    ];
                }
            }
            
            if (empty($input['name']) || empty($input['description'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name and description required']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, title_fr = ?, description_fr = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([
                $input['name'],
                $input['description'],
                $input['title_fr'] ?? '',
                $input['description_fr'] ?? '',
                $input['status'],
                $service_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update service']);
            }
            break;
            
        case 'DELETE':
            // Delete service
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $service_id = end($path_parts);
            
            if (!$service_id || !is_numeric($service_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid service ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $result = $stmt->execute([$service_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete service']);
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