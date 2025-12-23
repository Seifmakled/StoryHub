// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle for Mobile
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024) {
            if (!e.target.closest('.admin-sidebar') && !e.target.closest('.btn-toggle-sidebar')) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Navigation Active State + smooth scroll
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '';
            if (href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');

                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('active');
                }
            }
        });
    });

    // Fetch and render admin data
    loadAdminData();

    // Users: load list and wire form/actions
    initUsersModule();
});
let dashboardCharts = { traffic: null, categories: null };

async function loadAdminData() {
    try {
        const base = (window.APP_BASE || './');
        const res = await fetch(base + 'index.php?url=api-admin');
        if (!res.ok) throw new Error('Failed to fetch admin data');
        const data = await res.json();
        updateStats(data.stats || {});
        renderCharts(data.charts || {});
        renderRecentArticles((data.recent && data.recent.articles) || []);
        renderComments((data.recent && data.recent.comments) || []);
    } catch (err) {
        console.error('Admin data load failed', err);
    }
}

function updateStats(stats) {
    const { users = 0, articles = 0, views = 0, comments = 0 } = stats;
    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = formatNumber(value);
    };

    setText('statUsers', users);
    setText('statArticles', articles);
    setText('statViews', views);
    setText('statComments', comments);

    setText('badgeUsers', users);
    setText('badgeArticles', articles);
    setText('badgeComments', comments);

    animateStats();
}

function renderCharts(charts) {
    const traffic = charts.traffic || { labels: [], articles: [], users: [] };
    const categories = charts.categories || { labels: [], counts: [] };

    const trafficCanvas = document.getElementById('trafficChart');
    if (trafficCanvas) {
        if (dashboardCharts.traffic) dashboardCharts.traffic.destroy();
        dashboardCharts.traffic = new Chart(trafficCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: traffic.labels,
                datasets: [
                    {
                        label: 'Articles created',
                        data: traffic.articles,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.12)',
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'New users',
                        data: traffic.users,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: true, position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        if (dashboardCharts.categories) dashboardCharts.categories.destroy();
        const palette = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#8b5cf6', '#ef4444', '#22c55e', '#0ea5e9'];
        dashboardCharts.categories = new Chart(categoryCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categories.labels,
                datasets: [{
                    data: categories.counts,
                    backgroundColor: categories.counts.map((_, idx) => palette[idx % palette.length])
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

function renderRecentArticles(articles) {
    const tbody = document.getElementById('recentArticlesBody');
    if (!tbody) return;
    if (!articles.length) {
        tbody.innerHTML = '<tr><td colspan="6">No articles yet</td></tr>';
        return;
    }

    const rows = articles.map(article => {
        const statusClass = article.is_published ? 'published' : 'draft';
        const statusLabel = article.is_published ? 'Published' : 'Draft';
        const views = typeof article.views === 'number' ? formatNumber(article.views) : '0';
        const category = article.category || '—';
        const title = article.title || 'Untitled';
        const author = article.author || 'Unknown';
        const slug = article.slug || '';
        return `
        <tr data-article-id="${article.id}" data-article-slug="${escapeHtml(slug)}">
            <td>
                <div class="table-title">
                    <img src="public/images/article-placeholder.jpg" alt="">
                    <span>${escapeHtml(title)}</span>
                </div>
            </td>
            <td>${escapeHtml(author)}</td>
            <td><span class="badge-cat">${escapeHtml(category)}</span></td>
            <td><span class="badge-status ${statusClass}">${statusLabel}</span></td>
            <td>${views}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></button>
                    <button class="btn-icon btn-takedown" title="Take down"><i class="fas fa-ban"></i></button>
                    <button class="btn-icon btn-flag" title="Flag author"><i class="fas fa-flag"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');

    tbody.innerHTML = rows;

    // Wire actions via event delegation (single handler per render)
    tbody.onclick = async (e) => {
        const row = e.target.closest('tr');
        if (!row) return;
        const articleId = row.getAttribute('data-article-id');
        const slug = row.getAttribute('data-article-slug');
        if (!articleId) return;

        if (e.target.closest('.btn-view')) {
            const base = (window.APP_BASE || './');
            const url = base + `index.php?url=article&slug=${encodeURIComponent(slug || '')}`;
            window.open(url, '_blank');
            return;
        }

        if (e.target.closest('.btn-takedown')) {
            if (!confirm('Take down this article? It will be unpublished.')) return;
            await adminArticleAction('take_down', articleId);
            await loadAdminData();
            return;
        }

        if (e.target.closest('.btn-flag')) {
            if (!confirm('Flag this author and notify them?')) return;
            await adminArticleAction('flag', articleId);
            return;
        }
    };
}

function renderComments(comments) {
    const tbody = document.getElementById('commentsBody');
    if (!tbody) return;
    if (!comments.length) {
        tbody.innerHTML = '<tr><td colspan="5">No comments yet</td></tr>';
        return;
    }
    const rows = comments.map(c => {
        const contentPreview = escapeHtml((c.content || '').slice(0, 120));
        const author = c.author || 'Unknown';
        const articleTitle = c.article_title || 'Article';
        const slug = c.article_slug || '';
        const posted = c.created_at ? new Date(c.created_at).toLocaleString() : '';
        return `
        <tr data-comment-id="${c.id}" data-comment-content="${escapeHtml(c.content || '')}" data-article-slug="${escapeHtml(slug)}">
            <td>${contentPreview}</td>
            <td>${escapeHtml(author)}</td>
            <td>${escapeHtml(articleTitle)}</td>
            <td>${posted}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-view-comment" title="View"><i class="fas fa-eye"></i></button>
                    <button class="btn-icon btn-flag-comment" title="Flag commenter"><i class="fas fa-flag"></i></button>
                    <button class="btn-icon btn-delete-comment" title="Remove"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');
    tbody.innerHTML = rows;

    tbody.onclick = async (e) => {
        const row = e.target.closest('tr');
        if (!row) return;
        const id = row.getAttribute('data-comment-id');
        const content = row.getAttribute('data-comment-content') || '';
        const slug = row.getAttribute('data-article-slug') || '';
        const base = (window.APP_BASE || './');

        if (e.target.closest('.btn-view-comment')) {
            alert(content || 'No content');
            return;
        }

        if (e.target.closest('.btn-flag-comment')) {
            if (!confirm('Flag this commenter and notify them?')) return;
            await adminCommentAction('comment_flag', id);
            return;
        }

        if (e.target.closest('.btn-delete-comment')) {
            if (!confirm('Remove this comment?')) return;
            await adminCommentAction('comment_delete', id);
            await loadAdminData();
            return;
        }

    };
}

// Animate Stats on Load (re-used after data fetch)
function animateStats() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
        const target = stat.textContent.trim();
        const numeric = parseFloat(target.replace(/[^0-9.]/g, '')) || 0;
        const suffix = target.replace(/[0-9.,\s]/g, '');
        if (!numeric) return;
        const duration = 900;
        const steps = 45;
        const increment = numeric / steps;
        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= numeric) {
                current = numeric;
                clearInterval(timer);
            }
            const value = Math.round(current);
            stat.textContent = formatNumber(value) + suffix;
        }, duration / steps);
    });
}

// Users management
function initUsersModule() {
    const usersTable = document.getElementById('usersTable');
    const addForm = document.getElementById('addUserForm');
    if (!usersTable || !addForm) return;

    async function loadUsers() {
        try {
            const base = (window.APP_BASE || './');
            const res = await fetch(base + 'index.php?url=api-users');
            const json = await res.json();
            const rows = (json.data || []).map(u => {
                // Handle different field names that might exist
                const username = u.username || u.user_name || u.name || 'N/A';
                const email = u.email || 'N/A';
                const joined = u.created_at ? new Date(u.created_at).toLocaleDateString() : (u.date_created || 'N/A');
                const isAdmin = (u.is_admin == 1 || u.is_admin === '1' || u.is_admin === true) ? 'Yes' : 'No';
                return `
                <tr data-user-id="${u.id}">
                    <td>${u.id}</td>
                    <td>${escapeHtml(username)}</td>
                    <td>${escapeHtml(email)}</td>
                    <td>${joined}</td>
                    <td>${isAdmin}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
            usersTable.querySelector('tbody').innerHTML = rows;
        } catch (e) {
            console.error('Failed to load users', e);
        }
    }

    addForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('newUsername').value.trim();
        const email = document.getElementById('newEmail').value.trim();
        const password = document.getElementById('newPassword').value;
        if (!username || !email || !password) return;
        try {
            const base = (window.APP_BASE || './');
            const res = await fetch(base + 'index.php?url=api-users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, email, password })
            });
            if (!res.ok) {
                let msg = 'Failed to add user';
                try {
                    const err = await res.json();
                    if (err && err.error) msg = err.error;
                } catch(_) {}
                alert(msg);
                return;
            }
            addForm.reset();
            await loadUsers();
        } catch (e) {
            console.error('Failed to add user', e);
            alert('Network or server error while adding user');
        }
    });

    usersTable.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-delete');
        if (!btn) return;
        const tr = e.target.closest('tr');
        const id = tr && tr.getAttribute('data-user-id');
        if (!id) return;
        if (!confirm('Delete this user?')) return;
        try {
            const base = (window.APP_BASE || './');
            const res = await fetch(base + `index.php?url=api-users&id=${encodeURIComponent(id)}`, { method: 'DELETE' });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                alert(err.error || 'Failed to delete user');
                return;
            }
            await loadUsers();
        } catch (e) {
            console.error('Failed to delete user', e);
        }
    });

    loadUsers();
}

// Simple HTML escape to avoid XSS when rendering strings
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatNumber(value) {
    const num = Number(value) || 0;
    return num.toLocaleString();
}

async function adminArticleAction(action, articleId) {
    try {
        const base = (window.APP_BASE || './');
        const res = await fetch(base + 'index.php?url=api-admin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, article_id: articleId })
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            alert(err.error || 'Action failed');
            return false;
        }
        return true;
    } catch (err) {
        console.error('Admin article action failed', err);
        alert('Network or server error');
        return false;
    }
}

async function adminCommentAction(action, commentId) {
    try {
        const base = (window.APP_BASE || './');
        const res = await fetch(base + 'index.php?url=api-admin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, comment_id: commentId })
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            alert(err.error || 'Action failed');
            return false;
        }
        return true;
    } catch (err) {
        console.error('Admin comment action failed', err);
        alert('Network or server error');
        return false;
    }
}