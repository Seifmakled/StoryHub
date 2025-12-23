<?php
$pageTitle = 'StoryHub - Share Your Voice with the World';
$pageDescription = 'Join thousands of writers sharing their stories. Discover, create, and connect.';
$pageCSS = 'landing.css';
$pageJS = 'landing.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">Where Stories Come to Life</h1>
            <p class="hero-subtitle">Share your thoughts, express your creativity, and connect with a community of passionate readers and writers.</p>
            <div class="hero-buttons">
                <a href="/StoryHub/index.php?url=register" class="btn btn-primary">Start Writing</a>
                <a href="/StoryHub/index.php?url=explore" class="btn btn-secondary">Explore Stories</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-model" aria-label="StoryHub 3D preview">
                <canvas id="storyhubModelCanvas"></canvas>
                <div class="hero-model-fallback" id="storyhubModelFallback" hidden>
                    3D preview unavailable.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Section -->
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Stories</h2>
            <a href="/StoryHub/index.php?url=explore&filter=featured" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="featured-grid" id="featuredGrid">
            <div class="empty-state loading">Loading featured stories...</div>
        </div>
    </div>
</section>

<!-- Trending Section -->
<section class="trending-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-fire"></i> Trending Now</h2>
            <a href="/StoryHub/index.php?url=explore&filter=trending" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="trending-grid" id="trendingGrid">
            <div class="empty-state loading">Loading trending stories...</div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Share Your Story?</h2>
            <p>Join our community of writers and readers. Start your journey today.</p>
            <a href="/StoryHub/index.php?url=register" class="btn btn-primary btn-lg">Get Started for Free</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../partials/footer.php'; ?>
