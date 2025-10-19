<?php
// Ensure database bootstrap runs even if this partial is opened directly.
if (!class_exists('Database')) {
    $dbPath = __DIR__ . '/../../config/db.php';
    if (is_file($dbPath)) {
        require_once $dbPath;
        try { new Database(); } catch (Throwable $e) { /* ignore */ }
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

    <!-- Loads CSS of the current page -->
    <?php if (isset($pageCSS)): ?>
        <link rel="stylesheet" href="/StoryHub/public/css/<?php echo $pageCSS; ?>">
    <?php endif; ?>

    <!-- Page-specific JavaScript -->
<?php if (isset($pageJS)): ?>
    <script src="/StoryHub/public/js/<?php echo $pageJS; ?>"></script>
<?php endif; ?>
</head>
<body>