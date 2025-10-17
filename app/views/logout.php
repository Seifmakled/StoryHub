<?php
// Default page CSS (logout redirects immediately but define for consistency)
$pageCSS = 'landing.css';

session_start();
session_destroy();
header('Location: index.php?url=landing');
exit();
?>
