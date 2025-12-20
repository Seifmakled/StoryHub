<?php
$pageTitle = 'Write - StoryHub';
$pageDescription = 'Create a new story';
$pageCSS = 'write.css';
$pageJS = 'write.js';

// Optional auth guard: send guests to login
if (!isset($_SESSION['user_id'])) {
    header('Location: /StoryHub/index.php?url=login&error=login_required');
    exit();
}

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="write-shell">
    <section class="write-hero">
        <div class="hero-left">
            <span class="pill">Create</span>
            <h1>Start a new story</h1>
            <p>Share an idea, a draft, or a deep dive. Add a cover, pick tags, and publish when ready.</p>
            <div class="hero-meta">
                <div class="meta-item">
                    <i class="fas fa-save"></i>
                    <span>Autosave drafts (local)</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span>Live preview</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-lock"></i>
                    <span>Private until published</span>
                </div>
            </div>
        </div>
        <div class="hero-right">
            <div class="cover-drop" id="coverDrop">
                <input type="file" id="coverInput" accept="image/*" hidden>
                <div class="cover-hint">
                    <i class="fas fa-image"></i>
                    <p>Drop a cover image or click to upload</p>
                    <small>JPG/PNG up to 5MB</small>
                </div>
                <img id="coverPreview" alt="Cover preview" style="display:none;">
            </div>
        </div>
    </section>

    <section class="write-editor">
        <form id="writeForm" class="editor-grid">
            <div class="editor-main">
                <label class="field">
                    <span class="field-label">Title</span>
                    <input type="text" id="titleInput" name="title" placeholder="An unforgettable headline" required>
                </label>

                <label class="field">
                    <span class="field-label">Subtitle</span>
                    <input type="text" id="subtitleInput" name="subtitle" placeholder="Add context in one line" maxlength="160">
                </label>

                <label class="field">
                    <span class="field-label">Body</span>
                    <textarea id="bodyInput" name="body" rows="14" placeholder="Begin typing your story..."></textarea>
                </label>
            </div>

            <aside class="editor-side">
                <label class="field">
                    <span class="field-label">Tags</span>
                    <input type="text" id="tagsInput" name="tags" placeholder="e.g. design, productivity, ai">
                    <small class="field-hint">Comma-separated. Up to 5.</small>
                </label>

                <div class="field">
                    <span class="field-label">Visibility</span>
                    <div class="segmented" id="visibilityToggle">
                        <button type="button" data-value="public" class="active">Public</button>
                        <button type="button" data-value="private">Private</button>
                    </div>
                    <input type="hidden" name="visibility" id="visibilityInput" value="public">
                </div>

                <div class="field">
                    <span class="field-label">Actions</span>
                    <div class="action-buttons">
                        <button type="button" class="btn ghost" id="saveDraft">Save Draft</button>
                        <button type="submit" class="btn primary" id="publishBtn">Publish</button>
                    </div>
                    <div class="tiny-text" id="autosaveStatus">Not saved</div>
                </div>
            </aside>
        </form>
    </section>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
