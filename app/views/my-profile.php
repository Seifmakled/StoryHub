<?php
$pageTitle = 'My Profile - StoryHub';
$pageDescription = 'Manage your profile and articles';
$pageCSS = 'profile.css'; // Reuse profile styles
$pageJS = 'my-profile.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<script>
    // Compute app base URL that points to project root (where index.php lives)
    (function() {
        var scriptName = <?php echo json_encode($_SERVER['SCRIPT_NAME']); ?>;
        var base;

        // If this view is included through index.php, SCRIPT_NAME is typically /StoryHub/index.php
        if (/\/index\.php$/.test(scriptName)) {
            base = scriptName.replace(/\/index\.php$/, '/');
        } else {
            // If opened directly (not recommended), fall back to trimming /app/... to reach project root
            base = scriptName.replace(/\/app\/.+$/, '/');
        }

        if (!/\/$/.test(base)) base += '/';
        window.APP_BASE = base;
    })();
</script>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-cover">
            <img src="public/images/cover-placeholder.jpg" alt="Cover" id="coverImage">
        </div>

        <div class="profile-header-content">
            <div class="profile-avatar-wrapper">
                <img src="public/images/default-avatar.jpg" alt="Profile" class="profile-avatar" id="profileAvatar">
            </div>

            <div class="profile-info">
                <div class="profile-info-main">
                    <h1 class="profile-name" id="profileName">Your Name</h1>
                    <p class="profile-username" id="profileUsername">@username</p>
                </div>

                <div class="profile-actions">
                    <a href="/StoryHub/index.php?url=write" class="btn btn-primary">
                        <i class="fas fa-pen"></i> Create Article
                    </a>
                </div>
            </div>

            <div class="profile-bio" id="profileBio">
                <p></p>
            </div>

            <div class="profile-stats" id="profileStats">
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
                <div class="stat-item">
                    <span class="stat-value" id="statSaved">0</span>
                    <span class="stat-label">Saved</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value" id="statComments">0</span>
                    <span class="stat-label">Comments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="articles">
                <i class="fas fa-newspaper"></i> My Articles <span class="tab-count" id="countArticles">0</span>
            </button>
            <button class="tab-btn" data-tab="drafts">
                <i class="fas fa-file-alt"></i> Drafts <span class="tab-count" id="countDrafts">0</span>
            </button>
            <button class="tab-btn" data-tab="saved">
                <i class="fas fa-bookmark"></i> Saved
            </button>
            <button class="tab-btn" data-tab="liked">
                <i class="fas fa-heart"></i> Liked
            </button>
            <button class="tab-btn" data-tab="comments">
                <i class="fas fa-comments"></i> Comments
            </button>
            <button class="tab-btn" data-tab="settings">
                <i class="fas fa-cog"></i> Settings
            </button>
        </div>

        <!-- Articles Tab -->
        <div class="tab-content active" id="articles-tab">
            <div class="articles-grid" id="myArticlesContainer">
                <!-- Populated by JS -->
            </div>
        </div>

        <!-- Drafts Tab -->
        <div class="tab-content" id="drafts-tab">
            <div class="articles-grid" id="draftsContainer"></div>
        </div>

        <!-- Saved Tab -->
        <div class="tab-content" id="saved-tab">
            <div class="articles-list" id="savedContainer"></div>
        </div>

        <!-- Liked Tab -->
        <div class="tab-content" id="liked-tab">
            <div class="articles-list" id="likedContainer"></div>
        </div>

        <!-- Comments Tab -->
        <div class="tab-content" id="comments-tab">
            <div class="comments-list" id="commentsContainer"></div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-content" id="settings-tab">
            <div class="about-section">
                <div class="about-card">
                    <h3><i class="fas fa-user-edit"></i> Edit Profile</h3>
                    <form id="profileForm" class="settings-form" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" placeholder="Your full name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" placeholder="your@email.com">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="avatarInput">Profile Picture</label>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="coverInput">Cover Picture</label>
                                <input type="file" id="coverInput" name="cover" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" rows="4" placeholder="Tell readers about yourself..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        <span id="profileSaveStatus" class="save-status"></span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
