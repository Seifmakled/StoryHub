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
                    <li><a href="/StoryHub/index.php?url=my-profile">My Profile</a></li>
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
