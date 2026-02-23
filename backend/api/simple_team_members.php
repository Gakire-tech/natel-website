<?php
// Simple team members API endpoint
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
            // Get all team members
            $language = $_GET['language'] ?? 'en';
            
            $stmt = $pdo->query("SELECT * FROM team_members ORDER BY sort_order ASC, created_at DESC");
            $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process each team member to apply language-specific display with fallback
            foreach ($teamMembers as &$member) {
                if ($language === 'fr') {
                    $member['display_name'] = !empty(trim($member['name_fr'])) ? $member['name_fr'] : $member['name'];
                    $member['display_position'] = !empty(trim($member['position_fr'])) ? $member['position_fr'] : $member['position'];
                    $member['display_bio'] = !empty(trim($member['bio_fr'])) ? $member['bio_fr'] : $member['bio'];
                    // Use display fields as primary fields for backward compatibility
                    $member['name'] = $member['display_name'];
                    $member['position'] = $member['display_position'];
                    $member['bio'] = $member['display_bio'];
                } else {
                    $member['display_name'] = !empty(trim($member['name'])) ? $member['name'] : $member['name_fr'];
                    $member['display_position'] = !empty(trim($member['position'])) ? $member['position'] : $member['position_fr'];
                    $member['display_bio'] = !empty(trim($member['bio'])) ? $member['bio'] : $member['bio_fr'];
                    // Use display fields as primary fields for backward compatibility
                    $member['name'] = $member['display_name'];
                    $member['position'] = $member['display_position'];
                    $member['bio'] = $member['display_bio'];
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => $teamMembers,
                'count' => count($teamMembers)
            ]);
            break;
            
        case 'POST':
            // Create new team member with image upload
            if (isset($_FILES['image'])) {
                // Handle image upload
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $file = $_FILES['image'];
                $fileName = uniqid() . '_' . basename($file['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Get other form data
                    $name = $_POST['name'] ?? '';
                    $position = $_POST['position'] ?? '';
                    $bio = $_POST['bio'] ?? '';
                    $email = $_POST['email'] ?? null;
                    $phone = $_POST['phone'] ?? null;
                    $linkedin_url = $_POST['linkedin_url'] ?? null;
                    $twitter_url = $_POST['twitter_url'] ?? null;
                    $status = $_POST['status'] ?? 'active';
                    
                    $stmt = $pdo->prepare("INSERT INTO team_members (name, position, bio, name_fr, position_fr, bio_fr, image_path, email, phone, linkedin_url, twitter_url, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, COALESCE((SELECT MAX(sort_order) + 1 FROM team_members), 1), NOW())");
                    $result = $stmt->execute([
                        $name,
                        $position,
                        $bio,
                        $_POST['name_fr'] ?? '',
                        $_POST['position_fr'] ?? '',
                        $_POST['bio_fr'] ?? '',
                        $fileName,
                        $email,
                        $phone,
                        $linkedin_url,
                        $twitter_url,
                        $status
                    ]);
                    
                    if ($result) {
                        $member_id = $pdo->lastInsertId();
                        echo json_encode([
                            'success' => true,
                            'message' => 'Team member created successfully',
                            'data' => [
                                'id' => $member_id,
                                'file_path' => $fileName
                            ]
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'error' => 'Failed to create team member']);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to upload image']);
                }
            } else {
                // Create without image
                // Check if data comes from form data or JSON
                $name = $_POST['name'] ?? null;
                $position = $_POST['position'] ?? null;
                
                if (!$name || !$position) {
                    // Try JSON input
                    $input = json_decode(file_get_contents('php://input'), true);
                    $name = $input['name'] ?? null;
                    $position = $input['position'] ?? null;
                }
                
                if (!$name || !$position) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Name and position required']);
                    exit();
                }
                
                $bio = $_POST['bio'] ?? $input['bio'] ?? '';
                $email = $_POST['email'] ?? $input['email'] ?? null;
                $phone = $_POST['phone'] ?? $input['phone'] ?? null;
                $linkedin_url = $_POST['linkedin_url'] ?? $input['linkedin_url'] ?? null;
                $twitter_url = $_POST['twitter_url'] ?? $input['twitter_url'] ?? null;
                $status = $_POST['status'] ?? $input['status'] ?? 'active';
                
                $stmt = $pdo->prepare("INSERT INTO team_members (name, position, bio, name_fr, position_fr, bio_fr, email, phone, linkedin_url, twitter_url, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT sort_order FROM team_members) AS temp), NOW())");
                $result = $stmt->execute([
                    $name,
                    $position,
                    $bio,
                    $_POST['name_fr'] ?? $input['name_fr'] ?? '',
                    $_POST['position_fr'] ?? $input['position_fr'] ?? '',
                    $_POST['bio_fr'] ?? $input['bio_fr'] ?? '',
                    $email,
                    $phone,
                    $linkedin_url,
                    $twitter_url,
                    $status
                ]);
                
                if ($result) {
                    $member_id = $pdo->lastInsertId();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Team member created successfully',
                        'data' => ['id' => $member_id]
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to create team member']);
                }
            }
            break;
            
        case 'PUT':
            // Update team member
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $member_id = end($path_parts);
            
            if (!$member_id || !is_numeric($member_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid member ID']);
                exit();
            }
            
            // Check if this is a multipart request (for file uploads)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'multipart/form-data') !== false) {
                // Extract boundary from content type
                preg_match('/boundary=(.+)$/', $contentType, $matches);
                if (isset($matches[1])) {
                    $boundary = '--' . $matches[1];
                    
                    // Read raw input
                    $rawInput = file_get_contents('php://input');
                    
                    // Split the raw input by boundary
                    $parts = explode($boundary, $rawInput);
                    
                    $formData = [];
                    $fileData = null;
                    
                    foreach ($parts as $part) {
                        if (strpos($part, 'Content-Disposition:') !== false) {
                            // Extract field name
                            if (preg_match('/name="([^"]+)"/', $part, $nameMatches)) {
                                $fieldName = $nameMatches[1];
                                
                                // Check if it's a file field
                                if (preg_match('/filename="([^"]*)"/', $part, $filenameMatches)) {
                                    // This is a file upload
                                    $fileStart = strpos($part, "\r\n\r\n") + 4;
                                    $fileEnd = strpos($part, "\r\n--", $fileStart);
                                    if ($fileEnd === false) $fileEnd = strlen($part) - 2; // End of data
                                    $fileContent = substr($part, $fileStart, $fileEnd - $fileStart);
                                    
                                    // Create temporary file
                                    $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
                                    file_put_contents($tempFile, $fileContent);
                                    
                                    $fileData = [
                                        'name' => $filenameMatches[1],
                                        'type' => 'application/octet-stream', // We don't have the actual content-type
                                        'tmp_name' => $tempFile,
                                        'error' => UPLOAD_ERR_OK,
                                        'size' => strlen($fileContent)
                                    ];
                                } else {
                                    // This is a regular form field
                                    $valueStart = strpos($part, "\r\n\r\n") + 4;
                                    $valueEnd = strpos($part, "\r\n--", $valueStart);
                                    if ($valueEnd === false) $valueEnd = strlen($part) - 2; // End of data
                                    $fieldValue = trim(substr($part, $valueStart, $valueEnd - $valueStart), "\r\n");
                                    
                                    $formData[$fieldName] = $fieldValue;
                                }
                            }
                        }
                    }
                    
                    // Handle image upload if present
                    if ($fileData) {
                        // Handle image upload
                        $uploadDir = __DIR__ . '/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $fileName = uniqid() . '_' . basename($fileData['name']);
                        $uploadPath = $uploadDir . $fileName;
                        
                        if (copy($fileData['tmp_name'], $uploadPath)) {
                            // First get existing data
                            $stmt_select = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
                            $stmt_select->execute([$member_id]);
                            $existing_member = $stmt_select->fetch(PDO::FETCH_ASSOC);
                            
                            if (!$existing_member) {
                                http_response_code(404);
                                echo json_encode(['success' => false, 'error' => 'Team member not found']);
                                exit();
                            }
                            
                            // Update with new image - use parsed form data
                            $name = $formData['name'] ?? $existing_member['name'];
                            $position = $formData['position'] ?? $existing_member['position'];
                            $bio = $formData['bio'] ?? $existing_member['bio'];
                            $name_fr = $formData['name_fr'] ?? $existing_member['name_fr'];
                            $position_fr = $formData['position_fr'] ?? $existing_member['position_fr'];
                            $bio_fr = $formData['bio_fr'] ?? $existing_member['bio_fr'];
                            $email = $formData['email'] ?? $existing_member['email'];
                            $phone = $formData['phone'] ?? $existing_member['phone'];
                            $linkedin_url = $formData['linkedin_url'] ?? $existing_member['linkedin_url'];
                            $twitter_url = $formData['twitter_url'] ?? $existing_member['twitter_url'];
                            $status = $formData['status'] ?? $existing_member['status'];
                            
                            $stmt = $pdo->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, name_fr = ?, position_fr = ?, bio_fr = ?, image_path = ?, email = ?, phone = ?, linkedin_url = ?, twitter_url = ?, status = ?, updated_at = NOW() WHERE id = ?");
                            $result = $stmt->execute([
                                $name,
                                $position,
                                $bio,
                                $name_fr,
                                $position_fr,
                                $bio_fr,
                                $fileName,
                                $email,
                                $phone,
                                $linkedin_url,
                                $twitter_url,
                                $status,
                                $member_id
                            ]);
                            
                            if ($result) {
                                echo json_encode([
                                    'success' => true,
                                    'message' => 'Team member updated successfully',
                                    'data' => ['file_path' => $fileName]
                                ]);
                            } else {
                                http_response_code(500);
                                echo json_encode(['success' => false, 'error' => 'Failed to update team member']);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'error' => 'Failed to upload image']);
                        }
                        
                        // Clean up temp file if it exists
                        if ($fileData && isset($fileData['tmp_name']) && file_exists($fileData['tmp_name'])) {
                            unlink($fileData['tmp_name']);
                        }
                    } else {
                        // Update without image - use parsed form data
                        // First get existing data
                        $stmt_select = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
                        $stmt_select->execute([$member_id]);
                        $existing_member = $stmt_select->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$existing_member) {
                            http_response_code(404);
                            echo json_encode(['success' => false, 'error' => 'Team member not found']);
                            exit();
                        }
                        
                        // Get data from parsed form data or use existing values
                        $name = $formData['name'] ?? $existing_member['name'];
                        $position = $formData['position'] ?? $existing_member['position'];
                        $bio = $formData['bio'] ?? $existing_member['bio'];
                        $name_fr = $formData['name_fr'] ?? $existing_member['name_fr'];
                        $position_fr = $formData['position_fr'] ?? $existing_member['position_fr'];
                        $bio_fr = $formData['bio_fr'] ?? $existing_member['bio_fr'];
                        $email = $formData['email'] ?? $existing_member['email'];
                        $phone = $formData['phone'] ?? $existing_member['phone'];
                        $linkedin_url = $formData['linkedin_url'] ?? $existing_member['linkedin_url'];
                        $twitter_url = $formData['twitter_url'] ?? $existing_member['twitter_url'];
                        $status = $formData['status'] ?? $existing_member['status'];
                        
                        $stmt = $pdo->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, name_fr = ?, position_fr = ?, bio_fr = ?, email = ?, phone = ?, linkedin_url = ?, twitter_url = ?, status = ?, updated_at = NOW() WHERE id = ?");
                        $result = $stmt->execute([
                            $name,
                            $position,
                            $bio,
                            $name_fr,
                            $position_fr,
                            $bio_fr,
                            $email,
                            $phone,
                            $linkedin_url,
                            $twitter_url,
                            $status,
                            $member_id
                        ]);
                        
                        if ($result) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Team member updated successfully'
                            ]);
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'error' => 'Failed to update team member']);
                        }
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid multipart request']);
                }
            } else {
                // Update without image
                // Parse PUT data
                parse_str(file_get_contents('php://input'), $_PUT);
                
                // First get existing data
                $stmt_select = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
                $stmt_select->execute([$member_id]);
                $existing_member = $stmt_select->fetch(PDO::FETCH_ASSOC);
                
                if (!$existing_member) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Team member not found']);
                    exit();
                }
                
                // Get data from PUT or use existing values
                $name = $_PUT['name'] ?? $existing_member['name'];
                $position = $_PUT['position'] ?? $existing_member['position'];
                $bio = $_PUT['bio'] ?? $existing_member['bio'];
                $name_fr = $_PUT['name_fr'] ?? $existing_member['name_fr'];
                $position_fr = $_PUT['position_fr'] ?? $existing_member['position_fr'];
                $bio_fr = $_PUT['bio_fr'] ?? $existing_member['bio_fr'];
                $email = $_PUT['email'] ?? $existing_member['email'];
                $phone = $_PUT['phone'] ?? $existing_member['phone'];
                $linkedin_url = $_PUT['linkedin_url'] ?? $existing_member['linkedin_url'];
                $twitter_url = $_PUT['twitter_url'] ?? $existing_member['twitter_url'];
                $status = $_PUT['status'] ?? $existing_member['status'];
                
                $stmt = $pdo->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, name_fr = ?, position_fr = ?, bio_fr = ?, email = ?, phone = ?, linkedin_url = ?, twitter_url = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([
                    $name,
                    $position,
                    $bio,
                    $name_fr,
                    $position_fr,
                    $bio_fr,
                    $email,
                    $phone,
                    $linkedin_url,
                    $twitter_url,
                    $status,
                    $member_id
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Team member updated successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to update team member']);
                }
            }
            break;
            
        case 'DELETE':
            // Delete team member
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $member_id = end($path_parts);
            
            if (!$member_id || !is_numeric($member_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid member ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
            $result = $stmt->execute([$member_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Team member deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to delete team member']);
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