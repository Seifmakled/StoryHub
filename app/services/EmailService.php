<?php
/**
 * EmailService - Handles all email sending operations
 * Implements Service Pattern to separate email business logic from controllers
 */
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $emailConfig;

    public function __construct() {
        $this->emailConfig = require __DIR__ . '/../config/email_config.php';
    }

    /**
     * Send password reset email with code
     * 
     * @param string $email Recipient email address
     * @param string $code 6-digit reset code
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function sendPasswordResetEmail(string $email, string $code): array {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = $this->emailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->emailConfig['username'];
            $mail->Password = $this->emailConfig['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->emailConfig['port'];
            $mail->setFrom($this->emailConfig['from'], $this->emailConfig['from_name']);
            $mail->addAddress($email);
            $mail->Subject = 'Your StoryHub Password Reset Code';
            $mail->Body = "Your password reset code is: $code\nThis code expires in 15 minutes.";
            
            $mail->send();
            
            return ['success' => true, 'error' => null];
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

