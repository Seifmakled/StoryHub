<?php
$pageTitle = 'StoryHub - Share Your Voice with the World';
$pageDescription = 'Join thousands of writers sharing their stories. Discover, create, and connect.';
$pageCSS = 'landing.css';
$pageJS = 'landing.js';

include '../partials/header.php';
include '../partials/navbar.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">Where Stories Come to Life</h1>
            <p class="hero-subtitle">Share your thoughts, express your creativity, and connect with a community of passionate readers and writers.</p>
            <div class="hero-buttons">
                <a href="index.php?url=register" class="btn btn-primary">Start Writing</a>
                <a href="index.php?url=explore" class="btn btn-secondary">Explore Stories</a>
            </div>
        </div>
        <div class="hero-image">
            <!-- <img src="public/images/hero-illustration.svg" alt="Writing illustration" onerror="this.src='public/images/placeholder-hero.jpg'"> -->
    hi        
        </div>
    </div>
</section>

<!-- Featured Section -->
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Stories</h2>
            <a href="index.php?url=explore&filter=featured" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="featured-grid">
            <!-- Featured Article 1 -->
            <div class="featured-card featured-large">
                <div class="featured-image">
                    <img src="public/images/article-placeholder.jpg" alt="Article">
                    <span class="featured-badge">Featured</span>
                </div>
                <div class="featured-content">
                    <div class="featured-meta">
                        <span class="category">Technology</span>
                        <span class="reading-time"><i class="far fa-clock"></i> 8 min read</span>
                    </div>
                    <h3>The Future of Artificial Intelligence in Everyday Life</h3>
                    <p>Exploring how AI is transforming the way we live, work, and interact with technology in our daily routines...</p>
                    <div class="author-info">
                        <img src="public/images/default-avatar.jpg" alt="Author" class="author-avatar">
                        <div class="author-details">
                            <span class="author-name">Jane Doe</span>
                            <span class="publish-date">May 15, 2024</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Article 2 & 3 -->
            <div class="featured-small-grid">
                <div class="featured-card featured-small">
                    <div class="featured-image">
                        <img src="public/images/article-placeholder.jpg" alt="Article">
                    </div>
                    <div class="featured-content">
                        <span class="category">Design</span>
                        <h4>Minimalist Web Design Trends</h4>
                        <div class="author-info">
                            <img src="public/images/default-avatar.jpg" alt="Author" class="author-avatar">
                            <span class="author-name">John Smith</span>
                        </div>
                    </div>
                </div>

                <div class="featured-card featured-small">
                    <div class="featured-image">
                        <img src="public/images/article-placeholder.jpg" alt="Article">
                    </div>
                    <div class="featured-content">
                        <span class="category">Travel</span>
                        <h4>Hidden Gems of Southeast Asia</h4>
                        <div class="author-info">
                            <img src="public/images/default-avatar.jpg" alt="Author" class="author-avatar">
                            <span class="author-name">Sarah Lee</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trending Section -->
<section class="trending-section">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-fire"></i> Trending Now</h2>
            <a href="index.php?url=explore&filter=trending" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="trending-grid">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="trending-card">
                <div class="trending-image">
                    <img src="public/images/article-placeholder.jpg" alt="Article">
                    <div class="trending-overlay">
                        <span class="trending-number">#<?php echo $i; ?></span>
                    </div>
                </div>
                <div class="trending-content">
                    <span class="category">Category</span>
                    <h3>Article Title Goes Here and It Can Be Long</h3>
                    <div class="article-stats">
                        <span><i class="fas fa-eye"></i> 2.5k</span>
                        <span><i class="fas fa-heart"></i> 342</span>
                        <span><i class="fas fa-comment"></i> 89</span>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
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
            <a href="index.php?url=explore&category=<?php echo strtolower($category['name']); ?>" class="category-card" style="--category-color: <?php echo $category['color']; ?>">
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
            <a href="index.php?url=register" class="btn btn-primary btn-lg">Get Started for Free</a>
        </div>
    </div>
</section>

<?php include '../partials/footer.php'; ?>
