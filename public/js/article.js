// Article page renderer
(function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '';

    const qs = (id) => document.getElementById(id);
    const coverEl = qs('articleCover');
    const titleEl = qs('articleTitle');
    const excerptEl = qs('articleExcerpt');
    const categoryEl = qs('articleCategory');
    const readingEl = qs('articleReading');
    const authorNameEl = qs('authorName');
    const authorMetaEl = qs('authorMeta');
    const authorAvatarEl = qs('authorAvatar');
    const tagsEl = qs('articleTags');
    const bodyEl = qs('articleBody');
    const likeBtn = qs('likeBtn');
    const saveBtn = qs('saveBtn');
    const followBtn = qs('followBtn');
    const viewerId = (typeof window.CURRENT_USER_ID !== 'undefined') ? (Number(window.CURRENT_USER_ID) || null) : null;
    const likeCountEl = qs('likeCount');
    const saveLabel = qs('saveLabel');
    const commentsList = qs('commentsList');
    const commentForm = document.getElementById('commentForm');
    const commentInput = document.getElementById('commentInput');
    const commentStatus = document.getElementById('commentStatus');

    const params = new URLSearchParams(window.location.search);
    const slug = params.get('slug');
    const id = params.get('id');

    let currentArticleId = null;
    let authorId = null;

    const escapeHTML = (str) => {
        return (str || '').replace(/[&<>"]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    };

    const formatBody = (text) => {
        const safe = escapeHTML(text || '');
        const blocks = safe.split(/\n{2,}/).map(p => p.trim()).filter(Boolean);
        if (blocks.length === 0) return '<p>This story has no content yet.</p>';
        return blocks.map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`).join('');
    };

    const readingTime = (text) => {
        const words = (text || '').split(/\s+/).filter(Boolean).length;
        return Math.max(1, Math.ceil(words / 220)) + ' min read';
    };

    async function loadArticle() {
        if (!slug && !id) {
            bodyEl.innerHTML = '<div class="empty-state">Article not found.</div>';
            return;
        }
        try {
            const key = slug ? `slug=${encodeURIComponent(slug)}` : `id=${encodeURIComponent(id)}`;
            const res = await fetch(`${base}index.php?url=api-articles&${key}`);
            const data = await res.json();
            if (!res.ok || !data.data) throw new Error(data.error || 'Not found');
            const a = data.data;

            currentArticleId = a.id;
            authorId = Number(a.user_id) || null;

            document.title = (a.title || 'Story') + ' - StoryHub';
            titleEl.textContent = a.title || 'Untitled';
            excerptEl.textContent = a.excerpt || '';
            categoryEl.textContent = a.category || 'Story';
            readingEl.textContent = readingTime(a.content || a.excerpt || '');

            const cover = a.featured_image ? `${base}public/images/${a.featured_image}` : `${base}public/images/article-placeholder.jpg`;
            coverEl.style.backgroundImage = `url('${cover}')`;

            const authorName = a.full_name || a.username || 'Author';
            authorNameEl.textContent = authorName;
            const date = a.created_at ? new Date(a.created_at).toLocaleDateString() : '';
            authorMetaEl.textContent = date ? `${date}` : '';
            authorAvatarEl.src = `${base}public/images/${a.profile_image || 'default-avatar.jpg'}`;

            if (likeCountEl) likeCountEl.textContent = a.likes_count || 0;
            if (likeBtn && a.is_liked) likeBtn.classList.add('active');
            if (saveBtn && a.is_saved) saveBtn.classList.add('active');
            if (saveLabel) saveLabel.textContent = a.is_saved ? 'Saved' : 'Save';
            if (followBtn) {
                const following = !!a.is_following_author;
                if (viewerId && authorId && viewerId === authorId) {
                    followBtn.style.display = 'none';
                } else {
                    followBtn.style.display = '';
                    followBtn.classList.toggle('active', following);
                    followBtn.textContent = following ? 'Following' : 'Follow';
                }
            }

            if (tagsEl) {
                const tags = (a.tags || '').split(',').map(t => t.trim()).filter(Boolean);
                tagsEl.innerHTML = tags.map(tag => `<span class="tag">${escapeHTML(tag)}</span>`).join('');
            }

            bodyEl.innerHTML = formatBody(a.content || '');

            loadComments();
        } catch (err) {
            console.error(err);
            titleEl.textContent = 'Article not found';
            bodyEl.innerHTML = '<div class="empty-state">Could not load this article.</div>';
        }
    }

    async function loadComments() {
        if (!currentArticleId || !commentsList) return;
        commentsList.innerHTML = '<div class="empty-state loading">Loading comments...</div>';
        try {
            const res = await fetch(`${base}index.php?url=api-social&article_id=${encodeURIComponent(currentArticleId)}`);
            const data = await res.json();
            const list = data.data || [];
            if (list.length === 0) {
                commentsList.innerHTML = '<div class="empty-state">No comments yet.</div>';
                return;
            }
            commentsList.innerHTML = list.map(c => {
                const avatar = c.profile_image ? `${base}public/images/${c.profile_image}` : `${base}public/images/default-avatar.jpg`;
                const name = c.full_name || c.username || 'User';
                const date = c.created_at ? new Date(c.created_at).toLocaleString() : '';
                return `
                <div class="comment-item">
                    <div class="comment-head">
                        <img src="${avatar}" alt="${name}">
                        <div>
                            <div class="author-name">${name}</div>
                            <div class="comment-meta">${date}</div>
                        </div>
                    </div>
                    <div class="comment-body">${escapeHTML(c.content || '')}</div>
                </div>`;
            }).join('');
        } catch (err) {
            commentsList.innerHTML = '<div class="empty-state">Could not load comments.</div>';
        }
    }

    function pop(el) {
        if (!el) return;
        el.classList.remove('pop');
        // restart animation
        void el.offsetWidth;
        el.classList.add('pop');
        window.setTimeout(() => el.classList.remove('pop'), 360);
    }

    async function postAction(action, payload) {
        const fd = new FormData();
        fd.append('action', action);
        Object.entries(payload || {}).forEach(([k, v]) => fd.append(k, v));
        const res = await fetch(`${base}index.php?url=api-social`, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.error || res.statusText || 'Failed');
        }
        return res.json();
    }

    function toggleBusy(btn, on) {
        if (!btn) return;
        btn.disabled = on;
        btn.classList.toggle('busy', on);
    }

    function wireInteractions() {
        if (likeBtn) {
            likeBtn.addEventListener('click', async () => {
                if (!currentArticleId) return;
                toggleBusy(likeBtn, true);
                try {
                    const data = await postAction('like', { article_id: currentArticleId });
                    const liked = data.status === 'liked';
                    likeBtn.classList.toggle('active', liked);
                    likeBtn.setAttribute('aria-pressed', liked ? 'true' : 'false');
                    const count = parseInt(likeCountEl.textContent || '0', 10) || 0;
                    likeCountEl.textContent = liked ? count + 1 : Math.max(0, count - 1);
                    pop(likeBtn);
                } catch (err) {
                    alert('Sign in to like stories.');
                } finally {
                    toggleBusy(likeBtn, false);
                }
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', async () => {
                if (!currentArticleId) return;
                toggleBusy(saveBtn, true);
                try {
                    const data = await postAction('save', { article_id: currentArticleId });
                    const saved = data.status === 'saved';
                    saveBtn.classList.toggle('active', saved);
                    saveBtn.setAttribute('aria-pressed', saved ? 'true' : 'false');
                    if (saveLabel) saveLabel.textContent = saved ? 'Saved' : 'Save';
                    pop(saveBtn);
                } catch (err) {
                    alert('Sign in to save stories.');
                } finally {
                    toggleBusy(saveBtn, false);
                }
            });
        }

        if (followBtn) {
            followBtn.addEventListener('click', async () => {
                if (!authorId) return;
                toggleBusy(followBtn, true);
                try {
                    const data = await postAction('follow', { target_id: authorId });
                    const following = data.status === 'followed';
                    followBtn.classList.toggle('active', following);
                    followBtn.textContent = following ? 'Following' : 'Follow';
                    pop(followBtn);
                } catch (err) {
                    alert('Sign in to follow authors.');
                } finally {
                    toggleBusy(followBtn, false);
                }
            });
        }

        if (commentForm) {
            commentForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const content = (commentInput?.value || '').trim();
                if (content.length < 2) {
                    if (commentStatus) commentStatus.textContent = 'Add a longer comment.';
                    return;
                }
                if (commentStatus) commentStatus.textContent = 'Posting...';
                try {
                    const res = await postAction('comment', { article_id: currentArticleId, content });
                    commentInput.value = '';
                    if (commentStatus) commentStatus.textContent = 'Posted!';
                    // prepend new comment without full reload if returned
                    if (res && res.data) {
                        const c = res.data;
                        const avatar = c.profile_image ? `${base}public/images/${c.profile_image}` : `${base}public/images/default-avatar.jpg`;
                        const name = c.full_name || c.username || 'User';
                        const date = c.created_at ? new Date(c.created_at).toLocaleString() : '';
                        const newHtml = `
                        <div class="comment-item">
                            <div class="comment-head">
                                <img src="${avatar}" alt="${name}">
                                <div>
                                    <div class="author-name">${name}</div>
                                    <div class="comment-meta">${date}</div>
                                </div>
                            </div>
                            <div class="comment-body">${escapeHTML(c.content || '')}</div>
                        </div>`;
                        commentsList.insertAdjacentHTML('afterbegin', newHtml);
                    } else {
                        loadComments();
                    }
                    // stay on this page, scroll to comments
                    const anchor = document.getElementById('comments');
                    if (anchor) anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (err) {
                    if (commentStatus) commentStatus.textContent = 'Sign in to comment.';
                }
            });
        }
    }

    loadArticle();
    wireInteractions();
})();
