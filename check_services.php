<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=natel_enterprise;charset=utf8', 'root', '');
    $stmt = $pdo->query('SELECT id, title, icon_path FROM services');
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Services in database:\n";
    foreach($results as $row) {
        echo 'ID: ' . $row['id'] . ', Title: ' . $row['title'] . ', Icon Path: ' . $row['icon_path'] . "\n";
    }
    
    if (empty($results)) {
        echo "No services found in the database.\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>