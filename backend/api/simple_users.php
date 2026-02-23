<?php
// Simple users API endpoint
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
            // Get all users
            $stmt = $pdo->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY name ASC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $users,
                'count' => count($users)
            ]);
            break;
            
        case 'POST':
            // Create new user
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name, email, and password required']);
                exit();
            }
            
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$input['email']]);
            if ($checkStmt->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Email already exists']);
                exit();
            }
            
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
                $input['name'],
                $input['email'],
                $hashedPassword,
                $input['role'] ?? 'editor',
                $input['status'] ?? 'active'
            ]);
            
            if ($result) {
                $user_id = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => ['id' => $user_id]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to create user']);
            }
            break;
            
        case 'PUT':
            // Update user
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $user_id = end($path_parts);
            
            if (!$user_id || !is_numeric($user_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['name']) || !isset($input['email'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Name and email required']);
                exit();
            }
            
            // Check if email already exists for another user
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->execute([$input['email'], $user_id]);
            if ($checkStmt->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Email already exists']);
                exit();
            }
            
            // Build update query dynamically
            $fields = ['name = ?', 'email = ?', 'role = ?', 'status = ?'];
            $values = [$input['name'], $input['email'], $input['role'] ?? 'editor', $input['status'] ?? 'active'];
            
            // Handle password update
            if (isset($input['password']) && !empty($input['password'])) {
                $fields[] = 'password = ?';
                $values[] = password_hash($input['password'], PASSWORD_DEFAULT);
            }
            
            $values[] = $user_id; // For WHERE clause
            
            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update user']);
            }
            break;
            
        case 'DELETE':
            // Delete user
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $user_id = end($path_parts);
            
            if (!$user_id || !is_numeric($user_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
                exit();
            }
            
            // Prevent deleting the last admin user
            $adminCountStmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND id != $user_id");
            $adminCount = $adminCountStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($adminCount < 1) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Cannot delete the last administrator account']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$user_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
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