<?php
// backend/api/models/Database.php

require_once __DIR__ . '/../config/core.php';
require_once __DIR__ . '/../config/database.php';

class BaseModel {
    protected $conn;
    protected $table_name;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Common methods will be added here
    public function sanitizeInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    public function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}