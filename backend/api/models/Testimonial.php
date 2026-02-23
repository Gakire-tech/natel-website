<?php
// backend/api/models/Testimonial.php

class Testimonial {
    private $conn;
    private $table_name = "testimonials";

    public $id;
    public $name;
    public $position;
    public $company;
    public $content;
    public $name_fr;
    public $position_fr;
    public $content_fr;
    public $rating;
    public $image_path;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
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

    public function readActive() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function readActiveWithLanguage($language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, position_fr as position, content_fr as content FROM " . $this->table_name . " WHERE status = 'active' ORDER BY created_at DESC";
        } else {
            $query = "SELECT *, name as name, position as position, content as content FROM " . $this->table_name . " WHERE status = 'active' ORDER BY created_at DESC";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function readWithLanguage($language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, position_fr as position, content_fr as content FROM " . $this->table_name . " ORDER BY created_at DESC";
        } else {
            $query = "SELECT *, name as name, position as position, content as content FROM " . $this->table_name . " ORDER BY created_at DESC";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function readByIdWithLanguage($id, $language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, position_fr as position, content_fr as content FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        } else {
            $query = "SELECT *, name as name, position as position, content as content FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, position=:position, company=:company, 
                      content=:content, name_fr=:name_fr, position_fr=:position_fr, content_fr=:content_fr, rating=:rating, image_path=:image_path, status=:status";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->name_fr = htmlspecialchars(strip_tags($this->name_fr));
        $this->position_fr = htmlspecialchars(strip_tags($this->position_fr));
        $this->content_fr = htmlspecialchars(strip_tags($this->content_fr));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":company", $this->company);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":name_fr", $this->name_fr);
        $stmt->bindParam(":position_fr", $this->position_fr);
        $stmt->bindParam(":content_fr", $this->content_fr);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, position=:position, company=:company,
                      content=:content, name_fr=:name_fr, position_fr=:position_fr, content_fr=:content_fr, rating=:rating, image_path=:image_path,
                      status=:status, updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->name_fr = htmlspecialchars(strip_tags($this->name_fr));
        $this->position_fr = htmlspecialchars(strip_tags($this->position_fr));
        $this->content_fr = htmlspecialchars(strip_tags($this->content_fr));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":company", $this->company);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":name_fr", $this->name_fr);
        $stmt->bindParam(":position_fr", $this->position_fr);
        $stmt->bindParam(":content_fr", $this->content_fr);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    public function countActive() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}