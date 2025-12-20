// Landing Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '';

    // Mobile Navigation Toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Search Functionality
    const navSearch = document.getElementById('navSearch');
    if (navSearch) {
        navSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchQuery = this.value.trim();
                if (searchQuery) {
                    window.location.href = `${base}index.php?url=explore&search=${encodeURIComponent(searchQuery)}`;
                }
            }
        });
    }

    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const observeCards = (selector) => {
        document.querySelectorAll(selector).forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    };

    // Render helpers
    const timeToRead = (text) => {
        const words = (text || '').split(/\s+/).filter(Boolean).length;
        return Math.max(1, Math.ceil(words / 220)) + ' min read';
    };

    const featuredGrid = document.getElementById('featuredGrid');
    const trendingGrid = document.getElementById('trendingGrid');

    const renderFeatured = (items) => {
        if (!featuredGrid) return;
        if (!items || items.length === 0) {
            featuredGrid.innerHTML = '<div class="empty-state">No featured stories yet.</div>';
            return;
        }

        const [first, ...rest] = items;
        const cover = first.featured_image ? `${base}public/images/${first.featured_image}` : `${base}public/images/article-placeholder.jpg`;
        const authorImg = first.profile_image ? `${base}public/images/${first.profile_image}` : `${base}public/images/default-avatar.jpg`;
        const author = first.full_name || first.username || 'Author';
        const date = first.created_at ? new Date(first.created_at).toLocaleDateString() : '';

        const large = `
            <div class="featured-card featured-large" data-slug="${first.slug}">
                <div class="featured-image">
                    <img src="${cover}" alt="Article">
                    <span class="featured-badge">Featured</span>
                </div>
                <div class="featured-content">
                    <div class="featured-meta">
                        <span class="category">${first.category || 'Story'}</span>
                        <span class="reading-time"><i class="far fa-clock"></i> ${timeToRead(first.excerpt)}</span>
                    </div>
                    <h3>${first.title}</h3>
                    <p>${first.excerpt || ''}</p>
                    <div class="author-info">
                        <img src="${authorImg}" alt="Author" class="author-avatar">
                        <div class="author-details">
                            <span class="author-name">${author}</span>
                            <span class="publish-date">${date}</span>
                        </div>
                    </div>
                </div>
            </div>`;

        const smallCards = rest.slice(0, 2).map(item => {
            const coverSm = item.featured_image ? `${base}public/images/${item.featured_image}` : `${base}public/images/article-placeholder.jpg`;
            const authorSm = item.full_name || item.username || 'Author';
            return `
            <div class="featured-card featured-small" data-slug="${item.slug}">
                <div class="featured-image">
                    <img src="${coverSm}" alt="Article">
                </div>
                <div class="featured-content">
                    <span class="category">${item.category || 'Story'}</span>
                    <h4>${item.title}</h4>
                    <div class="author-info">
                        <img src="${base}public/images/${item.profile_image || 'default-avatar.jpg'}" alt="Author" class="author-avatar">
                        <span class="author-name">${authorSm}</span>
                    </div>
                </div>
            </div>`;
        }).join('');

        featuredGrid.innerHTML = `
            ${large}
            <div class="featured-small-grid">${smallCards || ''}</div>
        `;

        featuredGrid.querySelectorAll('.featured-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') return;
                const slug = card.getAttribute('data-slug');
                if (slug) window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
            });
        });

        observeCards('.featured-card');
    };

    const renderTrending = (items) => {
        if (!trendingGrid) return;
        if (!items || items.length === 0) {
            trendingGrid.innerHTML = '<div class="empty-state">No trending stories yet.</div>';
            return;
        }

        trendingGrid.innerHTML = items.slice(0, 6).map((item, idx) => {
            const cover = item.featured_image ? `${base}public/images/${item.featured_image}` : `${base}public/images/article-placeholder.jpg`;
            return `
            <div class="trending-card" data-slug="${item.slug}">
                <div class="trending-image">
                    <img src="${cover}" alt="Article">
                    <div class="trending-overlay">
                        <span class="trending-number">#${idx + 1}</span>
                    </div>
                </div>
                <div class="trending-content">
                    <span class="category">${item.category || 'Story'}</span>
                    <h3>${item.title}</h3>
                    <div class="article-stats">
                        <span><i class="fas fa-eye"></i> ${item.views || 0}</span>
                        <span><i class="fas fa-heart"></i> ${item.likes_count || 0}</span>
                        <span><i class="fas fa-comment"></i> ${item.comments_count || 0}</span>
                    </div>
                </div>
            </div>`;
        }).join('');

        trendingGrid.querySelectorAll('.trending-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') return;
                const slug = card.getAttribute('data-slug');
                if (slug) window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
            });
        });

        observeCards('.trending-card');
    };

    const fetchSection = async (params) => {
        const qs = new URLSearchParams(params).toString();
        const res = await fetch(`${base}index.php?url=api-articles&${qs}`);
        if (!res.ok) throw new Error('Failed to load');
        const data = await res.json();
        return data.data || [];
    };

    (async function initSections() {
        try {
            let [featured, trending] = await Promise.all([
                fetchSection({ featured: 1, limit: 3 }),
                fetchSection({ sort: 'likes', limit: 6 })
            ]);

            // Fallbacks if nothing is featured or no likes yet
            if (!featured || featured.length === 0) {
                featured = await fetchSection({ limit: 3 });
            }
            if (!trending || trending.length === 0) {
                trending = await fetchSection({ sort: 'latest', limit: 6 });
            }

            renderFeatured(featured);
            renderTrending(trending);
        } catch (err) {
            if (featuredGrid) featuredGrid.innerHTML = '<div class="empty-state">Could not load featured stories.</div>';
            if (trendingGrid) trendingGrid.innerHTML = '<div class="empty-state">Could not load trending stories.</div>';
            console.error(err);
        }
    })();
});
