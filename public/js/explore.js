// Explore Page JavaScript (fetch real articles)
document.addEventListener('DOMContentLoaded', function() {
    const exploreSearch = document.getElementById('exploreSearch');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const viewBtns = document.querySelectorAll('.view-btn');
    const articlesContainer = document.getElementById('articlesContainer');
    const resultsCount = document.getElementById('resultsCount');

    let articles = [];
    let total = 0;
    let activeFilter = 'all';
    let activeCategory = 'all';
    let activeSort = 'latest';
    const GENRES = ['technology','design','business','health','travel','food','lifestyle','entertainment'];

    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '';

    const readingTime = (text) => {
        const words = (text || '').split(/\s+/).filter(Boolean).length;
        return Math.max(1, Math.ceil(words / 200)) + ' min';
    };

    const renderCards = (list) => {
        if (!articlesContainer) return;
        if (!list || list.length === 0) {
            articlesContainer.innerHTML = '<div class="empty-state">No articles yet.</div>';
            if (resultsCount) resultsCount.textContent = 'No articles found';
            return;
        }

        const renderCard = (a) => {
            const cover = a.featured_image ? `${base}public/images/${a.featured_image}` : `${base}public/images/article-placeholder.jpg`;
            const authorImg = a.profile_image ? `${base}public/images/${a.profile_image}` : `${base}public/images/default-avatar.jpg`;
            const authorName = a.full_name || a.username || 'Author';
            const cat = a.category || 'General';
            const mins = readingTime(a.excerpt);
            const date = a.created_at ? new Date(a.created_at).toLocaleDateString() : '';
            return `
            <article class="explore-card" data-slug="${a.slug}" data-id="${a.id}">
                <div class="card-image">
                    <img src="${cover}" alt="Article">
                    <div class="card-overlay">
                        <button class="btn-save" title="Save article">
                            <i class="far fa-bookmark"></i>
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-meta">
                        <span class="category">${cat}</span>
                        <span class="reading-time"><i class="far fa-clock"></i> ${mins}</span>
                    </div>
                    <h3>${a.title || ''}</h3>
                    <p>${a.excerpt || ''}</p>
                    <div class="card-footer">
                        <div class="author-info">
                            <img src="${authorImg}" alt="Author">
                            <div class="author-details">
                                <span class="author-name">${authorName}</span>
                                <span class="publish-date">${date}</span>
                            </div>
                        </div>
                        <div class="card-stats">
                            <span title="Views"><i class="fas fa-eye"></i> ${a.views || 0}</span>
                            <span title="Likes"><i class="fas fa-heart"></i> ${a.likes_count || 0}</span>
                            <span title="Comments"><i class="fas fa-comment"></i> ${a.comments_count || 0}</span>
                        </div>
                    </div>
                </div>
            </article>`;
        };

        let html = '';
        if (activeCategory === 'all') {
            GENRES.forEach((g) => {
                const group = list.filter(a => (a.category || '').toLowerCase() === g);
                if (!group.length) return;
                const cards = group.map(renderCard).join('');
                html += `<section class="genre-section"><div class="genre-header"><h2>${g.charAt(0).toUpperCase() + g.slice(1)}</h2></div><div class="articles-container grid-view">${cards}</div></section>`;
            });
            const leftovers = list.filter(a => !GENRES.includes((a.category || '').toLowerCase()));
            if (leftovers.length) {
                html += `<section class="genre-section"><div class="genre-header"><h2>Other</h2></div><div class="articles-container grid-view">${leftovers.map(renderCard).join('')}</div></section>`;
            }
        } else {
            html = list.map(renderCard).join('');
        }

        articlesContainer.innerHTML = html;
        if (resultsCount) {
            const showing = list.length;
            resultsCount.textContent = `Showing ${showing} of ${total || showing} articles`;
        }

        articlesContainer.querySelectorAll('.explore-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.closest('.btn-save')) return;
                const slug = card.getAttribute('data-slug');
                if (slug) {
                    window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
                }
            });
        });

        const postAction = async (payload) => {
            const fd = new FormData();
            Object.entries(payload).forEach(([k, v]) => fd.append(k, v));
            const res = await fetch(`${base}index.php?url=api-social`, { method: 'POST', body: fd });
            if (!res.ok) throw new Error('auth');
            return res.json();
        };

        articlesContainer.querySelectorAll('.btn-save').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const card = btn.closest('.explore-card');
                const slug = card?.getAttribute('data-slug');
                const articleId = card?.getAttribute('data-id');
                try {
                    await postAction({ action: 'save', article_id: articleId || '', slug: slug || '' });
                    btn.classList.toggle('saved');
                    const icon = btn.querySelector('i');
                    icon.classList.toggle('far', !btn.classList.contains('saved'));
                    icon.classList.toggle('fas', btn.classList.contains('saved'));
                } catch (err) {
                    alert('Sign in to save stories.');
                }
            });
        });
    };

    const applyFilters = () => {
        let list = [...articles];
        const q = (exploreSearch?.value || '').trim().toLowerCase();
        if (q) {
            list = list.filter(a =>
                (a.title && a.title.toLowerCase().includes(q)) ||
                (a.excerpt && a.excerpt.toLowerCase().includes(q)) ||
                (a.tags && a.tags.toLowerCase().includes(q)) ||
                (a.username && a.username.toLowerCase().includes(q))
            );
        }

        if (activeCategory !== 'all') {
            list = list.filter(a => (a.category || 'general').toLowerCase() === activeCategory);
        }

        if (activeFilter === 'featured') {
            list = list.filter(a => a.is_featured === 1 || (a.tags && a.tags.toLowerCase().includes('featured')));
        }
        if (activeFilter === 'trending' || activeFilter === 'popular') {
            list = list.sort((a, b) => (b.likes_count || 0) - (a.likes_count || 0));
        }
        if (activeFilter === 'recent') {
            activeSort = 'latest';
        }

        if (activeSort === 'latest') {
            list = list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        } else if (activeSort === 'oldest') {
            list = list.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        } else if (activeSort === 'views') {
            list = list.sort((a, b) => (b.views || 0) - (a.views || 0));
        } else if (activeSort === 'likes') {
            list = list.sort((a, b) => (b.likes_count || 0) - (a.likes_count || 0));
        }

        renderCards(list);
    };

    const fetchArticles = async () => {
        if (!articlesContainer) return;
        articlesContainer.innerHTML = '<div class="empty-state loading">Loading articles...</div>';
        try {
            const res = await fetch(`${base}index.php?url=api-articles&limit=30`);
            const data = await res.json();
            articles = data.data || [];
            total = data.meta?.total || articles.length;
            applyFilters();
        } catch (err) {
            articlesContainer.innerHTML = '<div class="empty-state">Could not load articles.</div>';
            console.error(err);
        }
    };

    // Search
    if (exploreSearch) {
        exploreSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });
        exploreSearch.addEventListener('input', () => {
            applyFilters();
        });
    }

    // Filters
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter || 'all';
            applyFilters();
        });
    });

    // View Toggle
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const view = this.dataset.view;
            if (articlesContainer) {
                articlesContainer.className = `articles-container ${view}-view`;
            }
        });
    });

    // Dropdowns
    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            const sort = this.dataset.sort;
            const toggle = this.closest('.filter-dropdown').querySelector('.dropdown-toggle');
            const text = this.textContent;
            const icon = toggle.querySelector('i').outerHTML;
            const chevron = '<i class="fas fa-chevron-down"></i>';
            toggle.innerHTML = icon + ' ' + text + ' ' + chevron;

            if (category) {
                activeCategory = category.toLowerCase();
            }
            if (sort) {
                activeSort = sort;
            }
            applyFilters();
        });
    });

    // Tags click
    document.querySelectorAll('.tag').forEach(tag => {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            exploreSearch.value = this.textContent.trim();
            applyFilters();
        });
    });

    fetchArticles();
});
