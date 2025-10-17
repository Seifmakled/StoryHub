<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="index.php?page=home">
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
                <li><a href="index.php?page=home">Home</a></li>
                <li><a href="index.php?page=explore">Explore</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?page=profile&id=<?php echo $_SESSION['user_id']; ?>">My Profile</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="index.php?page=admin">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-write">
                        <a href="index.php?page=write" class="btn-write">
                            <i class="fas fa-pen"></i> Write
                        </a>
                    </li>
                    <li class="nav-user">
                        <div class="user-dropdown">
                            <img src="public/images/<?php echo $_SESSION['profile_image'] ?? 'default-avatar.jpg'; ?>" alt="Profile" class="nav-avatar">
                            <div class="dropdown-content">
                                <a href="index.php?page=profile&id=<?php echo $_SESSION['user_id']; ?>">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                                <a href="index.php?page=settings">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <hr>
                                <a href="index.php?page=logout">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="index.php?page=login" class="btn-login">Sign In</a></li>
                    <li><a href="index.php?page=register" class="btn-signup">Get Started</a></li>
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
