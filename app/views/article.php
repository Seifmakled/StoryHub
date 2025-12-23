<?php
$pageTitle = 'Story - StoryHub';
$pageDescription = 'Read this story on StoryHub';
$pageCSS = 'article.css';
$pageJS = 'article.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<script>
    window.CURRENT_USER_ID = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null'; ?>;
</script>

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
            <button class="btn-follow" id="followBtn" type="button">Follow</button>
        </div>
        <div class="article-actions">
            <button class="action-btn like" id="likeBtn" type="button" aria-pressed="false">
                <span class="action-icon"><i class="fas fa-heart"></i></span>
                <span class="action-text">Like</span>
                <span class="action-count" id="likeCount">0</span>
            </button>
            <button class="action-btn save" id="saveBtn" type="button" aria-pressed="false">
                <span class="action-icon"><i class="fas fa-bookmark"></i></span>
                <span class="action-text" id="saveLabel">Save</span>
            </button>
        </div>
        <div class="article-tags" id="articleTags"></div>
    </div>

    <article class="article-body" id="articleBody">
        <div class="empty-state loading">Loading content...</div>
    </article>

    <section class="article-comments" id="comments">
        <div class="comments-head">
            <h3>Comments</h3>
            <div class="tiny-text" id="commentStatus"></div>
        </div>
        <form id="commentForm" method="post" action="">
            <div class="comment-compose">
                <img class="comment-avatar" src="/StoryHub/public/images/<?php echo $_SESSION['profile_image'] ?? 'default-avatar.jpg'; ?>" alt="Your avatar">
                <div class="comment-input-wrap">
                    <textarea id="commentInput" placeholder="Write a comment..." rows="3"></textarea>
                    <div class="comment-actions">
                        <button type="submit" class="comment-submit" id="commentSubmit">Post</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="comments-list" id="commentsList">
            <div class="empty-state loading">Loading comments...</div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
