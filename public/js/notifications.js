// Notifications page
(function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '';
    const listEl = document.getElementById('notifList');
    const markBtn = document.getElementById('markReadBtn');

    const fetchNotifications = async () => {
        listEl.innerHTML = '<div class="empty-state loading">Loading notifications...</div>';
        try {
            const res = await fetch(`${base}index.php?url=api-social&notifications=1&limit=30`);
            if (res.status === 401) {
                listEl.innerHTML = '<div class="empty-state">Sign in to view notifications.</div>';
                return;
            }
            const data = await res.json();
            const items = data.data || [];
            if (items.length === 0) {
                listEl.innerHTML = '<div class="empty-state">No notifications yet.</div>';
                return;
            }
            listEl.innerHTML = items.map(n => {
                const actor = n.actor_full_name || n.actor_username || 'Someone';
                const avatar = n.actor_image ? `${base}public/images/${n.actor_image}` : `${base}public/images/default-avatar.jpg`;
                const date = n.created_at ? new Date(n.created_at).toLocaleString() : '';
                const readClass = n.is_read ? 'read' : 'unread';
                return `
                <div class="notif-item ${readClass}">
                    <img src="${avatar}" alt="${actor}">
                    <div class="notif-text">
                        <div class="notif-message"><strong>${actor}</strong> ${n.message}</div>
                        <div class="notif-meta">${date}</div>
                    </div>
                </div>`;
            }).join('');
        } catch (err) {
            listEl.innerHTML = '<div class="empty-state">Could not load notifications.</div>';
        }
    };

    async function markAllRead() {
        try {
            const fd = new FormData();
            fd.append('action', 'read_notifications');
            await fetch(`${base}index.php?url=api-social`, { method: 'POST', body: fd });
            fetchNotifications();
        } catch (err) {
            // ignore
        }
    }

    if (markBtn) {
        markBtn.addEventListener('click', (e) => {
            e.preventDefault();
            markAllRead();
        });
    }

    fetchNotifications();
})();
