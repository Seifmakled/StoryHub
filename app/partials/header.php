<?php
// Ensure database bootstrap runs even if this partial is opened directly.
if (!class_exists('Database')) {
    $dbPath = __DIR__ . '/../../config/db.php';
    if (is_file($dbPath)) {
        require_once $dbPath;
        try { Database::getInstance(); } catch (Throwable $e) { /* ignore */ }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDescription ?? 'Share your stories, connect with writers, and discover amazing content'; ?>">
    <title><?php echo $pageTitle ?? 'UGC Platform - Share Your Voice'; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Global Layout CSS (navbar + footer) -->
    <link rel="stylesheet" href="/StoryHub/public/css/layout.css">

    <!-- Global layout interactions (loader, nav helpers) -->
    <script src="/StoryHub/public/js/layout.js" defer></script>

    <script>
        // Base path for fetch() and asset URLs when hosted under a subfolder (e.g. /StoryHub/)
        window.APP_BASE = <?php
            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
            $base = ($base === '' ? '/' : ($base . '/'));
            echo json_encode($base);
        ?>;
    </script>

    <!-- Loads CSS of the current page -->
    <?php if (isset($pageCSS)): ?>
        <link rel="stylesheet" href="/StoryHub/public/css/<?php echo $pageCSS; ?>">
    <?php endif; ?>

    <!-- Page-specific JavaScript -->
<?php if (isset($pageJS)): ?>
    <script src="/StoryHub/public/js/<?php echo $pageJS; ?>" defer></script>
<?php endif; ?>
</head>
<body>
    <div id="page-loader" class="page-loader">
        <div class="loader-card loader-style-once">
            <div class="loader-logo">
                <i class="fas fa-feather-alt"></i>
                <span>StoryHub</span>
            </div>
            <div class="loader-bar"><span class="loader-progress"></span></div>
        </div>
    </div>