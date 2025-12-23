// Write page interactions
(function() {
    const coverDrop = document.getElementById('coverDrop');
    const coverInput = document.getElementById('coverInput');
    const coverPreview = document.getElementById('coverPreview');
    const titleInput = document.getElementById('titleInput');
    const subtitleInput = document.getElementById('subtitleInput');
    const bodyInput = document.getElementById('bodyInput');
    const tagsInput = document.getElementById('tagsInput');
    const visibilityToggle = document.getElementById('visibilityToggle');
    const visibilityInput = document.getElementById('visibilityInput');
    const autosaveStatus = document.getElementById('autosaveStatus');
    const saveDraftBtn = document.getElementById('saveDraft');
    const form = document.getElementById('writeForm');

    const params = new URLSearchParams(window.location.search);
    const editingId = params.get('edit');
    const STORAGE_KEY = editingId ? `storyhub_write_edit_${editingId}` : 'storyhub_write_draft';

    const readFile = (file) => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });

    // Cover upload
    if (coverDrop && coverInput && coverPreview) {
        coverDrop.addEventListener('click', () => coverInput.click());
        coverDrop.addEventListener('dragover', (e) => {
            e.preventDefault();
            coverDrop.classList.add('dragging');
        });
        coverDrop.addEventListener('dragleave', () => coverDrop.classList.remove('dragging'));
        coverDrop.addEventListener('drop', async (e) => {
            e.preventDefault();
            coverDrop.classList.remove('dragging');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const data = await readFile(file);
                coverPreview.src = data;
                coverPreview.style.display = 'block';
                saveDraft();
            }
        });
        coverInput.addEventListener('change', async () => {
            const file = coverInput.files[0];
            if (file && file.type.startsWith('image/')) {
                const data = await readFile(file);
                coverPreview.src = data;
                coverPreview.style.display = 'block';
                saveDraft();
            }
        });
    }

    // Visibility toggle
    if (visibilityToggle && visibilityInput) {
        visibilityToggle.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                visibilityToggle.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                visibilityInput.value = btn.dataset.value;
            });
        });
    }

    // Autosave to localStorage
    const applyDraftToForm = (draft) => {
        if (!draft) return;
        if (titleInput) titleInput.value = draft.title || '';
        if (subtitleInput) subtitleInput.value = draft.subtitle || '';
        if (bodyInput) bodyInput.value = draft.body || '';
        if (tagsInput) tagsInput.value = draft.tags || '';
        if (visibilityInput && draft.visibility) visibilityInput.value = draft.visibility;
        if (visibilityToggle) {
            visibilityToggle.querySelectorAll('button').forEach(b => {
                b.classList.toggle('active', b.dataset.value === (draft.visibility || 'public'));
            });
        }
        if (coverPreview && draft.cover) {
            coverPreview.src = draft.cover;
            coverPreview.style.display = 'block';
        }
    };

    const saveDraft = () => {
        const draft = {
            title: titleInput?.value || '',
            subtitle: subtitleInput?.value || '',
            body: bodyInput?.value || '',
            tags: tagsInput?.value || '',
            visibility: visibilityInput?.value || 'public',
            cover: coverPreview?.src && coverPreview.style.display !== 'none' ? coverPreview.src : null,
            updated: Date.now()
        };
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
            autosaveStatus.textContent = 'Saved locally';
        } catch (err) {
            autosaveStatus.textContent = 'Autosave failed';
        }
    };

    const loadLocalDraft = () => {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (!stored) return null;
            const draft = JSON.parse(stored);
            applyDraftToForm(draft);
            autosaveStatus.textContent = 'Draft loaded';
            return draft;
        } catch (err) {
            autosaveStatus.textContent = 'Could not load draft';
            return null;
        }
    };

    async function loadServerDraft() {
        if (!editingId) return;
        try {
            const res = await fetch(`index.php?url=api-articles&id=${encodeURIComponent(editingId)}`, { credentials: 'same-origin' });
            const payload = await res.json();
            if (!res.ok) throw new Error(payload.error || res.statusText || 'Failed to load draft');
            const a = payload.data;
            const draft = {
                title: a.title || '',
                subtitle: a.subtitle || a.excerpt || '',
                body: a.content || '',
                tags: a.tags || '',
                visibility: a.is_published === 1 ? 'public' : 'draft',
                cover: a.featured_image ? `public/images/${a.featured_image}` : null
            };
            applyDraftToForm(draft);
            autosaveStatus.textContent = 'Draft loaded from server';
        } catch (err) {
            autosaveStatus.textContent = 'Could not load server draft';
            console.error('Load draft error:', err);
        }
    }

    [titleInput, subtitleInput, bodyInput, tagsInput].forEach(el => {
        if (!el) return;
        el.addEventListener('input', () => {
            autosaveStatus.textContent = 'Saving...';
            window.requestIdleCallback ? requestIdleCallback(saveDraft) : setTimeout(saveDraft, 150);
        });
    });

    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', (e) => {
            e.preventDefault();
            saveDraft();
        });
    }

    async function submitArticle(visibilityMode) {
        const title = titleInput?.value.trim() || '';
        const body = bodyInput?.value.trim() || '';
        const isDraft = visibilityMode === 'draft';

        if (title.length < 3) {
            autosaveStatus.textContent = 'Add a title (min 3 characters).';
            return;
        }
        if (!isDraft && body.length < 20) {
            autosaveStatus.textContent = 'Story body is too short (min 20 characters).';
            return;
        }

        const fd = new FormData();
        fd.append('title', title);
        fd.append('subtitle', subtitleInput?.value || '');
        fd.append('body', body);
        fd.append('tags', tagsInput?.value || '');
        fd.append('visibility', visibilityMode);
        if (editingId) {
            fd.append('id', editingId);
        }

        if (coverInput && coverInput.files && coverInput.files[0]) {
            fd.append('cover', coverInput.files[0]);
        }

        const res = await fetch('index.php?url=api-articles', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(data.error || res.statusText || 'Failed to save');
        }

        localStorage.removeItem(STORAGE_KEY);
        autosaveStatus.textContent = data.message || 'Saved';
        return data;
    }

    if (form) {
        let submitting = false;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (submitting) return;
            submitting = true;
            const publishBtn = document.getElementById('publishBtn');
            if (publishBtn) publishBtn.disabled = true;
            autosaveStatus.textContent = 'Publishing…';
            try {
                const visibilityValue = visibilityInput?.value || 'public';
                const result = await submitArticle(visibilityValue);
                if (!result) throw new Error('No response from server');

                if (visibilityValue === 'draft') {
                    autosaveStatus.textContent = 'Draft saved. Redirecting…';
                    window.location.href = 'index.php?url=my-profile&tab=drafts';
                } else if (result.data && result.data.slug) {
                    autosaveStatus.textContent = 'Published! Redirecting…';
                    window.location.href = 'index.php?url=article&slug=' + encodeURIComponent(result.data.slug);
                } else {
                    autosaveStatus.textContent = 'Saved. Redirecting…';
                    window.location.href = 'index.php?url=my-profile';
                }
            } catch (err) {
                autosaveStatus.textContent = 'Error: ' + err.message;
                console.error('Publish error:', err);
            } finally {
                submitting = false;
                if (publishBtn) publishBtn.disabled = false;
            }
        });
    }

    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            autosaveStatus.textContent = 'Saving draft…';
            try {
                const result = await submitArticle('draft');
                if (!result) throw new Error('No response from server');
                autosaveStatus.textContent = 'Draft saved. Redirecting…';
                window.location.href = 'index.php?url=my-profile&tab=drafts';
            } catch (err) {
                autosaveStatus.textContent = 'Error: ' + err.message;
                console.error('Draft save error:', err);
            }
        });
    }

    // Init
    const local = loadLocalDraft();
    if (!local && editingId) {
        loadServerDraft();
    }
    if (editingId && visibilityInput) {
        // Default to draft when editing unless server/local overrides later
        visibilityInput.value = visibilityInput.value || 'draft';
    }
})();
