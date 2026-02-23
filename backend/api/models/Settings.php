<?php
// backend/api/models/Settings.php

require_once 'Database.php';

class Settings extends BaseModel {
    protected $table_name = 'settings';

    public $id;
    public $site_title;
    public $logo_path;
    public $address;
    public $phone;
    public $email;
    public $google_maps_url;
    public $footer_text;
    public $facebook_url;
    public $linkedin_url;
    public $whatsapp_url;
    public $instagram_url;
    public $twitter_url;
    public $youtube_url;
    public $working_hours;
    public $meta_description;
    public $meta_keywords;
    public $updated_at;
    public $site_keywords;
    public $google_analytics_id;
    public $smtp_host;
    public $smtp_port;
    public $smtp_username;
    public $smtp_password;
    public $smtp_encryption;
    public $maintenance_mode;
    public $contact_notifications;
    public $newsletter_enabled;
    public $confirmation_message;
    public $email_sender_address;
    public $email_sender_name;
    public $email_enabled;
    public $site_title_fr;
    public $address_fr;
    public $footer_text_fr;
    public $working_hours_fr;
    public $meta_description_fr;
    public $meta_keywords_fr;
    public $site_keywords_fr;
    public $confirmation_message_fr;

    public function __construct() {
        parent::__construct();
    }

    // Get all settings
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all settings with language support
    public function getAllWithLanguage($language = 'en') {
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
            $result['display_site_title'] = !empty(trim($result['site_title_fr'])) ? $result['site_title_fr'] : $result['site_title'];
            $result['display_footer_text'] = !empty(trim($result['footer_text_fr'])) ? $result['footer_text_fr'] : $result['footer_text'];
            $result['display_working_hours'] = !empty(trim($result['working_hours_fr'])) ? $result['working_hours_fr'] : $result['working_hours'];
            $result['display_meta_description'] = !empty(trim($result['meta_description_fr'])) ? $result['meta_description_fr'] : $result['meta_description'];
            $result['display_meta_keywords'] = !empty(trim($result['meta_keywords_fr'])) ? $result['meta_keywords_fr'] : $result['meta_keywords'];
            $result['display_site_keywords'] = !empty(trim($result['site_keywords_fr'])) ? $result['site_keywords_fr'] : $result['site_keywords'];
            $result['display_confirmation_message'] = !empty(trim($result['confirmation_message_fr'])) ? $result['confirmation_message_fr'] : $result['confirmation_message'];
        } else {
            $result['display_site_title'] = !empty(trim($result['site_title'])) ? $result['site_title'] : $result['site_title_fr'];
            $result['display_footer_text'] = !empty(trim($result['footer_text'])) ? $result['footer_text'] : $result['footer_text_fr'];
            $result['display_working_hours'] = !empty(trim($result['working_hours'])) ? $result['working_hours'] : $result['working_hours_fr'];
            $result['display_meta_description'] = !empty(trim($result['meta_description'])) ? $result['meta_description'] : $result['meta_description_fr'];
            $result['display_meta_keywords'] = !empty(trim($result['meta_keywords'])) ? $result['meta_keywords'] : $result['meta_keywords_fr'];
            $result['display_site_keywords'] = !empty(trim($result['site_keywords'])) ? $result['site_keywords'] : $result['site_keywords_fr'];
            $result['display_confirmation_message'] = !empty(trim($result['confirmation_message'])) ? $result['confirmation_message'] : $result['confirmation_message_fr'];
        }
        
        return $result;
    }

    // Update settings
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET site_title=:site_title, 
                      site_title_fr=:site_title_fr,
                      address=:address, 
                      address_fr=:address_fr,
                      phone=:phone, 
                      email=:email, 
                      google_maps_url=:google_maps_url, 
                      footer_text=:footer_text, 
                      footer_text_fr=:footer_text_fr,
                      facebook_url=:facebook_url, 
                      linkedin_url=:linkedin_url, 
                      whatsapp_url=:whatsapp_url,
                      instagram_url=:instagram_url,
                      twitter_url=:twitter_url,
                      youtube_url=:youtube_url,
                      working_hours=:working_hours,
                      working_hours_fr=:working_hours_fr,
                      meta_description=:meta_description,
                      meta_description_fr=:meta_description_fr,
                      meta_keywords=:meta_keywords,
                      meta_keywords_fr=:meta_keywords_fr,
                      site_keywords=:site_keywords,
                      site_keywords_fr=:site_keywords_fr,
                      google_analytics_id=:google_analytics_id,
                      smtp_host=:smtp_host,
                      smtp_port=:smtp_port,
                      smtp_username=:smtp_username,
                      smtp_password=:smtp_password,
                      smtp_encryption=:smtp_encryption,
                      maintenance_mode=:maintenance_mode,
                      contact_notifications=:contact_notifications,
                      newsletter_enabled=:newsletter_enabled,
                      confirmation_message=:confirmation_message,
                      confirmation_message_fr=:confirmation_message_fr,
                      email_sender_address=:email_sender_address,
                      email_sender_name=:email_sender_name,
                      email_enabled=:email_enabled";

        // Only update logo_path if provided
        if (!empty($this->logo_path)) {
            $query .= ", logo_path=:logo_path";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->site_title = $this->sanitizeInput($this->site_title);
        $this->site_title_fr = $this->sanitizeInput($this->site_title_fr);
        $this->address = $this->sanitizeInput($this->address);
        $this->address_fr = $this->sanitizeInput($this->address_fr);
        $this->phone = $this->sanitizeInput($this->phone);
        $this->email = $this->sanitizeEmail($this->email);
        $this->google_maps_url = $this->sanitizeInput($this->google_maps_url);
        $this->footer_text = $this->sanitizeInput($this->footer_text);
        $this->footer_text_fr = $this->sanitizeInput($this->footer_text_fr);
        $this->facebook_url = $this->sanitizeInput($this->facebook_url);
        $this->linkedin_url = $this->sanitizeInput($this->linkedin_url);
        $this->whatsapp_url = $this->sanitizeInput($this->whatsapp_url);
        $this->instagram_url = $this->sanitizeInput($this->instagram_url);
        $this->twitter_url = $this->sanitizeInput($this->twitter_url);
        $this->youtube_url = $this->sanitizeInput($this->youtube_url);
        $this->working_hours = $this->sanitizeInput($this->working_hours);
        $this->working_hours_fr = $this->sanitizeInput($this->working_hours_fr);
        $this->meta_description = $this->sanitizeInput($this->meta_description);
        $this->meta_description_fr = $this->sanitizeInput($this->meta_description_fr);
        $this->meta_keywords = $this->sanitizeInput($this->meta_keywords);
        $this->meta_keywords_fr = $this->sanitizeInput($this->meta_keywords_fr);
        $this->site_keywords = $this->sanitizeInput($this->site_keywords);
        $this->site_keywords_fr = $this->sanitizeInput($this->site_keywords_fr);
        $this->google_analytics_id = $this->sanitizeInput($this->google_analytics_id);
        $this->smtp_host = $this->sanitizeInput($this->smtp_host);
        $this->smtp_port = $this->sanitizeInput($this->smtp_port);
        $this->smtp_username = $this->sanitizeInput($this->smtp_username);
        $this->smtp_password = $this->sanitizeInput($this->smtp_password);
        $this->smtp_encryption = $this->sanitizeInput($this->smtp_encryption);
        $this->maintenance_mode = $this->sanitizeInput($this->maintenance_mode);
        $this->contact_notifications = $this->sanitizeInput($this->contact_notifications);
        $this->newsletter_enabled = $this->sanitizeInput($this->newsletter_enabled);
        $this->confirmation_message = $this->sanitizeInput($this->confirmation_message);
        $this->confirmation_message_fr = $this->sanitizeInput($this->confirmation_message_fr);
        $this->email_sender_address = $this->sanitizeInput($this->email_sender_address);
        $this->email_sender_name = $this->sanitizeInput($this->email_sender_name);
        $this->email_enabled = $this->sanitizeInput($this->email_enabled);
        $this->id = $this->sanitizeInput($this->id);

        // Bind values
        $stmt->bindParam(':site_title', $this->site_title);
        $stmt->bindParam(':site_title_fr', $this->site_title_fr);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':address_fr', $this->address_fr);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':google_maps_url', $this->google_maps_url);
        $stmt->bindParam(':footer_text', $this->footer_text);
        $stmt->bindParam(':footer_text_fr', $this->footer_text_fr);
        $stmt->bindParam(':facebook_url', $this->facebook_url);
        $stmt->bindParam(':linkedin_url', $this->linkedin_url);
        $stmt->bindParam(':whatsapp_url', $this->whatsapp_url);
        $stmt->bindParam(':instagram_url', $this->instagram_url);
        $stmt->bindParam(':twitter_url', $this->twitter_url);
        $stmt->bindParam(':youtube_url', $this->youtube_url);
        $stmt->bindParam(':working_hours', $this->working_hours);
        $stmt->bindParam(':working_hours_fr', $this->working_hours_fr);
        $stmt->bindParam(':meta_description', $this->meta_description);
        $stmt->bindParam(':meta_description_fr', $this->meta_description_fr);
        $stmt->bindParam(':meta_keywords', $this->meta_keywords);
        $stmt->bindParam(':meta_keywords_fr', $this->meta_keywords_fr);
        $stmt->bindParam(':site_keywords', $this->site_keywords);
        $stmt->bindParam(':site_keywords_fr', $this->site_keywords_fr);
        $stmt->bindParam(':google_analytics_id', $this->google_analytics_id);
        $stmt->bindParam(':smtp_host', $this->smtp_host);
        $stmt->bindParam(':smtp_port', $this->smtp_port);
        $stmt->bindParam(':smtp_username', $this->smtp_username);
        $stmt->bindParam(':smtp_password', $this->smtp_password);
        $stmt->bindParam(':smtp_encryption', $this->smtp_encryption);
        $stmt->bindParam(':maintenance_mode', $this->maintenance_mode);
        $stmt->bindParam(':contact_notifications', $this->contact_notifications);
        $stmt->bindParam(':newsletter_enabled', $this->newsletter_enabled);
        $stmt->bindParam(':confirmation_message', $this->confirmation_message);
        $stmt->bindParam(':confirmation_message_fr', $this->confirmation_message_fr);
        $stmt->bindParam(':email_sender_address', $this->email_sender_address);
        $stmt->bindParam(':email_sender_name', $this->email_sender_name);
        $stmt->bindParam(':email_enabled', $this->email_enabled);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->logo_path)) {
            $stmt->bindParam(':logo_path', $this->logo_path);
        }

        return $stmt->execute();
    }
}