// Article page renderer
(function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '';

    const qs = (id) => document.getElementById(id);
    const hero = qs('articleHero');
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

    const params = new URLSearchParams(window.location.search);
    const slug = params.get('slug');
    const id = params.get('id');

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

            if (tagsEl) {
                const tags = (a.tags || '').split(',').map(t => t.trim()).filter(Boolean);
                tagsEl.innerHTML = tags.map(tag => `<span class="tag">${escapeHTML(tag)}</span>`).join('');
            }

            bodyEl.innerHTML = formatBody(a.content || '');
        } catch (err) {
            console.error(err);
            titleEl.textContent = 'Article not found';
            bodyEl.innerHTML = '<div class="empty-state">Could not load this article.</div>';
        }
    }

    loadArticle();
})();
