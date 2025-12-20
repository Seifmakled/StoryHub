<?php
// Start a session if one is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /StoryHub/index.php?url=login&error=invalid_request");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

$pdo = Database::getConnection(); 

$email_or_username = htmlspecialchars(trim($_POST['email_or_username'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($email_or_username) || empty($password)) {
    header("Location: /StoryHub/index.php?url=login&error=empty_fields");
    exit();
}

try {
    // MODIFICATION 1: Add the 'is_admin' column to the SELECT statement.
    $sql = "SELECT id, full_name, username, email, password, is_admin, status FROM users WHERE email = ? OR username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email_or_username, $email_or_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: /StoryHub/index.php?url=login&error=user_not_found"); 
        exit();
    }

    if (password_verify($password, $user['password'])) {
        if (isset($user['status']) && $user['status'] === 'banned') {
            header("Location: /StoryHub/index.php?url=login&error=banned");
            exit();
        }
        
        // Setup standard session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['logged_in'] = true;
        
        // MODIFICATION 2: Store the is_admin status in the session.
        $_SESSION['is_admin'] = (bool)$user['is_admin']; 

        // MODIFICATION 3: Redirect based on the user's admin status.
        if ($_SESSION['is_admin']) {
            // Redirect admin users to the admin dashboard view via router to ensure session is loaded
            header("Location: /StoryHub/index.php?url=admin"); 
        } else {
            // Redirect regular users to their profile (router handles session start)
            header("Location: /StoryHub/index.php?url=my-profile"); 
        }
        exit();
        
    } else {
        header("Location: /StoryHub/index.php?url=login&error=invalid_credentials"); 
        exit();
    }

} catch (PDOException $e) {
    // Handle database connection or query errors
    error_log("Login DB Error: " . $e->getMessage());
    header("Location: /StoryHub/index.php?url=login&error=db_error");
    exit();
}

?>