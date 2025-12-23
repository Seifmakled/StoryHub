<?php
$navCategories = [
    ['name' => 'Technology', 'icon' => 'fa-laptop-code', 'color' => '#3b82f6'],
    ['name' => 'Design', 'icon' => 'fa-palette', 'color' => '#ec4899'],
    ['name' => 'Business', 'icon' => 'fa-briefcase', 'color' => '#f59e0b'],
    ['name' => 'Health', 'icon' => 'fa-heartbeat', 'color' => '#10b981'],
    ['name' => 'Travel', 'icon' => 'fa-plane', 'color' => '#8b5cf6'],
    ['name' => 'Food', 'icon' => 'fa-utensils', 'color' => '#ef4444'],
    ['name' => 'Lifestyle', 'icon' => 'fa-coffee', 'color' => '#06b6d4'],
    ['name' => 'Entertainment', 'icon' => 'fa-film', 'color' => '#f97316']
];

$navQuickFilters = [
    ['label' => 'Trending', 'icon' => 'fa-fire', 'query' => 'trending'],
    ['label' => 'Featured', 'icon' => 'fa-star', 'query' => 'featured'],
    ['label' => 'New', 'icon' => 'fa-bolt', 'query' => 'new'],
    ['label' => 'For You', 'icon' => 'fa-compass', 'query' => 'recommended']
];
?>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php?url=landing">
                <i class="fas fa-feather-alt"></i>
                <span>StoryHub</span>
            </a>
        </div>

        <div class="nav-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search articles, topics, or authors..." id="navSearch">
        </div>

        <div class="nav-menu" id="navMenu">
            <ul class="nav-links">
                <!-- problem right here to be fixed when pressed on home, it automatically gets you signed in to a dummy account i believe -->
                <li><a href="/StoryHub/index.php?url=landing">Home</a></li>
                <li class="nav-explore" id="navExplore">
                    <a href="/StoryHub/index.php?url=explore" class="nav-explore-trigger" aria-expanded="false">
                        Explore <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="mega-dropdown" role="menu" aria-label="Explore categories">
                        <div class="mega-inner">
                            <div class="mega-column">
                                <div class="mega-heading">
                                    <span><i class="fas fa-compass"></i> Discover</span>
                                    <a href="/StoryHub/index.php?url=explore" class="mega-view-all">View all</a>
                                </div>
                                <div class="mega-quick-links">
                                    <?php foreach ($navQuickFilters as $filter): ?>
                                        <a href="/StoryHub/index.php?url=explore&filter=<?php echo $filter['query']; ?>" class="mega-chip">
                                            <i class="fas <?php echo $filter['icon']; ?>"></i>
                                            <?php echo $filter['label']; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mega-cta">
                                    <div>
                                        <p class="mega-cta-title">Start writing today</p>
                                        <p class="mega-cta-sub">Share your first story with the community.</p>
                                    </div>
                                    <a href="/StoryHub/index.php?url=write" class="btn-write mega-cta-btn">
                                        <i class="fas fa-pen"></i> Write now
                                    </a>
                                </div>
                            </div>

                            <div class="mega-column mega-grid">
                                <?php foreach ($navCategories as $category): ?>
                                    <a href="/StoryHub/index.php?url=explore&category=<?php echo strtolower($category['name']); ?>" class="mega-card" style="--category-color: <?php echo $category['color']; ?>">
                                        <div class="mega-icon">
                                            <i class="fas <?php echo $category['icon']; ?>"></i>
                                        </div>
                                        <div>
                                            <p class="mega-card-title"><?php echo $category['name']; ?></p>
                                            <p class="mega-card-sub">Discover stories</p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-following" id="navFollowing">
                        <a href="/StoryHub/index.php?url=following" class="nav-following-trigger" aria-expanded="false">
                            Following <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="following-dropdown" id="followingDropdown">
                            <div class="following-inner">
                                <div class="following-heading">People you follow</div>
                                <div class="following-list" id="followingList">
                                    <div class="following-loading">Loading...</div>
                                </div>
                                <div class="following-footer">
                                    <a href="/StoryHub/index.php?url=following">View all followers & following</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="/StoryHub/index.php?url=admin">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-write">
                        <a href="/StoryHub/index.php?url=write" class="btn-write">
                            <i class="fas fa-pen"></i> Write
                        </a>
                    </li>
                    <li class="nav-user">
                        <div class="user-dropdown">
                            <img src="public/images/<?php echo $_SESSION['profile_image'] ?? 'default-avatar.jpg'; ?>" alt="Profile" class="nav-avatar">
                            <div class="dropdown-content">
                                <a href="/StoryHub/index.php?url=my-profile">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                                <a href="/StoryHub/index.php?url=settings">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <a href="/StoryHub/index.php?url=notifications">
                                    <i class="fas fa-bell"></i> Notifications
                                </a>
                                <hr>
                                <a href="/StoryHub/index.php?url=logout">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="/StoryHub/index.php?url=login" class="btn-login">Sign In</a></li>
                    <li><a href="/StoryHub/index.php?url=register" class="btn-signup">Get Started</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

<style>
.nav-following { position: relative; }
.nav-following-trigger { display: flex; align-items: center; gap: 6px; }
.following-dropdown { position: absolute; top: 110%; left: 0; background: #fff; border: 1px solid #e5e7eb; box-shadow: 0 12px 30px rgba(0,0,0,0.12); border-radius: 10px; width: 260px; opacity: 0; pointer-events: none; transform: translateY(6px); transition: opacity 0.2s ease, transform 0.2s ease; z-index: 20; }
.nav-following:hover .following-dropdown { opacity: 1; pointer-events: auto; transform: translateY(0); }
.following-inner { max-height: 320px; overflow: auto; padding: 12px; }
.following-heading { font-weight: 600; margin-bottom: 8px; font-size: 14px; }
.following-list { display: flex; flex-direction: column; gap: 10px; }
.following-item { display: flex; align-items: center; gap: 10px; }
.following-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: #f3f4f6; }
.following-name { font-weight: 600; font-size: 14px; color: #111827; }
.following-username { font-size: 12px; color: #6b7280; }
.following-empty, .following-loading { color: #6b7280; font-size: 13px; }
.following-footer { border-top: 1px solid #f3f4f6; padding-top: 10px; margin-top: 10px; font-size: 13px; }
.following-footer a { color: #2563eb; }
@media (max-width: 1024px) { .following-dropdown { position: static; width: 100%; box-shadow: none; border: none; transform: none; opacity: 1; pointer-events: auto; } }
</style>

<script>
(function() {
    const trigger = document.querySelector('.nav-following');
    const listEl = document.getElementById('followingList');
    const dropdown = document.getElementById('followingDropdown');
    if (!trigger || !listEl || !dropdown) return;

    let loaded = false;
    async function loadFollowing() {
        if (loaded) return;
        try {
            const res = await fetch('/StoryHub/index.php?url=api-social&following_list=1');
            if (!res.ok) throw new Error('Failed');
            const json = await res.json();
            const people = json.data || [];
            if (!people.length) {
                listEl.innerHTML = '<div class="following-empty">You are not following anyone yet.</div>';
                loaded = true;
                return;
            }
            const items = people.map(p => `
                <a class="following-item" href="/StoryHub/index.php?url=profile&id=${p.id}">
                    <img class="following-avatar" src="public/images/${p.profile_image || 'default-avatar.jpg'}" alt="${p.username}">
                    <div>
                        <div class="following-name">${p.full_name || p.username}</div>
                        <div class="following-username">@${p.username}</div>
                    </div>
                </a>
            `).join('');
            listEl.innerHTML = items;
            loaded = true;
        } catch (e) {
            listEl.innerHTML = '<div class="following-empty">Could not load following.</div>';
        }
    }

    trigger.addEventListener('mouseenter', loadFollowing, { once: true });
    trigger.addEventListener('focusin', loadFollowing, { once: true });
})();
</script>
