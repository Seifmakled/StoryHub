<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <!-- About Section -->
            <div class="footer-section">
                <h3 class="footer-logo">
                    <i class="fas fa-feather-alt"></i>
                    StoryHub
                </h3>
                <p>A platform where stories come to life. Share your voice, connect with readers, and discover amazing content from writers around the world.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h4>Explore</h4>
                <ul>
                    <li><a href="index.php?page=explore">All Articles</a></li>
                    <li><a href="index.php?page=explore&filter=trending">Trending</a></li>
                    <li><a href="index.php?page=explore&filter=featured">Featured</a></li>
                    <li><a href="index.php?page=explore&filter=recent">Recent</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="footer-section">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#guidelines">Writing Guidelines</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                    <li><a href="#careers">Careers</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#terms">Terms of Service</a></li>
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#cookies">Cookie Policy</a></li>
                    <li><a href="#dmca">DMCA</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> StoryHub. All rights reserved.</p>
            <p>Made with <i class="fas fa-heart"></i> by passionate writers</p>
        </div>
    </div>
</footer>

<!-- Page-specific JavaScript -->
<?php if (isset($pageJS)): ?>
    <script src="/StoryHub/public/js/<?php echo $pageJS; ?>"></script>
<?php endif; ?>

</body>
</html>
