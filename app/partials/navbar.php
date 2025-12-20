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
                <li><a href="/StoryHub/index.php?url=explore">Explore</a></li>
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
                        <a href="/StoryHub/index.php?url=my-profile">
                            <img src="public/images/<?php echo $_SESSION['profile_image'] ?? 'default-avatar.jpg'; ?>" alt="Profile" class="nav-avatar">
                        </a>
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
