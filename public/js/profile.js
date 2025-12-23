// Dynamic Profile Page
(function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '/';

    const qs = (sel, el=document) => el.querySelector(sel);
    const qsa = (sel, el=document) => Array.from(el.querySelectorAll(sel));

    function getProfileId() {
        const p = new URLSearchParams(window.location.search);
        const id = parseInt(p.get('id') || '', 10);
        return Number.isFinite(id) && id > 0 ? id : 0;
    }

    async function fetchJSON(url, opts) {
        const res = await fetch(url, { credentials: 'same-origin', ...opts });
        if (!res.ok) {
            let msg = res.statusText;
            try { const e = await res.json(); if (e && e.error) msg = e.error; } catch {}
            throw new Error(msg);
        }
        return res.json();
    }

    function goToArticle(slug) {
        if (!slug) return;
        window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
    }

    function articleCard(a) {
        const img = a.featured_image ? a.featured_image : 'article-placeholder.jpg';
        return `
        <article class="article-card" data-slug="${a.slug ? String(a.slug).replace(/"/g, '&quot;') : ''}" role="link" tabindex="0">
          <div class="article-image">
            <img src="public/images/${img}" alt="Article">
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

    function setFollowButton(btn, isFollowing) {
        if (!btn) return;
        btn.style.display = '';
        if (isFollowing) {
            btn.innerHTML = '<i class="fas fa-check"></i> Following';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
            btn.setAttribute('aria-pressed', 'true');
        } else {
            btn.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary');
            btn.setAttribute('aria-pressed', 'false');
        }
    }

    async function loadProfile() {
        const profileId = getProfileId();
        if (!profileId) {
            // If no id provided, fallback to my-profile
            window.location.href = `${base}index.php?url=my-profile`;
            return;
        }

        const data = await fetchJSON(`${base}index.php?url=api-users&id=${encodeURIComponent(profileId)}`);
        const u = data.user;

        const nameEl = qs('#profileName');
        const usernameEl = qs('#profileUsername');
        const bioEl = qs('#profileBio');

        if (nameEl) nameEl.textContent = u.full_name ? u.full_name : (u.username || 'User');
        if (usernameEl) usernameEl.textContent = u.username ? ('@' + u.username) : '';
        if (bioEl) bioEl.textContent = u.bio || '';

        // Avatar
        const avatarEl = qs('#profileAvatar');
        if (avatarEl && u.profile_image) {
            avatarEl.src = 'public/images/' + u.profile_image;
        }

        // Cover
        const coverEl = qs('#coverImage');
        if (coverEl && u.cover_image) {
            coverEl.src = 'public/images/' + u.cover_image;
        }

        // Joined
        const joinedEl = qs('#joinedText');
        if (joinedEl && u.created_at) {
            joinedEl.textContent = 'Member since ' + new Date(u.created_at).toLocaleDateString();
        }

        // About
        const aboutEl = qs('#aboutText');
        if (aboutEl) {
            aboutEl.textContent = u.bio || '—';
        }

        // Stats
        const s = data.stats || {};
        const statArticles = qs('#statArticles');
        const statFollowers = qs('#statFollowers');
        const statFollowing = qs('#statFollowing');
        const statLikes = qs('#statLikes');
        if (statArticles) statArticles.textContent = s.articles ?? 0;
        if (statFollowers) statFollowers.textContent = s.followers ?? 0;
        if (statFollowing) statFollowing.textContent = s.following ?? 0;
        if (statLikes) statLikes.textContent = s.likes ?? 0;

        // Articles
        const grid = qs('#profileArticlesContainer');
        if (grid) {
            const articles = Array.isArray(data.articles) ? data.articles : [];
            grid.innerHTML = articles.map(articleCard).join('') || '<p class="empty-state"><i class="fas fa-newspaper"></i> <span>No articles yet</span></p>';
        }

        // Follow button
        const followBtn = qs('#followBtn');
        if (followBtn) {
            // Only show for non-own profile; server already decides own/other in PHP, but keep safe.
            setFollowButton(followBtn, !!data.is_following);
            followBtn.onclick = async () => {
                followBtn.disabled = true;
                try {
                    const fd = new FormData();
                    fd.set('action', 'follow');
                    fd.set('target_id', String(profileId));
                    const res = await fetchJSON(`${base}index.php?url=api-social`, {
                        method: 'POST',
                        body: fd
                    });

                    // api-social returns { status: 'followed' | 'unfollowed' }
                    const nowFollowing = (res.status === 'followed');
                    setFollowButton(followBtn, nowFollowing);

                    // Update follower count immediately
                    const followersEl = qs('#statFollowers');
                    if (followersEl) {
                        const current = parseInt(followersEl.textContent || '0', 10);
                        const safeCurrent = Number.isFinite(current) ? current : 0;
                        followersEl.textContent = Math.max(0, safeCurrent + (nowFollowing ? 1 : -1));
                    }
                } catch (err) {
                    alert(err.message);
                } finally {
                    followBtn.disabled = false;
                }
            };
        }
    }

    function setupTabs() {
        const tabBtns = qsa('.tab-btn');
        const tabContents = qsa('.tab-content');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const panel = qs(`#${targetTab}-tab`);
                if (panel) panel.classList.add('active');
            });
        });
    }

    function setupArticleNavigation() {
        const grid = qs('#profileArticlesContainer');
        if (!grid) return;
        grid.addEventListener('click', (e) => {
            const card = e.target.closest('.article-card');
            if (!card) return;
            const slug = card.getAttribute('data-slug');
            if (slug) goToArticle(slug);
        });
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') return;
            const target = document.activeElement;
            if (!target || !target.classList || !target.classList.contains('article-card')) return;
            const slug = target.getAttribute('data-slug');
            if (slug) goToArticle(slug);
        });
    }

    (async function init() {
        setupTabs();
        setupArticleNavigation();
        await loadProfile();
    })();
})();
