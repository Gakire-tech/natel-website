<?php
// backend/api/models/Gallery.php

class Gallery {
    private $conn;
    private $table_name = "gallery";

    public $id;
    public $title;
    public $description;
    public $title_fr;
    public $description_fr;
    public $image_path;
    public $category;
    public $status;
    public $sort_order;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY sort_order ASC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt;
    }

    public function readByCategory($category) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category = ? AND status = 'active' ORDER BY sort_order ASC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category);
        $stmt->execute();
        return $stmt;
    }
    
    public function readWithLanguage($language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, title_fr as title, description_fr as description FROM " . $this->table_name . " ORDER BY sort_order ASC, created_at DESC";
        } else {
            $query = "SELECT *, title as title, description as description FROM " . $this->table_name . " ORDER BY sort_order ASC, created_at DESC";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function readByCategoryWithLanguage($category, $language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, title_fr as title, description_fr as description FROM " . $this->table_name . " WHERE category = ? AND status = 'active' ORDER BY sort_order ASC, created_at DESC";
        } else {
            $query = "SELECT *, title as title, description as description FROM " . $this->table_name . " WHERE category = ? AND status = 'active' ORDER BY sort_order ASC, created_at DESC";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category);
        $stmt->execute();
        return $stmt;
    }
    
    public function readByIdWithLanguage($id, $language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, title_fr as title, description_fr as description FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        } else {
            $query = "SELECT *, title as title, description as description FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title=:title, description=:description, title_fr=:title_fr, description_fr=:description_fr, image_path=:image_path, 
                      category=:category, status=:status, sort_order=:sort_order";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->title_fr = htmlspecialchars(strip_tags($this->title_fr));
        $this->description_fr = htmlspecialchars(strip_tags($this->description_fr));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":title_fr", $this->title_fr);
        $stmt->bindParam(":description_fr", $this->description_fr);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":sort_order", $this->sort_order);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title=:title, description=:description, title_fr=:title_fr, description_fr=:description_fr, image_path=:image_path,
                      category=:category, status=:status, sort_order=:sort_order,
                      updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->title_fr = htmlspecialchars(strip_tags($this->title_fr));
        $this->description_fr = htmlspecialchars(strip_tags($this->description_fr));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":title_fr", $this->title_fr);
        $stmt->bindParam(":description_fr", $this->description_fr);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":sort_order", $this->sort_order);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function updateSortOrder() {
        $query = "UPDATE " . $this->table_name . " 
                  SET sort_order=:sort_order, updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sort_order", $this->sort_order);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    public function countByCategory($category) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE category = ? AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}