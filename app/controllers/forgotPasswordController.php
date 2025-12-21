<?php
// Super simple forgot password controller
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../services/EmailService.php';

$userRepository = new UserRepository();
$emailService = new EmailService();

// Step 2: Handle code verification (check this FIRST before email submission)
if (isset($_POST['resetCode']) && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['resetCode']);
    $user = $userRepository->findByEmail($email);
    
    
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
    $userRepository->updatePasswordAndClearResetToken($email, $hashed);
    header('Location: ../../index.php?url=forgot-password&step=success');
    exit();
}

// Step 1: Handle email submission (check this LAST)
if (isset($_POST['email']) && !isset($_POST['resetCode']) && !isset($_POST['newPassword'])) {
    $email = trim($_POST['email']);
    $user = $userRepository->findByEmail($email);
    if ($user) {
        // Generate a 6-digit code
        $code = random_int(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        // Save code and expiry - Using Repository
        $userRepository->saveResetToken($user['id'], $code, $expires);
        // Send email using EmailService
        $emailService->sendPasswordResetEmail($email, $code);
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
