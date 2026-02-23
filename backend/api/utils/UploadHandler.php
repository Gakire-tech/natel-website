<?php
// backend/api/utils/UploadHandler.php

class UploadHandler {
    
    private $uploadPath;
    private $maxFileSize;
    private $allowedTypes;
    private $errors = [];

    public function __construct() {
        $this->uploadPath = UPLOAD_PATH;
        $this->maxFileSize = MAX_FILE_SIZE;
        $this->allowedTypes = explode(',', ALLOWED_TYPES);
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    public function uploadFile($file, $targetDir = '') {
        // Check if file was uploaded without errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'File upload error: ' . $file['error'];
            return false;
        }

        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = 'File size exceeds maximum allowed size (' . ($this->maxFileSize / 1024 / 1024) . 'MB)';
            return false;
        }

        // Get file extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($fileExtension, $this->allowedTypes)) {
            $this->errors[] = 'File type not allowed. Allowed types: ' . ALLOWED_TYPES;
            return false;
        }

        // Generate unique filename
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        
        // Create target directory if needed
        $fullPath = $this->uploadPath . $targetDir;
        if (!empty($targetDir) && !file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        // Move uploaded file
        $destination = $fullPath . $fileName;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $targetDir . $fileName;
        } else {
            $this->errors[] = 'Failed to move uploaded file';
            return false;
        }
    }

    public function deleteFile($filePath) {
        $fullPath = $this->uploadPath . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return true; // File doesn't exist, so consider it deleted
    }

    public function getErrors() {
        return $this->errors;
    }
}