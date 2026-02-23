<?php
// Simple settings API endpoint
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
            // Get all settings
            $language = $_GET['language'] ?? 'en';
            
            if ($language === 'fr') {
                $stmt = $pdo->query("SELECT *, site_title_fr as site_title, footer_text_fr as footer_text, working_hours_fr as working_hours, meta_description_fr as meta_description, meta_keywords_fr as meta_keywords, site_keywords_fr as site_keywords, confirmation_message_fr as confirmation_message FROM settings WHERE id = 1");
            } else {
                $stmt = $pdo->query("SELECT *, site_title as site_title, footer_text as footer_text, working_hours as working_hours, meta_description as meta_description, meta_keywords as meta_keywords, site_keywords as site_keywords, confirmation_message as confirmation_message FROM settings WHERE id = 1");
            }
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$settings) {
                // Initialize with default empty settings
                $default_settings = [
                    'site_title' => 'natel Enterprise',
                    'site_title_fr' => 'natel Enterprise',
                    'logo_path' => null,
                    'address' => '',
                    'phone' => '',
                    'email' => '',
                    'google_maps_url' => '',
                    'footer_text' => '',
                    'footer_text_fr' => '',
                    'facebook_url' => '',
                    'linkedin_url' => '',
                    'whatsapp_url' => '',
                    'instagram_url' => '',
                    'twitter_url' => '',
                    'youtube_url' => '',
                    'tiktok_url' => '',
                    'pinterest_url' => '',
                    'working_hours' => '',
                    'working_hours_fr' => '',
                    'meta_description' => '',
                    'meta_description_fr' => '',
                    'meta_keywords' => '',
                    'meta_keywords_fr' => '',
                    'site_keywords' => '',
                    'site_keywords_fr' => '',
                    'google_analytics_id' => '',
                    'email_sender_address' => '',
                    'email_sender_name' => '',
                    'email_enabled' => '',
                    'confirmation_message' => '',
                    'confirmation_message_fr' => '',
                    'smtp_host' => '',
                    'smtp_port' => '',
                    'smtp_username' => '',
                    'smtp_password' => '',
                    'smtp_encryption' => 'tls',
                    'maintenance_mode' => 0,
                    'contact_notifications' => 1,
                    'newsletter_enabled' => 1
                ];
                
                $fields = implode(', ', array_keys($default_settings));
                $placeholders = ':' . implode(', :', array_keys($default_settings));
                
                $insert_sql = "INSERT INTO settings (id, $fields, created_at) VALUES (1, $placeholders, NOW())";
                $stmt = $pdo->prepare($insert_sql);
                
                foreach ($default_settings as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
                
                $stmt->execute();
                
                if ($language === 'fr') {
                    $stmt = $pdo->query("SELECT *, site_title_fr as site_title, footer_text_fr as footer_text, working_hours_fr as working_hours, meta_description_fr as meta_description, meta_keywords_fr as meta_keywords, site_keywords_fr as site_keywords, confirmation_message_fr as confirmation_message FROM settings WHERE id = 1");
                } else {
                    $stmt = $pdo->query("SELECT *, site_title as site_title, footer_text as footer_text, working_hours as working_hours, meta_description as meta_description, meta_keywords as meta_keywords, site_keywords as site_keywords, confirmation_message as confirmation_message FROM settings WHERE id = 1");
                }
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            // Convert boolean values
            $settings['maintenance_mode'] = (bool)$settings['maintenance_mode'];
            $settings['contact_notifications'] = (bool)$settings['contact_notifications'];
            $settings['newsletter_enabled'] = (bool)$settings['newsletter_enabled'];
            
            echo json_encode([
                'success' => true,
                'data' => $settings
            ]);
            break;
            
        case 'POST':
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $file = $_FILES['logo'];
                $fileName = uniqid() . '_' . basename($file['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    echo json_encode([
                        'success' => true,
                        'file_path' => $fileName,
                        'message' => 'Logo uploaded successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to upload logo']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No logo file provided']);
            }
            break;
            
        case 'PUT':
            // Update settings
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($uri, '/'));
            $setting_id = end($path_parts);
            
            if (!$setting_id || !is_numeric($setting_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid setting ID']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid input data']);
                exit();
            }
            
            // Define allowed fields to prevent SQL injection
            $allowed_fields = [
                'site_title', 'site_title_fr', 'logo_path', 'address', 'phone', 'email',
                'google_maps_url', 'footer_text', 'footer_text_fr', 'facebook_url', 'linkedin_url',
                'whatsapp_url', 'instagram_url', 'twitter_url', 'youtube_url',
                'tiktok_url', 'pinterest_url', 'working_hours', 'working_hours_fr', 'meta_description',
                'meta_description_fr', 'meta_keywords', 'meta_keywords_fr', 'site_keywords', 'site_keywords_fr',
                'google_analytics_id', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                'smtp_encryption', 'maintenance_mode', 'contact_notifications',
                'newsletter_enabled', 'email_sender_address', 'email_sender_name',
                'email_enabled', 'confirmation_message', 'confirmation_message_fr'
            ];
            
            // Filter input to only allowed fields
            $filtered_input = array_intersect_key($input, array_flip($allowed_fields));
            
            // Convert boolean values to integers for database storage
            if (isset($filtered_input['maintenance_mode'])) {
                $filtered_input['maintenance_mode'] = $filtered_input['maintenance_mode'] ? 1 : 0;
            }
            if (isset($filtered_input['contact_notifications'])) {
                $filtered_input['contact_notifications'] = $filtered_input['contact_notifications'] ? 1 : 0;
            }
            if (isset($filtered_input['newsletter_enabled'])) {
                $filtered_input['newsletter_enabled'] = $filtered_input['newsletter_enabled'] ? 1 : 0;
            }
            
            // Build dynamic update query
            $fields = [];
            $values = [];
            
            foreach ($filtered_input as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
            
            $values[] = $setting_id; // For WHERE clause
            
            $sql = "UPDATE settings SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Settings updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update settings']);
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