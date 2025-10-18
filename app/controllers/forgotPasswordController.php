<?php
// Super simple forgot password controller
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../vendor/autoload.php';
// Load email config
$emailConfig = require __DIR__ . '/../config/email_config.php';

$pdo = Database::getConnection();

// Step 2: Handle code verification (check this FIRST before email submission)
if (isset($_POST['resetCode']) && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['resetCode']);
    $stmt = $pdo->prepare('SELECT id, reset_token, reset_token_expiry FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    
    if ($user && $user['reset_token'] === $code && strtotime($user['reset_token_expiry']) > time()) {
        // Code is valid
        header('Location: ../../index.php?url=forgot-password&step=reset&email=' . urlencode($email));
        exit();
    } else {
        // Invalid code - stay on verify page with error
        header('Location: ../../index.php?url=forgot-password&step=verify&email=' . urlencode($email) . '&error=invalid_code');
        exit();
    }
}

// Step 3: Handle password reset (check this BEFORE email submission)
if (isset($_POST['newPassword']) && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $newPassword = $_POST['newPassword'];
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?');
    $stmt->execute([$hashed, $email]);
    header('Location: ../../index.php?url=forgot-password&step=success');
    exit();
}

// Step 1: Handle email submission (check this LAST)
if (isset($_POST['email']) && !isset($_POST['resetCode']) && !isset($_POST['newPassword'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        // Generate a 6-digit code
        $code = random_int(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        // Save code and expiry
        $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?');
        $stmt->execute([$code, $expires, $user['id']]);
        // Send email using PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $emailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $emailConfig['username'];
            $mail->Password = $emailConfig['password'];
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $emailConfig['port'];
            $mail->setFrom($emailConfig['from'], $emailConfig['from_name']);
            $mail->addAddress($email);
            $mail->Subject = 'Your StoryHub Password Reset Code';
            $mail->Body = "Your password reset code is: $code\nThis code expires in 15 minutes.";
            $mail->send();
        } catch (Exception $e) {
            // Log error or show message
        }
        // Redirect to verify code step (could use session or GET param)
        header('Location: ../../index.php?url=forgot-password&step=verify&email=' . urlencode($email));
        exit();
    } else {
        header('Location: ../../index.php?url=forgot-password&error=email_not_found');
        exit();
    }
}

// If nothing matched, redirect back
header('Location: ../../index.php?url=forgot-password');
exit();
?>
