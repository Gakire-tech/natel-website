<?php
// backend/api/models/Project.php

require_once 'Database.php';

class Project extends BaseModel {
    protected $table_name = 'projects';

    public $id;
    public $name;
    public $description;
    public $name_fr;
    public $description_fr;
    public $image_path;
    public $technologies;
    public $client;
    public $status;
    public $project_date;
    public $created_at;
    public $updated_at;

    public function __construct() {
        parent::__construct();
    }

    // Create project
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, description=:description, name_fr=:name_fr, description_fr=:description_fr, image_path=:image_path, 
                      technologies=:technologies, client=:client, status=:status, project_date=:project_date";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = $this->sanitizeInput($this->name);
        $this->description = $this->sanitizeInput($this->description);
        $this->name_fr = $this->sanitizeInput($this->name_fr);
        $this->description_fr = $this->sanitizeInput($this->description_fr);
        $this->image_path = $this->sanitizeInput($this->image_path);
        $this->technologies = $this->sanitizeInput($this->technologies);
        $this->client = $this->sanitizeInput($this->client);
        $this->status = $this->sanitizeInput($this->status);
        $this->project_date = $this->sanitizeInput($this->project_date);

        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':name_fr', $this->name_fr);
        $stmt->bindParam(':description_fr', $this->description_fr);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':technologies', $this->technologies);
        $stmt->bindParam(':client', $this->client);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':project_date', $this->project_date);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get all projects
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' OR status = 'completed' ORDER BY created_at DESC LIMIT ?, ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all projects (for admin)
    public function getAllForAdmin($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?, ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all projects with language support
    public function getAllWithLanguage($limit = 100, $offset = 0, $language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, description_fr as description FROM " . $this->table_name . " WHERE status = 'active' OR status = 'completed' ORDER BY created_at DESC LIMIT ?, ?";
        } else {
            $query = "SELECT *, name as name, description as description FROM " . $this->table_name . " WHERE status = 'active' OR status = 'completed' ORDER BY created_at DESC LIMIT ?, ?";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get project by ID with language support
    public function getByIdWithLanguage($id, $language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, description_fr as description FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        } else {
            $query = "SELECT *, name as name, description as description FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get project by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update project
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, description=:description, name_fr=:name_fr, description_fr=:description_fr, client=:client, 
                      status=:status, project_date=:project_date";

        if (!empty($this->image_path)) {
            $query .= ", image_path=:image_path";
        }

        if (!empty($this->technologies)) {
            $query .= ", technologies=:technologies";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = $this->sanitizeInput($this->name);
        $this->description = $this->sanitizeInput($this->description);
        $this->name_fr = $this->sanitizeInput($this->name_fr);
        $this->description_fr = $this->sanitizeInput($this->description_fr);
        $this->client = $this->sanitizeInput($this->client);
        $this->status = $this->sanitizeInput($this->status);
        $this->project_date = $this->sanitizeInput($this->project_date);
        $this->id = $this->sanitizeInput($this->id);

        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':name_fr', $this->name_fr);
        $stmt->bindParam(':description_fr', $this->description_fr);
        $stmt->bindParam(':client', $this->client);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':project_date', $this->project_date);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->image_path)) {
            $this->image_path = $this->sanitizeInput($this->image_path);
            $stmt->bindParam(':image_path', $this->image_path);
        }

        if (!empty($this->technologies)) {
            $this->technologies = $this->sanitizeInput($this->technologies);
            $stmt->bindParam(':technologies', $this->technologies);
        }

        return $stmt->execute();
    }

    // Delete project
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    // Get total count of projects
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}