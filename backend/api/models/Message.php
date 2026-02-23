<?php
// backend/api/models/Message.php

require_once 'Database.php';

class Message extends BaseModel {
    protected $table_name = 'messages';

    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct() {
        parent::__construct();
    }

    // Create message
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, email=:email, subject=:subject, message=:message, status='new'";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = $this->sanitizeInput($this->name);
        $this->email = $this->sanitizeEmail($this->email);
        $this->subject = $this->sanitizeInput($this->subject);
        $this->message = $this->sanitizeInput($this->message);

        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':subject', $this->subject);
        $stmt->bindParam(':message', $this->message);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get all messages
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?, ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get message by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update message status
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status=:status WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->status = $this->sanitizeInput($this->status);
        $this->id = $this->sanitizeInput($this->id);

        // Bind values
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete message
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    // Get total count of messages
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    // Get count of new messages
    public function getNewCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'new'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}