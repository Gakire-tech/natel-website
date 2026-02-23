<?php
// Test script to check what the services API returns
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Database connection
    $host = 'localhost';
    $dbname = 'natel_enterprise';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all services
    $language = 'en';
    
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
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>