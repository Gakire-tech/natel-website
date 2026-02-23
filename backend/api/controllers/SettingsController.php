<?php
// backend/api/controllers/SettingsController.php

require_once 'models/Settings.php';

class SettingsController {
    
    public function getSettings() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $settings = new Settings();
        $result = $settings->getAll();
        
        if ($result) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Settings not found'
            ]);
        }
    }
    
    public function updateSettings() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'PUT') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Verify JWT token
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$auth_header || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. No token provided.']);
            return;
        }
        
        $token = $matches[1];
        $decoded = JwtHandler::validateToken($token);
        
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Access denied. Invalid token.']);
            return;
        }
        
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided']);
            return;
        }
        
        $settings = new Settings();
        $settings->id = 1; // We only have one settings record
        $settings->site_title = $input['site_title'] ?? $settings->site_title;
        $settings->site_title_fr = $input['site_title_fr'] ?? $settings->site_title_fr;
        $settings->address = $input['address'] ?? $settings->address;
        $settings->address_fr = $input['address_fr'] ?? $settings->address_fr;
        $settings->phone = $input['phone'] ?? $settings->phone;
        $settings->email = $input['email'] ?? $settings->email;
        $settings->google_maps_url = $input['google_maps_url'] ?? $settings->google_maps_url;
        $settings->footer_text = $input['footer_text'] ?? $settings->footer_text;
        $settings->footer_text_fr = $input['footer_text_fr'] ?? $settings->footer_text_fr;
        $settings->facebook_url = $input['facebook_url'] ?? $settings->facebook_url;
        $settings->linkedin_url = $input['linkedin_url'] ?? $settings->linkedin_url;
        $settings->whatsapp_url = $input['whatsapp_url'] ?? $settings->whatsapp_url;
        $settings->instagram_url = $input['instagram_url'] ?? $settings->instagram_url;
        $settings->twitter_url = $input['twitter_url'] ?? $settings->twitter_url;
        $settings->youtube_url = $input['youtube_url'] ?? $settings->youtube_url;
        $settings->working_hours = $input['working_hours'] ?? $settings->working_hours;
        $settings->working_hours_fr = $input['working_hours_fr'] ?? $settings->working_hours_fr;
        $settings->meta_description = $input['meta_description'] ?? $settings->meta_description;
        $settings->meta_description_fr = $input['meta_description_fr'] ?? $settings->meta_description_fr;
        $settings->meta_keywords = $input['meta_keywords'] ?? $settings->meta_keywords;
        $settings->meta_keywords_fr = $input['meta_keywords_fr'] ?? $settings->meta_keywords_fr;
        $settings->site_keywords = $input['site_keywords'] ?? $settings->site_keywords;
        $settings->site_keywords_fr = $input['site_keywords_fr'] ?? $settings->site_keywords_fr;
        $settings->google_analytics_id = $input['google_analytics_id'] ?? $settings->google_analytics_id;
        $settings->smtp_host = $input['smtp_host'] ?? $settings->smtp_host;
        $settings->smtp_port = $input['smtp_port'] ?? $settings->smtp_port;
        $settings->smtp_username = $input['smtp_username'] ?? $settings->smtp_username;
        $settings->smtp_password = $input['smtp_password'] ?? $settings->smtp_password;
        $settings->smtp_encryption = $input['smtp_encryption'] ?? $settings->smtp_encryption;
        $settings->maintenance_mode = $input['maintenance_mode'] ?? $settings->maintenance_mode;
        $settings->contact_notifications = $input['contact_notifications'] ?? $settings->contact_notifications;
        $settings->newsletter_enabled = $input['newsletter_enabled'] ?? $settings->newsletter_enabled;
        $settings->confirmation_message = $input['confirmation_message'] ?? $settings->confirmation_message;
        $settings->confirmation_message_fr = $input['confirmation_message_fr'] ?? $settings->confirmation_message_fr;
        $settings->email_sender_address = $input['email_sender_address'] ?? $settings->email_sender_address;
        $settings->email_sender_name = $input['email_sender_name'] ?? $settings->email_sender_name;
        $settings->email_enabled = $input['email_enabled'] ?? $settings->email_enabled;
        
        // Handle file upload if logo is being updated
        if (isset($_FILES['logo'])) {
            $uploadHandler = new UploadHandler();
            $logoPath = $uploadHandler->uploadFile($_FILES['logo'], 'logos/');
            
            if ($logoPath) {
                $settings->logo_path = $logoPath;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Logo upload failed', 'details' => $uploadHandler->getErrors()]);
                return;
            }
        }
        
        if ($settings->update()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update settings'
            ]);
        }
    }
}