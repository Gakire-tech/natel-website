<?php
// backend/api/models/Service.php

require_once 'Database.php';

class Service extends BaseModel {
    protected $table_name = 'services';

    public $id;
    public $title;
    public $description;
    public $title_fr;
    public $description_fr;
    public $icon_path;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct() {
        parent::__construct();
    }

    // Create service
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title=:title, description=:description, title_fr=:title_fr, description_fr=:description_fr, icon_path=:icon_path, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = $this->sanitizeInput($this->title);
        $this->description = $this->sanitizeInput($this->description);
        $this->title_fr = $this->sanitizeInput($this->title_fr);
        $this->description_fr = $this->sanitizeInput($this->description_fr);
        $this->icon_path = $this->sanitizeInput($this->icon_path);
        $this->status = $this->sanitizeInput($this->status);

        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':title_fr', $this->title_fr);
        $stmt->bindParam(':description_fr', $this->description_fr);
        $stmt->bindParam(':icon_path', $this->icon_path);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get all services
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY created_at DESC LIMIT ?, ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all services (for admin)
    public function getAllForAdmin($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?, ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all services with language support
    public function getAllWithLanguage($limit = 100, $offset = 0, $language = 'en') {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY created_at DESC LIMIT ?, ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process each result to add language-specific display fields based on language preference with fallback
        foreach ($results as &$result) {
            if ($language === 'fr') {
                $result['display_title'] = !empty(trim($result['title_fr'])) ? $result['title_fr'] : $result['title'];
                $result['display_description'] = !empty(trim($result['description_fr'])) ? $result['description_fr'] : $result['description'];
            } else {
                $result['display_title'] = !empty(trim($result['title'])) ? $result['title'] : $result['title_fr'];
                $result['display_description'] = !empty(trim($result['description'])) ? $result['description'] : $result['description_fr'];
            }
        }
        
        return $results;
    }

    // Get service by ID with language support
    public function getByIdWithLanguage($id, $language = 'en') {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Convert any null values to empty strings to prevent React warnings
        foreach ($result as $key => $value) {
            if (is_null($value)) {
                $result[$key] = '';
            }
        }
        
        // Add language-specific display fields based on language preference with fallback
        if ($language === 'fr') {
            $result['display_title'] = !empty(trim($result['title_fr'])) ? $result['title_fr'] : $result['title'];
            $result['display_description'] = !empty(trim($result['description_fr'])) ? $result['description_fr'] : $result['description'];
        } else {
            $result['display_title'] = !empty(trim($result['title'])) ? $result['title'] : $result['title_fr'];
            $result['display_description'] = !empty(trim($result['description'])) ? $result['description'] : $result['description_fr'];
        }
        
        return $result;
    }

    // Get service by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update service
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title=:title, description=:description, title_fr=:title_fr, description_fr=:description_fr, status=:status";

        if (!empty($this->icon_path)) {
            $query .= ", icon_path=:icon_path";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = $this->sanitizeInput($this->title);
        $this->description = $this->sanitizeInput($this->description);
        $this->title_fr = $this->sanitizeInput($this->title_fr);
        $this->description_fr = $this->sanitizeInput($this->description_fr);
        $this->status = $this->sanitizeInput($this->status);
        $this->id = $this->sanitizeInput($this->id);

        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':title_fr', $this->title_fr);
        $stmt->bindParam(':description_fr', $this->description_fr);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->icon_path)) {
            $this->icon_path = $this->sanitizeInput($this->icon_path);
            $stmt->bindParam(':icon_path', $this->icon_path);
        }

        return $stmt->execute();
    }

    // Delete service
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    // Get total count of services
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}