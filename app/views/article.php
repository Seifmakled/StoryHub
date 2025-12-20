<?php
$pageTitle = 'Story - StoryHub';
$pageDescription = 'Read this story on StoryHub';
$pageCSS = 'article.css';
$pageJS = 'article.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="article-shell">
    <div class="article-hero" id="articleHero">
        <div class="article-cover" id="articleCover"></div>
        <div class="article-meta-top">
            <span class="pill" id="articleCategory">Story</span>
            <span class="muted" id="articleReading">Loading...</span>
        </div>
        <h1 id="articleTitle">Loading story...</h1>
        <p class="article-excerpt" id="articleExcerpt"></p>
        <div class="author-block" id="authorBlock">
            <img src="/StoryHub/public/images/default-avatar.jpg" alt="Author" id="authorAvatar">
            <div>
                <div class="author-name" id="authorName">—</div>
                <div class="author-meta" id="authorMeta">—</div>
            </div>
        </div>
        <div class="article-tags" id="articleTags"></div>
    </div>

    <article class="article-body" id="articleBody">
        <div class="empty-state loading">Loading content...</div>
    </article>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
