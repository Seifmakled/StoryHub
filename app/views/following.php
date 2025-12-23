<?php
$pageTitle = 'Connections - StoryHub';
$pageDescription = 'People you follow and your followers';
$pageCSS = 'profile.css';
include __DIR__ . '/../partials/header.php';
?>

<div class="container" style="max-width: 960px; margin: 40px auto;">
    <h1 style="margin-bottom: 16px;">Your connections</h1>
    <p style="margin-bottom: 24px; color: #6b7280;">See everyone you follow and everyone following you.</p>

    <div class="card" style="padding: 16px; margin-bottom: 20px;">
        <h2 style="margin-bottom: 12px;">Following</h2>
        <div id="followingListPage" class="connections-list">Loading...</div>
    </div>

    <div class="card" style="padding: 16px;">
        <h2 style="margin-bottom: 12px;">Followers</h2>
        <div id="followersListPage" class="connections-list">Loading...</div>
    </div>
</div>

<script>
(async function() {
    const followingEl = document.getElementById('followingListPage');
    const followersEl = document.getElementById('followersListPage');

    async function load(endpoint, targetEl, emptyLabel) {
        try {
            const res = await fetch(endpoint);
            if (!res.ok) throw new Error('Failed');
            const json = await res.json();
            const people = json.data || [];
            if (!people.length) {
                targetEl.innerHTML = `<div class="connections-empty">${emptyLabel}</div>`;
                return;
            }
            targetEl.innerHTML = people.map(p => `
                <div class="connection-item">
                    <img class="connection-avatar" src="public/images/${p.profile_image || 'default-avatar.jpg'}" alt="${p.username}">
                    <div>
                        <div class="connection-name">${p.full_name || p.username}</div>
                        <div class="connection-username">@${p.username}</div>
                        ${p.bio ? `<div class="connection-bio">${p.bio}</div>` : ''}
                    </div>
                    <a class="connection-link" href="/StoryHub/index.php?url=profile&id=${p.id}">View profile</a>
                </div>
            `).join('');
        } catch (e) {
            targetEl.innerHTML = '<div class="connections-empty">Could not load.</div>';
        }
    }

    await load('/StoryHub/index.php?url=api-social&following_list=1', followingEl, 'You are not following anyone yet.');
    await load('/StoryHub/index.php?url=api-social&followers_list=1', followersEl, 'No one is following you yet.');
})();
</script>

<style>
.container .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.05); }
.connections-list { display: flex; flex-direction: column; gap: 12px; }
.connection-item { display: flex; align-items: center; gap: 12px; padding: 10px; border: 1px solid #f3f4f6; border-radius: 10px; }
.connection-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; background: #f3f4f6; }
.connection-name { font-weight: 600; color: #111827; }
.connection-username { font-size: 13px; color: #6b7280; }
.connection-bio { font-size: 13px; color: #4b5563; }
.connection-link { margin-left: auto; color: #2563eb; font-size: 13px; }
.connections-empty { color: #6b7280; font-size: 14px; }
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>
