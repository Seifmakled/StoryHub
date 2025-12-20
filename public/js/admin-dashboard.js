// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const base = (window.APP_BASE || './');
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

    // Navigation Active State
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                document.querySelectorAll('.nav-item').forEach(nav => {
                    nav.classList.remove('active');
                });
                
                this.classList.add('active');
                
                // Close mobile sidebar
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('active');
                }
            }
        });
    });

    // Initialize Charts
    initializeCharts();

    // Stats Cards Animation
    animateStats();

    // Period Selector
    document.querySelectorAll('.select-period').forEach(select => {
        select.addEventListener('change', function() {
            console.log('Period changed:', this.value);
            // Update charts based on selected period
        });
    });

    // Stats, articles, users modules
    loadStats(base);
    initArticlesModule(base);
    initUsersModule(base);
});

// Initialize Charts using Chart.js
function initializeCharts() {
    // Traffic Chart
    const trafficChart = document.getElementById('trafficChart');
    if (trafficChart) {
        new Chart(trafficChart.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Views',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value / 1000 + 'k';
                            }
                        }
                    }
                }
            }
        });
    }

    // Category Chart
    const categoryChart = document.getElementById('categoryChart');
    if (categoryChart) {
        new Chart(categoryChart.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Technology', 'Design', 'Business', 'Health', 'Travel', 'Food'],
                datasets: [{
                    data: [30, 20, 15, 12, 13, 10],
                    backgroundColor: [
                        '#6366f1',
                        '#ec4899',
                        '#f59e0b',
                        '#10b981',
                        '#8b5cf6',
                        '#ef4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Animate Stats on Load
function animateStats() {
    const statValues = document.querySelectorAll('.stat-value');
    
    statValues.forEach(stat => {
        const target = stat.textContent.trim();
        const isNumber = !isNaN(target.replace(/[^0-9.]/g, ''));
        
        if (isNumber) {
            const value = parseFloat(target.replace(/[^0-9.]/g, ''));
            const suffix = target.replace(/[0-9.]/g, '');
            const duration = 2000;
            const steps = 60;
            const increment = value / steps;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= value) {
                    current = value;
                    clearInterval(timer);
                }
                
                if (suffix.includes('K')) {
                    stat.textContent = (current / 1000).toFixed(1) + 'K';
                } else {
                    stat.textContent = Math.floor(current).toLocaleString() + suffix;
                }
            }, duration / steps);
        }
    });
}

// Users management
async function loadStats(base) {
    try {
        const res = await fetch(base + 'index.php?url=api-stats');
        const json = await res.json();
        const data = json.data || {};
        setStatText('statUsers', data.users_total);
        setStatText('statArticles', data.articles_published ?? data.articles_total);
        setStatText('statViews', data.views_total);
        setStatText('statComments', data.comments_total);
    } catch (e) {
        console.error('Failed to load stats', e);
    }
}

function setStatText(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    const v = (typeof value === 'number') ? value : 0;
    el.textContent = v.toLocaleString();
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function initArticlesModule(base) {
    const tbody = document.getElementById('articlesTableBody');
    if (!tbody) return;

    async function loadArticles() {
        tbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';
        try {
            const res = await fetch(base + 'index.php?url=api-articles&status=pending&limit=50');
            const json = await res.json();
            const rows = (json.data || []).map(renderArticleRow).join('');
            tbody.innerHTML = rows || '<tr><td colspan="6">No pending articles.</td></tr>';
        } catch (e) {
            console.error('Failed to load articles', e);
            tbody.innerHTML = '<tr><td colspan="6">Could not load articles.</td></tr>';
        }
    }

    function renderArticleRow(a) {
        const author = a.full_name || a.username || 'Author';
        const cat = a.category || 'General';
        const status = a.status || 'pending';
        const cover = a.featured_image ? `${base}public/images/${a.featured_image}` : `${base}public/images/article-placeholder.jpg`;
        return `
        <tr data-article-id="${a.id}" data-slug="${a.slug || ''}">
            <td>
                <div class="table-title">
                    <img src="${cover}" alt="">
                    <span>${escapeHtml(a.title || 'Untitled')}</span>
                </div>
            </td>
            <td>${escapeHtml(author)}</td>
            <td><span class="badge-cat">${escapeHtml(cat)}</span></td>
            <td>${renderStatusBadge(status)}</td>
            <td>${(a.views || 0).toLocaleString()}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></button>
                    <button class="btn-icon btn-approve" title="Approve"><i class="fas fa-check"></i></button>
                    <button class="btn-icon btn-reject" title="Reject"><i class="fas fa-times"></i></button>
                </div>
            </td>
        </tr>`;
    }

    function renderStatusBadge(status) {
        const cls = status === 'approved' ? 'published' : (status === 'rejected' ? 'archived' : 'draft');
        return `<span class="badge-status ${cls}">${status}</span>`;
    }

    tbody.addEventListener('click', async (e) => {
        const tr = e.target.closest('tr');
        if (!tr) return;
        const id = tr.getAttribute('data-article-id');
        const slug = tr.getAttribute('data-slug');

        if (e.target.closest('.btn-view')) {
            if (slug) window.open(`${base}index.php?url=article&slug=${encodeURIComponent(slug)}`, '_blank');
            return;
        }

        if (e.target.closest('.btn-approve')) {
            await reviewArticle(id, 'approved');
            await loadArticles();
            return;
        }

        if (e.target.closest('.btn-reject')) {
            const reason = prompt('Optional rejection reason:');
            await reviewArticle(id, 'rejected', reason);
            await loadArticles();
            return;
        }
    });

    async function reviewArticle(id, status, reason) {
        if (!id) return;
        try {
            const res = await fetch(base + 'index.php?url=api-articles', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status, reason })
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                alert(err.error || 'Failed to update article');
            }
        } catch (e) {
            console.error('Review failed', e);
            alert('Network error while updating article');
        }
    }

    loadArticles();
}

function initUsersModule(base) {
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