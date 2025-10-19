<?php
$pageTitle = 'For You - StoryHub';
$pageDescription = 'Personalized recommendations just for you';
$pageCSS = 'fyp.css';
$pageJS = 'fyp.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';?>

<div class="fyp-container">
    <!-- Header Section -->
    <div class="fyp-header">
        <div class="container">
            <div class="fyp-welcome">
                <h1><i class="fas fa-magic"></i> For You</h1>
                <p>AI-powered recommendations tailored to your interests</p>
            </div>

            <div class="fyp-search">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="fypSearch" placeholder="Search your personalized feed...">
                </div>
            </div>

            <div class="fyp-filters">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fas fa-sparkles"></i> All Recommendations
                    </button>
                    <button class="filter-btn" data-filter="trending">
                        <i class="fas fa-fire"></i> Trending for You
                    </button>
                    <button class="filter-btn" data-filter="similar">
                        <i class="fas fa-users"></i> Similar Readers
                    </button>
                    <button class="filter-btn" data-filter="new">
                        <i class="fas fa-star"></i> New Discoveries
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Personalized Content -->
    <div class="fyp-content">
        <div class="container">
            <!-- Recommendation Sections -->
            
            <!-- Because You Liked Section -->
            <div class="recommendation-section">
                <div class="section-header">
                    <h2><i class="fas fa-heart"></i> Because you liked "Football Tactics"</h2>
                    <p>More articles you might enjoy based on your reading history</p>
                </div>
                <div class="articles-container grid-view" id="becauseYouLiked">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <article class="fyp-card">
                        <div class="card-image">
                            <img src="\StoryHub\public\images\articles\AI.jpg" alt="Article">
                            <div class="card-overlay">
                                <button class="btn-save" title="Save article">
                                    <i class="far fa-bookmark"></i>
                                </button>
                            </div>
                            <div class="recommendation-badge">
                                <i class="fas fa-magic"></i> Recommended
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-meta">
                                <span class="category">Sports</span>
                                <span class="reading-time"><i class="far fa-clock"></i> 7 min</span>
                            </div>
                            <h3>Advanced Football Strategies for Modern Teams</h3>
                            <p>Discover the latest tactical innovations that are revolutionizing the beautiful game...</p>
                            
                            <div class="card-footer">
                                <div class="author-info">
                                    <img src="\StoryHub\public\images\authors\author1.jpg" alt="Author">
                                    <div class="author-details">
                                        <span class="author-name">Mike Johnson</span>
                                        <span class="publish-date">May 20, 2024</span>
                                    </div>
                                </div>
                                <div class="card-stats">
                                    <span title="Views"><i class="fas fa-eye"></i> 2.1K</span>
                                    <span title="Likes"><i class="fas fa-heart"></i> 456</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Maybe You Will Like Section -->
            <div class="recommendation-section">
                <div class="section-header">
                    <h2><i class="fas fa-lightbulb"></i> Maybe you will like this</h2>
                    <p>Articles trending among readers with similar interests</p>
                </div>
                <div class="articles-container grid-view" id="maybeYouWillLike">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <article class="fyp-card">
                        <div class="card-image">
                            <img src="\StoryHub\public\images\articles\web.jpg" alt="Article">
                            <div class="card-overlay">
                                <button class="btn-save" title="Save article">
                                    <i class="far fa-bookmark"></i>
                                </button>
                            </div>
                            <div class="recommendation-badge trending">
                                <i class="fas fa-trending-up"></i> Trending
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-meta">
                                <span class="category">Technology</span>
                                <span class="reading-time"><i class="far fa-clock"></i> 5 min</span>
                            </div>
                            <h3>The Future of Web Development: What's Next?</h3>
                            <p>Exploring emerging technologies and frameworks that will shape the next decade...</p>
                            
                            <div class="card-footer">
                                <div class="author-info">
                                    <img src="\StoryHub\public\images\authors\author3.jpg" alt="Author">
                                    <div class="author-details">
                                        <span class="author-name">Sarah Chen</span>
                                        <span class="publish-date">May 18, 2024</span>
                                    </div>
                                </div>
                                <div class="card-stats">
                                    <span title="Views"><i class="fas fa-eye"></i> 3.2K</span>
                                    <span title="Likes"><i class="fas fa-heart"></i> 789</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Don't Miss This Out Section -->
            <div class="recommendation-section">
                <div class="section-header">
                    <h2><i class="fas fa-exclamation-circle"></i> Don't miss this out</h2>
                    <p>Handpicked articles that are making waves right now</p>
                </div>
                <div class="articles-container grid-view" id="dontMissOut">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                    <article class="fyp-card featured">
                        <div class="card-image">
                            <img src="\StoryHub\public\images\articles\AI2.jpg" alt="Article">
                            <div class="card-overlay">
                                <button class="btn-save" title="Save article">
                                    <i class="far fa-bookmark"></i>
                                </button>
                            </div>
                            <div class="recommendation-badge featured">
                                <i class="fas fa-crown"></i> Featured
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-meta">
                                <span class="category">AI & Machine Learning</span>
                                <span class="reading-time"><i class="far fa-clock"></i> 12 min</span>
                            </div>
                            <h3>Revolutionary AI Breakthroughs That Will Change Everything</h3>
                            <p>An in-depth analysis of the latest AI developments and their potential impact on society...</p>
                            
                            <div class="card-footer">
                                <div class="author-info">
                                    <img src="\StoryHub\public\images\authors\author4.jpg" alt="Author">
                                    <div class="author-details">
                                        <span class="author-name">Dr. Alex Rivera</span>
                                        <span class="publish-date">May 22, 2024</span>
                                    </div>
                                </div>
                                <div class="card-stats">
                                    <span title="Views"><i class="fas fa-eye"></i> 5.8K</span>
                                    <span title="Likes"><i class="fas fa-heart"></i> 1.2K</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Continue Reading Section -->
            <div class="recommendation-section">
                <div class="section-header">
                    <h2><i class="fas fa-book-open"></i> Continue reading</h2>
                    <p>Pick up where you left off</p>
                </div>
                <div class="articles-container list-view" id="continueReading">
                    <?php for ($i = 1; $i <= 2; $i++): ?>
                    <article class="fyp-card continue-reading">
                        <div class="card-image">
                            <img src="\StoryHub\public\images\articles\AI3.jpg" alt="Article">
                            <div class="reading-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 65%"></div>
                                </div>
                                <span class="progress-text">65% read</span>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-meta">
                                <span class="category">Design</span>
                                <span class="reading-time"><i class="far fa-clock"></i> 8 min left</span>
                            </div>
                            <h3>Minimalist Design Principles for Modern Websites</h3>
                            <p>Learn how to create clean, effective designs that focus on user experience and content...</p>
                            
                            <div class="card-footer">
                                <div class="author-info">
                                    <img src="\StoryHub\public\images\authors\author5.jpg" alt="Author">
                                    <div class="author-details">
                                        <span class="author-name">Emma Wilson</span>
                                        <span class="publish-date">May 16, 2024</span>
                                    </div>
                                </div>
                                <div class="card-stats">
                                    <span title="Views"><i class="fas fa-eye"></i> 1.8K</span>
                                    <span title="Likes"><i class="fas fa-heart"></i> 234</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="load-more-section">
                <button class="btn-load-more">
                    <i class="fas fa-plus"></i> Load More Recommendations
                </button>
            </div>
        </div>
    </div>

    <!-- AI Insights Sidebar -->
    <aside class="fyp-sidebar">
        <div class="sidebar-section">
            <h3><i class="fas fa-brain"></i> Your Reading Profile</h3>
            <div class="reading-stats">
                <div class="stat-item">
                    <span class="stat-label">Articles Read</span>
                    <span class="stat-value">47</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Favorite Topics</span>
                    <span class="stat-value">Technology, Sports</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Reading Streak</span>
                    <span class="stat-value">12 days</span>
                </div>
            </div>
        </div>

        <div class="sidebar-section">
            <h3><i class="fas fa-chart-line"></i> Trending Topics</h3>
            <div class="trending-topics">
                <div class="topic-item">
                    <span class="topic-name">Artificial Intelligence</span>
                    <span class="topic-trend">+23%</span>
                </div>
                <div class="topic-item">
                    <span class="topic-name">Web Development</span>
                    <span class="topic-trend">+18%</span>
                </div>
                <div class="topic-item">
                    <span class="topic-name">Football Tactics</span>
                    <span class="topic-trend">+15%</span>
                </div>
                <div class="topic-item">
                    <span class="topic-name">Design Trends</span>
                    <span class="topic-trend">+12%</span>
                </div>
            </div>
        </div>

        <div class="sidebar-section">
            <h3><i class="fas fa-user-friends"></i> Similar Readers</h3>
            <div class="similar-readers">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="reader-item">
                    <img src="\StoryHub\public\images\authors\author6.jpg" alt="Reader">
                    <div class="reader-info">
                        <span class="name">Reader Name</span>
                        <span class="similarity">87% match</span>
                    </div>
                    <button class="btn-follow-sm">Follow</button>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </aside>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
