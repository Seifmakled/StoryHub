<?php
// Start a session if one is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../index.php?url=login&error=invalid_request");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

$pdo = Database::getConnection(); 

$email_or_username = htmlspecialchars(trim($_POST['email_or_username'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($email_or_username) || empty($password)) {
    header("Location: ../../index.php?url=login&error=empty_fields");
    exit();
}

try {
    // MODIFICATION 1: Add the 'is_admin' column to the SELECT statement.
    $sql = "SELECT id, full_name, username, email, password, is_admin FROM users WHERE email = ? OR username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email_or_username, $email_or_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // a toastie is required here to show invalid login
        header("Location: ../views/login.php"); 
        exit();
    }

    if (password_verify($password, $user['password'])) {
        
        // Setup standard session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['logged_in'] = true;
        
        // MODIFICATION 2: Store the is_admin status in the session.
        $_SESSION['is_admin'] = (bool)$user['is_admin']; 

        // MODIFICATION 3: Redirect based on the user's admin status.
        if ($_SESSION['is_admin']) {
            // Redirect admin users to the admin dashboard view
            header("Location: ../views/admin-dashboard.php"); 
        } else {
            // Redirect regular users to the landing page view
            header("Location: ../views/landing.php"); 
        }
        exit();
        
    } else {

        // a toastie is required here to show invalid login
        header("Location: ../views/login.php"); 
        exit();
    }

} catch (PDOException $e) {
    // Handle database connection or query errors
    error_log("Login DB Error: " . $e->getMessage());
    header("Location: ../../index.php?url=login&error=db_error");
    exit();
}

?>