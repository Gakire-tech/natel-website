<?php
// Simple auth check endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Check for token in GET parameter as fallback
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    
    if (!$token) {
        // Try to get from Authorization header
        $headers = apache_request_headers();
        $auth_header = $headers['Authorization'] ?? '';
        
        if ($auth_header && preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            $token = $matches[1];
        }
    }
    
    if ($token) {
        // Decode token
        $decoded = json_decode(base64_decode($token), true);
        
        if ($decoded && isset($decoded['user_id']) && isset($decoded['email']) && isset($decoded['exp'])) {
            echo json_encode([
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'id' => $decoded['user_id'],
                    'email' => $decoded['email'],
                    'role' => $decoded['role'] ?? 'admin'
                ],
                'valid' => $decoded['exp'] > time(),
                'expires_at' => date('Y-m-d H:i:s', $decoded['exp'])
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'authenticated' => false,
                'error' => 'Invalid token structure'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'authenticated' => false,
            'error' => 'No token provided'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'authenticated' => false,
        'error' => $e->getMessage()
    ]);
}
?>