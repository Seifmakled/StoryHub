// My Profile client-side logic
(function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '/';

    const qs = (sel, el=document) => el.querySelector(sel);
    const qsa = (sel, el=document) => Array.from(el.querySelectorAll(sel));

    // Elements
    const nameEl = qs('#profileName');
    const usernameEl = qs('#profileUsername');
    const bioWrap = qs('#profileBio p');
    const avatarEl = qs('#profileAvatar');
    const stats = {
        articles: qs('#statArticles'),
        likes: qs('#statLikes'),
        saved: qs('#statSaved'),
        comments: qs('#statComments')
    };

    const containers = {
        articles: qs('#myArticlesContainer'),
        saved: qs('#savedContainer'),
        liked: qs('#likedContainer'),
        comments: qs('#commentsContainer')
    };

    async function fetchJSON(url, opts) {
        const res = await fetch(url, opts);
        if (!res.ok) {
            let msg = res.statusText;
            try { const e = await res.json(); if (e && e.error) msg = e.error; } catch {}
            throw new Error(msg);
        }
        return res.json();
    }

    async function loadOverview() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=overview');
        const u = data.user;
        nameEl.textContent = u.full_name ? u.full_name : (u.username || 'User');
        usernameEl.textContent = u.username ? ('@' + u.username) : '';
        bioWrap.textContent = u.bio || '';
        if (u.profile_image) {
            avatarEl.src = 'public/images/' + u.profile_image;
        }
        stats.articles.textContent = data.counts.articles;
        stats.likes.textContent = data.counts.likes;
        stats.saved.textContent = data.counts.saved;
        stats.comments.textContent = data.counts.comments;

        // Pre-fill settings form
        qs('#fullName').value = u.full_name || '';
        qs('#email').value = u.email || '';
        qs('#bio').value = u.bio || '';
    }

    function articleCard(a, owned=true) {
        const img = a.featured_image ? a.featured_image : 'article-placeholder.jpg';
        const actions = owned ? `
            <div class="article-menu">
              <button class="btn-menu"><i class="fas fa-ellipsis-v"></i></button>
              <div class="menu-dropdown">
                <a href="index.php?url=write&edit=${encodeURIComponent(a.id)}"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" data-action="delete" data-id="${a.id}"><i class="fas fa-trash"></i> Delete</a>
              </div>
            </div>` : '';
        return `
        <article class="article-card" data-id="${a.id}">
          <div class="article-image">
            <img src="public/images/${img}" alt="Article">
            ${actions}
          </div>
          <div class="article-content">
            <span class="article-category">${a.category || ''}</span>
            <h3>${a.title || ''}</h3>
            <p>${a.excerpt || ''}</p>
            <div class="article-meta">
              <span class="article-date"><i class="far fa-calendar"></i> ${new Date(a.created_at).toLocaleDateString()}</span>
              <div class="article-stats">
                <span><i class="fas fa-eye"></i> ${a.views || 0}</span>
                <span><i class="fas fa-heart"></i> ${a.likes_count || 0}</span>
              </div>
            </div>
          </div>
        </article>`;
    }

    function articleListItem(a) {
        const img = a.featured_image ? a.featured_image : 'article-placeholder.jpg';
        return `
        <div class="article-list-item">
          <img src="public/images/${img}" alt="Article">
          <div class="article-list-content">
            <h4>${a.title || ''}</h4>
            <p>${a.excerpt || ''}</p>
            <div class="article-list-meta">
              <span>${new Date(a.created_at).toLocaleDateString()}</span>
            </div>
          </div>
        </div>`;
    }

    function commentItem(c) {
        return `
        <div class="comment-item">
          <div class="comment-meta">
            <strong>${c.article_title || 'Article'}</strong>
            <span>${new Date(c.created_at).toLocaleString()}</span>
          </div>
          <p>${c.content || ''}</p>
        </div>`;
    }

    async function loadArticles() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=articles');
        containers.articles.innerHTML = data.data.map(a => articleCard(a, true)).join('') || '<p class="empty-state"><i class="fas fa-newspaper"></i> <span>No articles yet</span></p>';
    }
    async function loadSaved() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=saved');
        containers.saved.innerHTML = data.data.map(a => articleListItem(a)).join('') || '<p class="empty-state"><i class="fas fa-bookmark"></i> <span>No saved articles</span></p>';
    }
    async function loadLiked() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=liked');
        containers.liked.innerHTML = data.data.map(a => articleListItem(a)).join('') || '<p class="empty-state"><i class="fas fa-heart"></i> <span>No liked articles</span></p>';
    }
    async function loadComments() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=comments');
        containers.comments.innerHTML = data.data.map(c => commentItem(c)).join('') || '<p class="empty-state"><i class="fas fa-comments"></i> <span>No comments yet</span></p>';
    }

    // Tabs
    function setupTabs() {
        qsa('.tab-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const tab = btn.getAttribute('data-tab');
                qsa('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                qsa('.tab-content').forEach(tc => tc.classList.remove('active'));
                const active = qs('#' + tab + '-tab');
                if (active) active.classList.add('active');

                if (tab === 'articles') await loadArticles();
                if (tab === 'saved') await loadSaved();
                if (tab === 'liked') await loadLiked();
                if (tab === 'comments') await loadComments();
            });
        });
    }

    // Delete own article via event delegation
    function setupDelete() {
        containers.articles.addEventListener('click', async (e) => {
            const a = e.target.closest('[data-action="delete"]');
            if (!a) return;
            e.preventDefault();
            const id = a.getAttribute('data-id');
            if (!id) return;
            if (!confirm('Delete this article?')) return;
            try {
                const res = await fetch(base + 'index.php?url=api-articles&id=' + encodeURIComponent(id), { method: 'DELETE' });
                if (!res.ok) {
                    const t = await res.text();
                    throw new Error(t || 'Failed to delete');
                }
                await loadArticles();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });
    }

    function setupProfileForm() {
        const form = qs('#profileForm');
        const status = qs('#profileSaveStatus');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            status.textContent = 'Saving...';
            status.style.color = '';
            const payload = {
                full_name: qs('#fullName').value.trim(),
                email: qs('#email').value.trim(),
                bio: qs('#bio').value.trim()
            };
            try {
                const res = await fetch(base + 'index.php?url=api-me', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.error || res.statusText);
                }
                status.textContent = 'Saved!';
                status.style.color = 'green';
                await loadOverview();
            } catch (err) {
                status.textContent = 'Error: ' + err.message;
                status.style.color = 'crimson';
            }
        });
    }

    // Init
    (async function init() {
        setupTabs();
        setupDelete();
        setupProfileForm();
        await loadOverview();
        await loadArticles(); // default active tab
    })();
})();
