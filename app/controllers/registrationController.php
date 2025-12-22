<?php

session_start();

require_once __DIR__ . '/../repositories/UserRepository.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. SAFELY COLLECT FORM DATA (Using the new schema names)
    $full_name = htmlspecialchars(trim($_POST['full_name'] ?? '')); 
    $username  = htmlspecialchars(trim($_POST['username'] ?? '')); 
    $email     = htmlspecialchars(trim($_POST['email'] ?? ''));     
    $password  = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $is_verified = 0; // Default for email verification

    // 3. SERVER-SIDE VALIDATION (Cannot be bypassed)
    if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "empty_fields";
    } elseif ($password !== $confirm_password) {
        $error = "password_mismatch";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "invalid_email";
    }

    if (empty($error)) {
        
        try {
            // 4. SECURITY CHECK: Check for Existing User (Email OR Username) - Using Repository
            $userRepository = new UserRepository();
            
            if ($userRepository->emailExists($email) || $userRepository->usernameExists($username)) {
                $error = $userRepository->emailExists($email) ? "email_exists" : "username_exists";
            } else {
                
                // 5. SECURITY: HASH PASSWORD AND INSERT USER - Using Repository
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $userId = $userRepository->createUser([
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password,
                    'full_name' => $full_name,
                    'is_verified' => $is_verified
                ]);

                if ($userId) {
                    // 6. SUCCESS: START SESSION AND REDIRECT
                    $_SESSION['user_email'] = $email;
                    header("Location: ../views/verify.php"); 
                    exit;
                } else {
                    $error = "db_insert_failed";
                }
            }
        } catch (PDOException $e) {
            error_log("Database Error during registration: " . $e->getMessage());
            $error = "db_error";
        }
    }
}

// 7. ERROR HANDLING: Redirect back to the registration view
header("Location: ../views/register.php?error=" . $error);
exit;
?>