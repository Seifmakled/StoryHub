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
        followers: qs('#statFollowers'),
        following: qs('#statFollowing'),
        likes: qs('#statLikes'),
        saved: qs('#statSaved'),
        comments: qs('#statComments')
    };

    const containers = {
        articles: qs('#myArticlesContainer'),
        drafts: qs('#draftsContainer'),
        saved: qs('#savedContainer'),
        liked: qs('#likedContainer'),
        comments: qs('#commentsContainer')
    };

    const counts = {
        articles: qs('#countArticles'),
        drafts: qs('#countDrafts')
    };

    async function fetchJSON(url, opts) {
        const res = await fetch(url, { credentials: 'same-origin', ...opts });
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
        if (stats.followers) stats.followers.textContent = data.counts.followers ?? 0;
        if (stats.following) stats.following.textContent = data.counts.following ?? 0;
        stats.likes.textContent = data.counts.likes;
        stats.saved.textContent = data.counts.saved;
        stats.comments.textContent = data.counts.comments;

        // Pre-fill settings form
        qs('#fullName').value = u.full_name || '';
        qs('#email').value = u.email || '';
        qs('#bio').value = u.bio || '';
    }

    function goToArticle(slug) {
        if (!slug) return;
        window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
    }

    function goToDraftEdit(id) {
        if (!id) return;
        window.location.href = `${base}index.php?url=write&edit=${encodeURIComponent(id)}`;
    }

        function articleCard(a, owned=true) {
                const img = a.featured_image ? a.featured_image : 'article-placeholder.jpg';
                const isDraft = (a.is_published === 0 || a.is_published === '0');
                const statusBadge = isDraft ? '<span class="status-badge draft">Draft</span>' : '';
                const actions = owned ? `
            <div class="article-menu">
              <button class="btn-menu"><i class="fas fa-ellipsis-v"></i></button>
              <div class="menu-dropdown">
                <a href="index.php?url=write&edit=${encodeURIComponent(a.id)}"><i class="fas fa-edit"></i> Edit</a>
                <a href="#" data-action="delete" data-id="${a.id}"><i class="fas fa-trash"></i> Delete</a>
              </div>
            </div>` : '';
        return `
                                <article class="article-card" data-id="${a.id}" data-slug="${a.slug ? String(a.slug).replace(/"/g, '&quot;') : ''}" data-published="${isDraft ? '0' : '1'}" role="link" tabindex="0">
                    <div class="article-image">
                        <img src="public/images/${img}" alt="Article">
                        ${statusBadge}
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
                <div class="article-list-item" data-id="${a.id}" data-slug="${a.slug ? String(a.slug).replace(/"/g, '&quot;') : ''}" role="link" tabindex="0">
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
                const slug = c.article_slug ? String(c.article_slug) : '';
                const safeSlug = slug.replace(/"/g, '&quot;');
                const title = c.article_title || 'Article';
                const href = slug ? `${base}index.php?url=article&slug=${encodeURIComponent(slug)}` : '#';
                const excerpt = (c.article_excerpt || '').trim();
                return `
                                <div class="comment-item" data-article-id="${c.article_id || ''}" data-slug="${safeSlug}" role="link" tabindex="0">
                    <div class="comment-meta">
                        <span>Commented on: <a class="comment-article-link" href="${href}">${title}</a></span>
                        <span>${new Date(c.created_at).toLocaleString()}</span>
                    </div>
                    <p>${c.content || ''}</p>
                    ${excerpt ? `<div class="comment-article-excerpt">${excerpt}</div>` : ''}
                </div>`;
        }

    async function loadArticles() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=articles');
        containers.articles.innerHTML = data.data.map(a => articleCard(a, true)).join('') || '<p class="empty-state"><i class="fas fa-newspaper"></i> <span>No articles yet</span></p>';
        if (counts.articles) counts.articles.textContent = data.data.length;
    }

    async function loadDrafts() {
        const data = await fetchJSON(base + 'index.php?url=api-me&section=drafts');
        containers.drafts.innerHTML = data.data.map(a => articleCard(a, true)).join('') || '<p class="empty-state"><i class="fas fa-file-alt"></i> <span>No drafts yet</span></p>';
        if (counts.drafts) counts.drafts.textContent = data.data.length;
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

    function setupNavigation() {
        // My Articles grid
        containers.articles.addEventListener('click', (e) => {
            // Ignore menu interactions
            if (e.target.closest('.article-menu') || e.target.closest('.btn-menu') || e.target.closest('.menu-dropdown') || e.target.closest('[data-action="delete"]')) {
                return;
            }
            const card = e.target.closest('.article-card');
            if (!card) return;
            const slug = card.getAttribute('data-slug');
            if (slug) goToArticle(slug);
        });

        // Drafts grid
        containers.drafts.addEventListener('click', (e) => {
            if (e.target.closest('.article-menu') || e.target.closest('.btn-menu') || e.target.closest('.menu-dropdown') || e.target.closest('[data-action="delete"]')) {
                return;
            }
            const card = e.target.closest('.article-card');
            if (!card) return;
            const published = card.getAttribute('data-published');
            const id = card.getAttribute('data-id');
            const slug = card.getAttribute('data-slug');
            if (published === '0' && id) {
                goToDraftEdit(id);
            } else if (slug) {
                goToArticle(slug);
            }
        });

        // Saved + Liked lists
        const listClick = (e) => {
            const item = e.target.closest('.article-list-item');
            if (!item) return;
            const slug = item.getAttribute('data-slug');
            if (slug) goToArticle(slug);
        };
        containers.saved.addEventListener('click', listClick);
        containers.liked.addEventListener('click', listClick);

        // Comments list
        containers.comments.addEventListener('click', (e) => {
            // Let explicit links behave like normal links
            if (e.target.closest('a')) return;
            const item = e.target.closest('.comment-item');
            if (!item) return;
            const slug = item.getAttribute('data-slug');
            if (slug) goToArticle(slug);
        });

        // Keyboard accessibility (Enter to open)
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') return;
            const target = document.activeElement;
            if (!target) return;
            if (target.classList && (target.classList.contains('article-card') || target.classList.contains('article-list-item') || target.classList.contains('comment-item'))) {
                const slug = target.getAttribute('data-slug');
                const published = target.getAttribute('data-published');
                const id = target.getAttribute('data-id');
                if (published === '0' && id) {
                    goToDraftEdit(id);
                } else if (slug) {
                    goToArticle(slug);
                }
            }
        });
    }

    const VALID_TABS = ['articles', 'drafts', 'saved', 'liked', 'comments', 'settings'];

    function setActiveTab(tab) {
        qsa('.tab-btn').forEach(btn => btn.classList.toggle('active', btn.getAttribute('data-tab') === tab));
        qsa('.tab-content').forEach(tc => tc.classList.toggle('active', tc.id === `${tab}-tab`));
    }

    async function activateTab(tab) {
        const chosen = VALID_TABS.includes(tab) ? tab : 'articles';
        setActiveTab(chosen);
        if (chosen === 'articles') await loadArticles();
        if (chosen === 'drafts') await loadDrafts();
        if (chosen === 'saved') await loadSaved();
        if (chosen === 'liked') await loadLiked();
        if (chosen === 'comments') await loadComments();
    }

    // Tabs
    function setupTabs() {
        qsa('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.getAttribute('data-tab');
                activateTab(tab);
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
                await loadOverview();
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
        setupNavigation();
        setupProfileForm();
        await loadOverview();
        const params = new URLSearchParams(window.location.search);
        const initialTab = params.get('tab') || 'articles';
        await activateTab(initialTab);
        if (initialTab !== 'drafts') {
            // Preload drafts to get accurate badge count even before opening the tab
            await loadDrafts();
        }
    })();
})();
