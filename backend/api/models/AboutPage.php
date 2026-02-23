<?php
// backend/api/models/AboutPage.php

require_once 'Database.php';

class AboutPage extends BaseModel {
    protected $table_name = 'about_page';

    public $id;
    public $main_content;
    public $image_path;
    public $mission;
    public $vision;
    public $values_content;
    public $main_content_fr;
    public $mission_fr;
    public $vision_fr;
    public $values_content_fr;
    public $updated_at;

    public function __construct() {
        parent::__construct();
    }

    // Get about page content
    public function getAboutPage() {
        $query = "SELECT * FROM " . $this->table_name . " LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get about page content with language support
    public function getAboutPageWithLanguage($language = 'en') {
        $query = "SELECT * FROM " . $this->table_name . " LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
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
            $result['display_main_content'] = !empty(trim($result['main_content_fr'])) ? $result['main_content_fr'] : $result['main_content'];
            $result['display_mission'] = !empty(trim($result['mission_fr'])) ? $result['mission_fr'] : $result['mission'];
            $result['display_vision'] = !empty(trim($result['vision_fr'])) ? $result['vision_fr'] : $result['vision'];
            $result['display_values_content'] = !empty(trim($result['values_content_fr'])) ? $result['values_content_fr'] : $result['values_content'];
        } else {
            $result['display_main_content'] = !empty(trim($result['main_content'])) ? $result['main_content'] : $result['main_content_fr'];
            $result['display_mission'] = !empty(trim($result['mission'])) ? $result['mission'] : $result['mission_fr'];
            $result['display_vision'] = !empty(trim($result['vision'])) ? $result['vision'] : $result['vision_fr'];
            $result['display_values_content'] = !empty(trim($result['values_content'])) ? $result['values_content'] : $result['values_content_fr'];
        }
        
        return $result;
    }

    // Update about page content
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET main_content=:main_content, 
                      mission=:mission, 
                      vision=:vision, 
                      values_content=:values_content,
                      main_content_fr=:main_content_fr,
                      mission_fr=:mission_fr,
                      vision_fr=:vision_fr,
                      values_content_fr=:values_content_fr";

        // Only update image_path if provided
        if (!empty($this->image_path)) {
            $query .= ", image_path=:image_path";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->main_content = $this->sanitizeInput($this->main_content);
        $this->mission = $this->sanitizeInput($this->mission);
        $this->vision = $this->sanitizeInput($this->vision);
        $this->values_content = $this->sanitizeInput($this->values_content);
        $this->main_content_fr = $this->sanitizeInput($this->main_content_fr);
        $this->mission_fr = $this->sanitizeInput($this->mission_fr);
        $this->vision_fr = $this->sanitizeInput($this->vision_fr);
        $this->values_content_fr = $this->sanitizeInput($this->values_content_fr);
        $this->id = $this->sanitizeInput($this->id);

        // Bind values
        $stmt->bindParam(':main_content', $this->main_content);
        $stmt->bindParam(':mission', $this->mission);
        $stmt->bindParam(':vision', $this->vision);
        $stmt->bindParam(':values_content', $this->values_content);
        $stmt->bindParam(':main_content_fr', $this->main_content_fr);
        $stmt->bindParam(':mission_fr', $this->mission_fr);
        $stmt->bindParam(':vision_fr', $this->vision_fr);
        $stmt->bindParam(':values_content_fr', $this->values_content_fr);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->image_path)) {
            $stmt->bindParam(':image_path', $this->image_path);
        }

        return $stmt->execute();
    }
}