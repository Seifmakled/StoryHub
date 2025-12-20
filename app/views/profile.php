<?php
$pageTitle = 'Profile - StoryHub';
$pageDescription = 'User profile page';
$pageCSS = 'profile.css';
$pageJS = 'profile.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';

// Determine if viewing own profile
$isOwnProfile = isset($_SESSION['user_id']) && isset($_GET['id']) && (int)$_SESSION['user_id'] === (int)$_GET['id'];
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
                    <h1 class="profile-name" id="profileName">User</h1>
                    <p class="profile-username" id="profileUsername"></p>
                </div>

                <div class="profile-actions">
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-outline" onclick="window.location.href='index.php?url=settings'">
                        <i class="fas fa-cog"></i> Edit Profile
                    </button>
                    <?php else: ?>
                    <button class="btn btn-primary" id="followBtn" type="button" style="display:none">
                        <i class="fas fa-user-plus"></i> Follow
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-bio">
                <p id="profileBio"></p>
            </div>

            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value" id="statArticles">0</span>
                    <span class="stat-label">Articles</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value" id="statFollowers">0</span>
                    <span class="stat-label">Followers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value" id="statFollowing">0</span>
                    <span class="stat-label">Following</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value" id="statLikes">0</span>
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
            <button class="tab-btn" data-tab="about">
                <i class="fas fa-info-circle"></i> About
            </button>
        </div>

        <!-- Articles Tab -->
        <div class="tab-content active" id="articles-tab">
            <div class="articles-grid" id="profileArticlesContainer"></div>
        </div>

        <!-- About Tab -->
        <div class="tab-content" id="about-tab">
            <div class="about-section">
                <div class="about-card">
                    <h3><i class="fas fa-user"></i> About</h3>
                    <p id="aboutText"></p>
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
                    <p id="joinedText"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
