<?php
// Simple about API endpoint
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
            // Get about page data
            $language = $_GET['language'] ?? 'en';
            
            if ($language === 'fr') {
                $stmt = $pdo->query("SELECT *, main_content_fr as main_content, mission_fr as mission, vision_fr as vision, values_content_fr as values_content FROM about_page LIMIT 1");
            } else {
                $stmt = $pdo->query("SELECT *, main_content as main_content, mission as mission, vision as vision, values_content as values_content FROM about_page LIMIT 1");
            }
            $aboutData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$aboutData) {
                // Initialize with default empty data
                $stmt = $pdo->prepare("INSERT INTO about_page (main_content, mission, vision, values_content, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute(['', '', '', '']);
                
                if ($language === 'fr') {
                    $stmt = $pdo->query("SELECT *, main_content_fr as main_content, mission_fr as mission, vision_fr as vision, values_content_fr as values_content FROM about_page LIMIT 1");
                } else {
                    $stmt = $pdo->query("SELECT *, main_content as main_content, mission as mission, vision as vision, values_content as values_content FROM about_page LIMIT 1");
                }
                $aboutData = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            // Get team members with language preference
            $teamStmt = $pdo->prepare("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC");
            $teamStmt->execute();
            $teamMembers = $teamStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process team members for language display while preserving original fields
            foreach ($teamMembers as &$member) {
                // Ensure all fields are converted to empty strings instead of null
                foreach ($member as $key => $value) {
                    if (is_null($value)) {
                        $member[$key] = '';
                    }
                }
                
                // Apply language-specific display based on preference
                if ($language === 'fr') {
                    // For French preference, set display fields to French content with English fallback
                    $member['name'] = !empty(trim($member['name_fr'])) ? $member['name_fr'] : $member['name'];
                    $member['position'] = !empty(trim($member['position_fr'])) ? $member['position_fr'] : $member['position'];
                    $member['bio'] = !empty(trim($member['bio_fr'])) ? $member['bio_fr'] : $member['bio'];
                } else {
                    // For English preference, set display fields to English content with French fallback
                    $member['name'] = !empty(trim($member['name'])) ? $member['name'] : $member['name_fr'];
                    $member['position'] = !empty(trim($member['position'])) ? $member['position'] : $member['position_fr'];
                    $member['bio'] = !empty(trim($member['bio'])) ? $member['bio'] : $member['bio_fr'];
                }
            }
            
            $aboutData['team_members'] = $teamMembers;
            
            echo json_encode([
                'success' => true,
                'data' => $aboutData
            ]);
            break;
            
        case 'PUT':
            // Update about page data
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid input data']);
                exit();
            }
            
            // Update main about data
            $stmt = $pdo->prepare("UPDATE about_page SET main_content = ?, mission = ?, vision = ?, values_content = ?, main_content_fr = ?, mission_fr = ?, vision_fr = ?, values_content_fr = ?, updated_at = NOW() WHERE id = 1");
            $result = $stmt->execute([
                $input['main_content'] ?? '',
                $input['mission'] ?? '',
                $input['vision'] ?? '',
                $input['values_content'] ?? '',
                $input['main_content_fr'] ?? '',
                $input['mission_fr'] ?? '',
                $input['vision_fr'] ?? '',
                $input['values_content_fr'] ?? ''
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'About page updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update about page']);
            }
            break;
            
        case 'POST':
            // Handle file uploads for team members
            if (isset($_FILES['image'])) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $file = $_FILES['image'];
                $fileName = uniqid() . '_' . basename($file['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    echo json_encode([
                        'success' => true,
                        'file_path' => $fileName,
                        'message' => 'File uploaded successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No file provided']);
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