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
            <!-- <img src="public/images/hero-illustration.svg" alt="Writing illustration" onerror="this.src='public/images/placeholder-hero.jpg'"> -->        
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

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Explore by Category</h2>
        </div>
        
        <div class="categories-grid">
            <?php
            $categories = [
                ['name' => 'Technology', 'icon' => 'fa-laptop-code', 'color' => '#3b82f6'],
                ['name' => 'Design', 'icon' => 'fa-palette', 'color' => '#ec4899'],
                ['name' => 'Business', 'icon' => 'fa-briefcase', 'color' => '#f59e0b'],
                ['name' => 'Health', 'icon' => 'fa-heartbeat', 'color' => '#10b981'],
                ['name' => 'Travel', 'icon' => 'fa-plane', 'color' => '#8b5cf6'],
                ['name' => 'Food', 'icon' => 'fa-utensils', 'color' => '#ef4444'],
                ['name' => 'Lifestyle', 'icon' => 'fa-coffee', 'color' => '#06b6d4'],
                ['name' => 'Entertainment', 'icon' => 'fa-film', 'color' => '#f97316']
            ];
            
            foreach ($categories as $category):
            ?>
            <a href="/StoryHub/index.php?url=explore&category=<?php echo strtolower($category['name']); ?>" class="category-card" style="--category-color: <?php echo $category['color']; ?>">
                <i class="fas <?php echo $category['icon']; ?> category-icon"></i>
                <h4><?php echo $category['name']; ?></h4>
                <span class="article-count">125+ articles</span>
            </a>
            <?php endforeach; ?>
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
