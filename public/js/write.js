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

    const STORAGE_KEY = 'storyhub_write_draft';

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

    const loadDraft = () => {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (!stored) return;
            const draft = JSON.parse(stored);
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
            autosaveStatus.textContent = 'Draft loaded';
        } catch (err) {
            autosaveStatus.textContent = 'Could not load draft';
        }
    };

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

    if (form) {
        let submitting = false;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (submitting) return;

            const title = titleInput?.value.trim() || '';
            const body = bodyInput?.value.trim() || '';
            const visibility = visibilityInput?.value || 'public';

            if (title.length < 3) {
                alert('Please add a title (min 3 characters).');
                return;
            }
            if (body.length < 20) {
                alert('Story body is too short (min 20 characters).');
                return;
            }

            submitting = true;
            autosaveStatus.textContent = 'Publishing…';
            const publishBtn = document.getElementById('publishBtn');
            if (publishBtn) publishBtn.disabled = true;

            try {
                const fd = new FormData();
                fd.append('title', title);
                fd.append('subtitle', subtitleInput?.value || '');
                fd.append('body', body);
                fd.append('tags', tagsInput?.value || '');
                fd.append('visibility', visibility);

                if (coverInput && coverInput.files && coverInput.files[0]) {
                    fd.append('cover', coverInput.files[0]);
                }

                const res = await fetch('index.php?url=api-articles', {
                    method: 'POST',
                    body: fd
                });

                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.error || res.statusText || 'Failed to publish');
                }

                localStorage.removeItem(STORAGE_KEY);
                autosaveStatus.textContent = data.message || 'Saved';
                alert('Story saved! Redirecting to your profile.');
                window.location.href = 'index.php?url=my-profile';
            } catch (err) {
                autosaveStatus.textContent = 'Error: ' + err.message;
                alert('Could not publish: ' + err.message);
            } finally {
                submitting = false;
                if (publishBtn) publishBtn.disabled = false;
            }
        });
    }

    loadDraft();
})();
