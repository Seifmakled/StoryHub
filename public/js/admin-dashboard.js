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

    // Table Row Click
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.action-buttons')) {
                console.log('Row clicked:', row);
            }
        });
    });

    // Action Buttons
    document.querySelectorAll('.btn-icon').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const action = this.getAttribute('title').toLowerCase();
            
            if (action === 'delete' || action === 'ban') {
                if (confirm(`Are you sure you want to ${action} this item?`)) {
                    console.log(`${action} confirmed`);
                }
            } else {
                console.log(`${action} action triggered`);
            }
        });
    });

    // Stats Cards Animation
    animateStats();

    // Period Selector
    document.querySelectorAll('.select-period').forEach(select => {
        select.addEventListener('change', function() {
            console.log('Period changed:', this.value);
            // Update charts based on selected period
        });
    });

    // Users: load list and wire form/actions
    initUsersModule();
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

    // Simple HTML escape to avoid XSS when rendering strings
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    loadUsers();
}
