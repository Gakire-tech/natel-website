<?php
// backend/api/utils/EmailSender.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';  // Load Composer's autoloader from backend root

class EmailSender {
    
    /**
     * Send a confirmation email to the visitor
     */
    public static function sendConfirmationEmail($toEmail, $toName, $subject, $confirmationMessage, $additionalInfo = []) {
        $mail = new PHPMailer(true);
        
        try {
            // Log email attempt
            error_log("Attempting to send confirmation email to: $toEmail, subject: $subject");
            
            // Get SMTP configuration from database settings
            $smtpConfig = self::getSmtpConfig();
            
            if ($smtpConfig['email_enabled']) {
                // Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = $smtpConfig['smtp_host'];              // Set the SMTP server
                $mail->SMTPAuth   = !empty($smtpConfig['smtp_username']);   // Enable SMTP authentication if credentials exist
                $mail->Username   = $smtpConfig['smtp_username'];          // SMTP username
                $mail->Password   = $smtpConfig['smtp_password'];          // SMTP password
                $mail->SMTPSecure = $smtpConfig['smtp_encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS or SSL encryption
                $mail->Port       = $smtpConfig['smtp_port'];              // TCP port to connect to
                
                // Use the configured sender email and name
                $senderEmail = $smtpConfig['email_sender_address'] ?: $smtpConfig['smtp_username'];
                $senderName = $smtpConfig['email_sender_name'] ?: 'Infinity Enterprise';
                $mail->setFrom($senderEmail, $senderName);
                
                error_log("Using SMTP configuration: host={$smtpConfig['smtp_host']}, port={$smtpConfig['smtp_port']}, username={$smtpConfig['smtp_username']}");
            } else {
                // Use PHP's mail() function if SMTP is disabled
                $mail->isMail();
                
                // Get default sender from settings
                $defaultSettings = self::getDefaultSettings();
                $senderEmail = $defaultSettings['email'] ?: 'noreply@yoursite.com';
                $senderName = $defaultSettings['site_title'] ?: 'Infinity Enterprise';
                $mail->setFrom($senderEmail, $senderName);
                
                error_log("SMTP is disabled, using PHP mail() function");
            }
            
            $mail->addAddress($toEmail, $toName);                       // Add a recipient
            
            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = $subject;
            
            // Prepare the email content
            $emailContent = self::buildConfirmationEmail($toName, $confirmationMessage, $additionalInfo);
            $mail->Body    = $emailContent;
            
            // Send the email
            $result = $mail->send();
            
            if ($result) {
                error_log("SUCCESS: Confirmation email sent successfully to: $toEmail");
            } else {
                error_log("FAILED: Confirmation email failed to send to: $toEmail");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Get SMTP configuration from database settings
     */
    private static function getSmtpConfig() {
        require_once __DIR__ . '/../models/Settings.php';
        $settings = new Settings();
        $settingsData = $settings->getAll();
        
        return [
            'smtp_host' => $settingsData['smtp_host'] ?? 'localhost',
            'smtp_port' => $settingsData['smtp_port'] ?? 587,
            'smtp_username' => $settingsData['smtp_username'] ?? '',
            'smtp_password' => $settingsData['smtp_password'] ?? '',
            'smtp_encryption' => $settingsData['smtp_encryption'] ?? 'tls',
            'email_sender_address' => $settingsData['email_sender_address'] ?? '',
            'email_sender_name' => $settingsData['email_sender_name'] ?? '',
            'email_enabled' => $settingsData['email_enabled'] ?? true
        ];
    }
    
    /**
     * Get default settings for fallback
     */
    private static function getDefaultSettings() {
        require_once __DIR__ . '/../models/Settings.php';
        $settings = new Settings();
        $settingsData = $settings->getAll();
        
        return [
            'email' => $settingsData['email'] ?? 'noreply@yoursite.com',
            'site_title' => $settingsData['site_title'] ?? 'Our Company'
        ];
    }
    
    /**
     * Build the confirmation email template
     */
    private static function buildConfirmationEmail($name, $message, $additionalInfo = []) {
        $siteTitle = self::getSiteTitle();
        
        $emailTemplate = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Confirmation Message</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #ffffff; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .info-box { background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$siteTitle}</h2>
                    <h3>Confirmation Message</h3>
                </div>
                <div class='content'>
                    <p>Hello {$name},</p>
                    <p>{$message}</p>";
                    
        if (!empty($additionalInfo)) {
            $emailTemplate .= "<div class='info-box'><p><strong>Additional Information:</strong></p>";
            $emailTemplate .= "<ul>"; 
            foreach ($additionalInfo as $key => $value) {
                if ($value !== '') {
                    $emailTemplate .= "<li><strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> {$value}</li>";
                }
            }
            $emailTemplate .= "</ul></div>"; 
        }
        
        $emailTemplate .= "
                    <p>We will get back to you as soon as possible.</p>
                    <p>Best regards,<br/>{$siteTitle} Team</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " {$siteTitle}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $emailTemplate;
    }
    
    /**
     * Get site title from settings
     */
    private static function getSiteTitle() {
        require_once __DIR__ . '/../models/Settings.php';
        $settings = new Settings();
        $settingsData = $settings->getAll();
        
        return $settingsData['site_title'] ?? 'Our Company';
    }
}