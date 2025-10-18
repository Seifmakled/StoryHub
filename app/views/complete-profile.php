<?php
$pageTitle = 'Complete Your Profile - StoryHub';
$pageDescription = 'Set your profile picture, description, and interests';
$pageCSS = 'complete-profile.css';
$pageJS = 'complete-profile.js';

include __DIR__ . '/../partials/header.php';
?>

<div class="profile-setup-container">
    <div class="profile-setup-wrapper">
        <div class="setup-header">
            <h1>Welcome! Let’s complete your profile</h1>
            <p>Help readers get to know you. You can change these later in your profile settings.</p>
        </div>

        <form id="completeProfileForm" class="profile-setup-form" enctype="multipart/form-data">
            <!-- Avatar Upload -->
            <div class="form-section">
                <label class="section-label">Profile Picture</label>
                <div class="avatar-row">
                    <div class="avatar-preview">
                        <img id="avatarPreview" src="/StoryHub/public/images/default-avatar.jpg" alt="Profile preview">
                    </div>
                    <div class="avatar-actions">
                        <input type="file" id="avatar" name="avatar" accept="image/*" hidden>
                        <button type="button" class="btn" id="chooseAvatar">
                            <i class="fas fa-upload"></i> Upload Image
                        </button>
                        <button type="button" class="btn btn-secondary" id="removeAvatar">
                            <i class="fas fa-times"></i> Remove
                        </button>
                        <p class="help-text">JPG, PNG up to 2MB. Recommended 400x400px.</p>
                    </div>
                </div>
            </div>

            <!-- Bio / Description -->
            <div class="form-section">
                <label for="bio" class="section-label">Short Bio</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Tell the community about yourself"></textarea>
                <div class="char-counter"><span id="bioCount">0</span>/200</div>
            </div>

            <!-- Interests -->
            <div class="form-section">
                <label class="section-label">Your Interests</label>
                <p class="help-text">Pick a few topics you’re into. This helps us personalize your feed.</p>
                <div class="chips" id="interestsChips">
                    <!-- Chips populated by JS -->
                </div>
                <div class="selection-hint">Selected: <span id="selectedCount">0</span>/5</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <span class="btn-text">Finish Setup</span>
                    <span class="btn-loader" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
                </button>
                <a href="/StoryHub/index.php?url=landing" class="btn btn-link">Skip for now</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
