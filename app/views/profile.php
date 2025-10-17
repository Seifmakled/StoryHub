<?php
$pageTitle = 'Profile - StoryHub';
$pageDescription = 'User profile page';
$pageCSS = 'profile.css';
$pageJS = 'profile.js';

include '../partials/header.php';
include '../partials/navbar.php';

// Mock user data (replace with actual database query)
$isOwnProfile = isset($_SESSION['user_id']) && isset($_GET['id']) && $_SESSION['user_id'] == $_GET['id'];
?>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-cover">
            <img src="public/images/cover-placeholder.jpg" alt="Cover" id="coverImage">
            <?php if ($isOwnProfile): ?>
            <button class="btn-edit-cover" title="Change cover photo">
                <i class="fas fa-camera"></i>
            </button>
            <?php endif; ?>
        </div>

        <div class="profile-header-content">
            <div class="profile-avatar-wrapper">
                <img src="public/images/default-avatar.jpg" alt="Profile" class="profile-avatar" id="profileAvatar">
                <?php if ($isOwnProfile): ?>
                <button class="btn-edit-avatar" title="Change profile picture">
                    <i class="fas fa-camera"></i>
                </button>
                <?php endif; ?>
            </div>

            <div class="profile-info">
                <div class="profile-info-main">
                    <h1 class="profile-name">John Doe</h1>
                    <p class="profile-username">@johndoe</p>
                </div>

                <div class="profile-actions">
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-outline" onclick="window.location.href='index.php?page=settings'">
                        <i class="fas fa-cog"></i> Edit Profile
                    </button>
                    <?php else: ?>
                    <button class="btn btn-primary" id="followBtn">
                        <i class="fas fa-user-plus"></i> Follow
                    </button>
                    <button class="btn btn-outline">
                        <i class="fas fa-envelope"></i> Message
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-bio">
                <p>Passionate writer and storyteller. Love exploring new ideas and sharing experiences through words. ✍️</p>
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value">42</span>
                    <span class="stat-label">Articles</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">1.2K</span>
                    <span class="stat-label">Followers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">856</span>
                    <span class="stat-label">Following</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">3.5K</span>
                    <span class="stat-label">Likes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="articles">
                <i class="fas fa-newspaper"></i> Articles
            </button>
            <button class="tab-btn" data-tab="liked">
                <i class="fas fa-heart"></i> Liked
            </button>
            <button class="tab-btn" data-tab="saved">
                <i class="fas fa-bookmark"></i> Saved
            </button>
            <button class="tab-btn" data-tab="about">
                <i class="fas fa-info-circle"></i> About
            </button>
        </div>

        <!-- Articles Tab -->
        <div class="tab-content active" id="articles-tab">
            <div class="articles-grid">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <article class="article-card">
                    <div class="article-image">
                        <img src="public/images/article-placeholder.jpg" alt="Article">
                        <?php if ($isOwnProfile): ?>
                        <div class="article-menu">
                            <button class="btn-menu"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="menu-dropdown">
                                <a href="#edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="#delete"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="article-content">
                        <span class="article-category">Technology</span>
                        <h3>The Future of Web Development in 2024</h3>
                        <p>Exploring the latest trends and technologies shaping the future of web development...</p>
                        <div class="article-meta">
                            <span class="article-date"><i class="far fa-calendar"></i> May 15, 2024</span>
                            <div class="article-stats">
                                <span><i class="fas fa-eye"></i> 1.2K</span>
                                <span><i class="fas fa-heart"></i> 234</span>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Liked Tab -->
        <div class="tab-content" id="liked-tab">
            <div class="articles-list">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="article-list-item">
                    <img src="public/images/article-placeholder.jpg" alt="Article">
                    <div class="article-list-content">
                        <h4>Article Title Goes Here</h4>
                        <p>Brief excerpt of the article content...</p>
                        <div class="article-list-meta">
                            <span>by <strong>Author Name</strong></span>
                            <span>May 15, 2024</span>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Saved Tab -->
        <div class="tab-content" id="saved-tab">
            <div class="articles-list">
                <p class="empty-state">
                    <i class="fas fa-bookmark"></i>
                    <span>No saved articles yet</span>
                </p>
            </div>
        </div>

        <!-- About Tab -->
        <div class="tab-content" id="about-tab">
            <div class="about-section">
                <div class="about-card">
                    <h3><i class="fas fa-user"></i> About</h3>
                    <p>Passionate writer and storyteller with a love for technology and innovation. Been writing for 5+ years and sharing insights on web development, design, and entrepreneurship.</p>
                </div>

                <div class="about-card">
                    <h3><i class="fas fa-link"></i> Social Links</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                        <a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a>
                        <a href="#"><i class="fab fa-github"></i> GitHub</a>
                        <a href="#"><i class="fas fa-globe"></i> Website</a>
                    </div>
                </div>

                <div class="about-card">
                    <h3><i class="fas fa-calendar"></i> Joined</h3>
                    <p>Member since January 2023</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
