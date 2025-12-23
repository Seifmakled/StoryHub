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

<style>
/* Inline styles to ensure genre selector renders even if cached CSS lags */
.genre-field .field-label { display:inline-flex; align-items:center; gap:0.5rem; }
.genre-field .label-pill { padding:0.15rem 0.6rem; border-radius:999px; background:linear-gradient(120deg, rgba(99,102,241,0.16), rgba(236,72,153,0.14)); color:#4338ca; font-size:0.82rem; font-weight:800; }
.pill-select { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px,1fr)); gap:0.5rem; background:rgba(148,163,184,0.12); padding:0.55rem; border-radius:14px; border:1px solid rgba(148,163,184,0.35); box-shadow:0 12px 30px -26px rgba(15,23,42,0.35); }
.pill-select button { border:1px solid rgba(148,163,184,0.4); background:#fff; border-radius:12px; padding:0.6rem 0.8rem; font-weight:700; color:#334155; cursor:pointer; transition:all 0.2s ease; box-shadow:0 10px 24px -20px rgba(99,102,241,0.35); }
.pill-select button:hover { border-color:#6366f1; color:#6366f1; transform:translateY(-1px); }
.pill-select button.active { background:linear-gradient(120deg, rgba(99,102,241,0.12), rgba(236,72,153,0.12)); border-color:#6366f1; color:#6366f1; box-shadow:0 16px 38px -24px rgba(99,102,241,0.55); }
</style>

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

                <label class="field genre-field">
                    <span class="field-label">
                        Genre
                        <span class="label-pill">Required</span>
                    </span>
                    <div class="pill-select" role="listbox" aria-label="Genre">
                        <button type="button" data-value="technology">Technology</button>
                        <button type="button" data-value="design">Design</button>
                        <button type="button" data-value="business">Business</button>
                        <button type="button" data-value="health">Health</button>
                        <button type="button" data-value="travel">Travel</button>
                        <button type="button" data-value="food">Food</button>
                        <button type="button" data-value="lifestyle">Lifestyle</button>
                        <button type="button" data-value="entertainment">Entertainment</button>
                    </div>
                    <input type="hidden" id="categoryInput" name="category" required>
                    <small class="field-hint">Pick the closest fit so Explore can place it correctly.</small>
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
