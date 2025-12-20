<?php
$pageTitle = 'Explore - StoryHub';
$pageDescription = 'Discover amazing articles and stories';
$pageCSS = 'explore.css';
$pageJS = 'explore.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';?>

<div class="explore-container">
    <!-- Search & Filter Section -->
    <div class="explore-header">
        <div class="container">
            <h1>Explore Stories</h1>
            <p>Discover amazing content from writers around the world</p>

            <div class="explore-search">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="exploreSearch" placeholder="Search articles, topics, or authors...">
                </div>
            </div>

            <div class="explore-filters">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fas fa-globe"></i> All
                    </button>
                    <button class="filter-btn" data-filter="trending">
                        <i class="fas fa-fire"></i> Trending
                    </button>
                    <button class="filter-btn" data-filter="featured">
                        <i class="fas fa-star"></i> Featured
                    </button>
                    <button class="filter-btn" data-filter="recent">
                        <i class="fas fa-clock"></i> Recent
                    </button>
                    <button class="filter-btn" data-filter="popular">
                        <i class="fas fa-heart"></i> Popular
                    </button>
                </div>

                <div class="filter-dropdown">
                    <button class="dropdown-toggle">
                        <i class="fas fa-filter"></i> Category
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" data-category="all">All Categories</a>
                        <a href="#" data-category="technology">Technology</a>
                        <a href="#" data-category="design">Design</a>
                        <a href="#" data-category="business">Business</a>
                        <a href="#" data-category="health">Health</a>
                        <a href="#" data-category="travel">Travel</a>
                        <a href="#" data-category="food">Food</a>
                        <a href="#" data-category="lifestyle">Lifestyle</a>
                        <a href="#" data-category="entertainment">Entertainment</a>
                    </div>
                </div>

                <div class="filter-dropdown">
                    <button class="dropdown-toggle">
                        <i class="fas fa-sort"></i> Sort By
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" data-sort="latest">Latest</a>
                        <a href="#" data-sort="oldest">Oldest</a>
                        <a href="#" data-sort="views">Most Viewed</a>
                        <a href="#" data-sort="likes">Most Liked</a>
                    </div>
                </div>
            </div>

            <div class="explore-metrics">
                <div class="metric-card">
                    <div class="metric-label">Stories</div>
                    <div class="metric-value">12.4k</div>
                    <div class="metric-sub">+180 this week</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Writers</div>
                    <div class="metric-value">3.1k</div>
                    <div class="metric-sub">Now active</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Topics</div>
                    <div class="metric-value">42</div>
                    <div class="metric-sub">Fresh tags added</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Trending</div>
                    <div class="metric-value">AI · Travel · UX</div>
                    <div class="metric-sub">Updated hourly</div>
                </div>
            </div>

            <div class="chip-row">
                <button class="chip">AI</button>
                <button class="chip">Productivity</button>
                <button class="chip">Design Systems</button>
                <button class="chip">Remote Work</button>
                <button class="chip">Health</button>
                <button class="chip">Photography</button>
                <button class="chip">Finance</button>
            </div>
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="explore-content">
        <div class="container">
            <div class="results-info">
                <span id="resultsCount">Showing 1-12 of 1,234 articles</span>
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" data-view="list" title="List View">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <div class="articles-container grid-view" id="articlesContainer">
                <div class="empty-state loading">Loading articles...</div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">4</button>
                <button class="page-btn">5</button>
                <span class="page-dots">...</span>
                <button class="page-btn">103</button>
                <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Sidebar (Tags & Popular Authors) -->
    <aside class="explore-sidebar">
        <div class="sidebar-section">
            <h3><i class="fas fa-tags"></i> Popular Tags</h3>
            <div class="tags-cloud">
                <a href="#" class="tag">JavaScript</a>
                <a href="#" class="tag">React</a>
                <a href="#" class="tag">Design</a>
                <a href="#" class="tag">AI</a>
                <a href="#" class="tag">Python</a>
                <a href="#" class="tag">Startup</a>
                <a href="#" class="tag">Marketing</a>
                <a href="#" class="tag">Health</a>
                <a href="#" class="tag">Travel</a>
                <a href="#" class="tag">Photography</a>
            </div>
        </div>

        <div class="sidebar-section">
            <h3><i class="fas fa-user-friends"></i> Popular Authors</h3>
            <div class="authors-list">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="author-item">
                    <img src="public/images/default-avatar.jpg" alt="Author">
                    <div class="author-info">
                        <span class="name">Author Name</span>
                        <span class="followers">1.2K followers</span>
                    </div>
                    <button class="btn-follow-sm">Follow</button>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </aside>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
