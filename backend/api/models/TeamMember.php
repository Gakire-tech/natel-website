<?php
// backend/api/models/TeamMember.php

class TeamMember {
    private $conn;
    private $table_name = "team_members";

    public $id;
    public $name;
    public $position;
    public $bio;
    public $name_fr;
    public $position_fr;
    public $bio_fr;
    public $image_path;
    public $email;
    public $phone;
    public $linkedin_url;
    public $twitter_url;
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

    public function readActive() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readActiveWithLanguage($language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, position_fr as position, bio_fr as bio FROM " . $this->table_name . " WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC";
        } else {
            $query = "SELECT *, name as name, position as position, bio as bio FROM " . $this->table_name . " WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readWithLanguage($language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, position_fr as position, bio_fr as bio FROM " . $this->table_name . " ORDER BY sort_order ASC, created_at DESC";
        } else {
            $query = "SELECT *, name as name, position as position, bio as bio FROM " . $this->table_name . " ORDER BY sort_order ASC, created_at DESC";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByIdWithLanguage($id, $language = 'en') {
        if ($language === 'fr') {
            $query = "SELECT *, name_fr as name, position_fr as position, bio_fr as bio FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        } else {
            $query = "SELECT *, name as name, position as position, bio as bio FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, position=:position, bio=:bio, 
                      name_fr=:name_fr, position_fr=:position_fr, bio_fr=:bio_fr,
                      image_path=:image_path, email=:email, phone=:phone,
                      linkedin_url=:linkedin_url, twitter_url=:twitter_url,
                      status=:status, sort_order=:sort_order";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->name_fr = htmlspecialchars(strip_tags($this->name_fr));
        $this->position_fr = htmlspecialchars(strip_tags($this->position_fr));
        $this->bio_fr = htmlspecialchars(strip_tags($this->bio_fr));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->linkedin_url = htmlspecialchars(strip_tags($this->linkedin_url));
        $this->twitter_url = htmlspecialchars(strip_tags($this->twitter_url));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":bio", $this->bio);
        $stmt->bindParam(":name_fr", $this->name_fr);
        $stmt->bindParam(":position_fr", $this->position_fr);
        $stmt->bindParam(":bio_fr", $this->bio_fr);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":linkedin_url", $this->linkedin_url);
        $stmt->bindParam(":twitter_url", $this->twitter_url);
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
                  SET name=:name, position=:position, bio=:bio,
                      name_fr=:name_fr, position_fr=:position_fr, bio_fr=:bio_fr,
                      image_path=:image_path, email=:email, phone=:phone,
                      linkedin_url=:linkedin_url, twitter_url=:twitter_url,
                      status=:status, sort_order=:sort_order,
                      updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->position = htmlspecialchars(strip_tags($this->position));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->name_fr = htmlspecialchars(strip_tags($this->name_fr));
        $this->position_fr = htmlspecialchars(strip_tags($this->position_fr));
        $this->bio_fr = htmlspecialchars(strip_tags($this->bio_fr));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->linkedin_url = htmlspecialchars(strip_tags($this->linkedin_url));
        $this->twitter_url = htmlspecialchars(strip_tags($this->twitter_url));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":bio", $this->bio);
        $stmt->bindParam(":name_fr", $this->name_fr);
        $stmt->bindParam(":position_fr", $this->position_fr);
        $stmt->bindParam(":bio_fr", $this->bio_fr);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":linkedin_url", $this->linkedin_url);
        $stmt->bindParam(":twitter_url", $this->twitter_url);
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

    public function countActive() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}