// FYP (For You Page) JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize FYP functionality
    initFYP();
});

function initFYP() {
    // Initialize search functionality
    initSearch();
    
    // Initialize filter functionality
    initFilters();
    
    // Initialize save functionality
    initSaveButtons();
    
    // Initialize load more functionality
    initLoadMore();
    
    // Initialize follow buttons
    initFollowButtons();
    
    // Initialize reading progress
    initReadingProgress();
    
    // Initialize animations
    initAnimations();
}

// Search functionality
function initSearch() {
    const searchInput = document.getElementById('fypSearch');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            showAllSections();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
    
    // Clear search on escape
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            showAllSections();
        }
    });
}

function performSearch(query) {
    const sections = document.querySelectorAll('.recommendation-section');
    const queryLower = query.toLowerCase();
    let hasResults = false;
    
    sections.forEach(section => {
        const cards = section.querySelectorAll('.fyp-card');
        let sectionHasResults = false;
        
        cards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const content = card.querySelector('p').textContent.toLowerCase();
            const author = card.querySelector('.author-name').textContent.toLowerCase();
            const category = card.querySelector('.category').textContent.toLowerCase();
            
            const matches = title.includes(queryLower) || 
                          content.includes(queryLower) || 
                          author.includes(queryLower) || 
                          category.includes(queryLower);
            
            if (matches) {
                card.style.display = 'flex';
                sectionHasResults = true;
                hasResults = true;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide section based on results
        if (sectionHasResults) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
    
    // Show no results message if needed
    showNoResultsMessage(!hasResults);
}

function showAllSections() {
    const sections = document.querySelectorAll('.recommendation-section');
    const cards = document.querySelectorAll('.fyp-card');
    
    sections.forEach(section => {
        section.style.display = 'block';
    });
    
    cards.forEach(card => {
        card.style.display = 'flex';
    });
    
    showNoResultsMessage(false);
}

function showNoResultsMessage(show) {
    let noResultsMsg = document.getElementById('noResultsMessage');
    
    if (show && !noResultsMsg) {
        noResultsMsg = document.createElement('div');
        noResultsMsg.id = 'noResultsMessage';
        noResultsMsg.className = 'no-results-message';
        noResultsMsg.innerHTML = `
            <div class="no-results-content">
                <i class="fas fa-search"></i>
                <h3>No articles found</h3>
                <p>Try adjusting your search terms or explore different categories</p>
            </div>
        `;
        
        const container = document.querySelector('.fyp-content .container');
        container.appendChild(noResultsMsg);
    } else if (!show && noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Filter functionality
function initFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Apply filter
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });
}

function applyFilter(filter) {
    const sections = document.querySelectorAll('.recommendation-section');
    
    sections.forEach(section => {
        const cards = section.querySelectorAll('.fyp-card');
        
        cards.forEach(card => {
            let shouldShow = true;
            
            switch(filter) {
                case 'trending':
                    shouldShow = card.querySelector('.recommendation-badge.trending') !== null;
                    break;
                case 'similar':
                    shouldShow = section.id === 'becauseYouLiked' || section.id === 'maybeYouWillLike';
                    break;
                case 'new':
                    shouldShow = section.id === 'dontMissOut' || section.id === 'continueReading';
                    break;
                case 'all':
                default:
                    shouldShow = true;
                    break;
            }
            
            card.style.display = shouldShow ? 'flex' : 'none';
        });
        
        // Show/hide section based on visible cards
        const visibleCards = section.querySelectorAll('.fyp-card[style*="flex"], .fyp-card:not([style*="none"])');
        section.style.display = visibleCards.length > 0 ? 'block' : 'none';
    });
}

// Save functionality
function initSaveButtons() {
    const saveButtons = document.querySelectorAll('.btn-save');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const icon = this.querySelector('i');
            const isSaved = this.classList.contains('saved');
            
            if (isSaved) {
                // Remove from saved
                this.classList.remove('saved');
                icon.className = 'far fa-bookmark';
                showNotification('Article removed from saved', 'info');
            } else {
                // Add to saved
                this.classList.add('saved');
                icon.className = 'fas fa-bookmark';
                showNotification('Article saved!', 'success');
            }
            
            // Animate the button
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
}

// Load more functionality
function initLoadMore() {
    const loadMoreBtn = document.querySelector('.btn-load-more');
    if (!loadMoreBtn) return;
    
    loadMoreBtn.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        this.disabled = true;
        
        // Simulate loading more content
        setTimeout(() => {
            loadMoreContent();
            this.innerHTML = '<i class="fas fa-plus"></i> Load More Recommendations';
            this.disabled = false;
        }, 1500);
    });
}

function loadMoreContent() {
    // Create new recommendation section
    const newSection = document.createElement('div');
    newSection.className = 'recommendation-section';
    newSection.innerHTML = `
        <div class="section-header">
            <h2><i class="fas fa-rocket"></i> More for you</h2>
            <p>Additional recommendations based on your interests</p>
        </div>
        <div class="articles-container grid-view">
            ${generateNewCards(3)}
        </div>
    `;
    
    // Insert before load more section
    const loadMoreSection = document.querySelector('.load-more-section');
    loadMoreSection.parentNode.insertBefore(newSection, loadMoreSection);
    
    // Reinitialize functionality for new cards
    initSaveButtons();
    
    // Animate the new section
    newSection.style.opacity = '0';
    newSection.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
        newSection.style.transition = 'all 0.6s ease-out';
        newSection.style.opacity = '1';
        newSection.style.transform = 'translateY(0)';
    }, 100);
    
    showNotification('New recommendations loaded!', 'success');
}

function generateNewCards(count) {
    const cards = [];
    const titles = [
        'The Future of Remote Work',
        'Sustainable Living Tips',
        'Digital Marketing Trends 2024',
        'Healthy Cooking Made Easy',
        'Photography Techniques',
        'Financial Planning Guide'
    ];
    
    const authors = ['Alex Smith', 'Maria Garcia', 'David Chen', 'Sarah Johnson', 'Mike Wilson', 'Emma Brown'];
    const categories = ['Technology', 'Lifestyle', 'Business', 'Health', 'Arts', 'Finance'];
    
    for (let i = 0; i < count; i++) {
        const randomTitle = titles[Math.floor(Math.random() * titles.length)];
        const randomAuthor = authors[Math.floor(Math.random() * authors.length)];
        const randomCategory = categories[Math.floor(Math.random() * categories.length)];
        
        cards.push(`
            <article class="fyp-card">
                <div class="card-image">
                    <img src="\\StoryHub\\public\\images\\articles\\AI.jpg" alt="Article">
                    <div class="card-overlay">
                        <button class="btn-save" title="Save article">
                            <i class="far fa-bookmark"></i>
                        </button>
                    </div>
                    <div class="recommendation-badge">
                        <i class="fas fa-magic"></i> New
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-meta">
                        <span class="category">${randomCategory}</span>
                        <span class="reading-time"><i class="far fa-clock"></i> ${Math.floor(Math.random() * 10) + 3} min</span>
                    </div>
                    <h3>${randomTitle}</h3>
                    <p>Discover amazing insights and tips that will help you stay ahead of the curve...</p>
                    <div class="card-footer">
                        <div class="author-info">
                            <img src="\\StoryHub\\public\\images\\authors\\author1.jpg" alt="Author">
                            <div class="author-details">
                                <span class="author-name">${randomAuthor}</span>
                                <span class="publish-date">May ${Math.floor(Math.random() * 30) + 1}, 2024</span>
                            </div>
                        </div>
                        <div class="card-stats">
                            <span title="Views"><i class="fas fa-eye"></i> ${Math.floor(Math.random() * 5000) + 500}</span>
                            <span title="Likes"><i class="fas fa-heart"></i> ${Math.floor(Math.random() * 1000) + 50}</span>
                        </div>
                    </div>
                </div>
            </article>
        `);
    }
    
    return cards.join('');
}

// Follow functionality
function initFollowButtons() {
    const followButtons = document.querySelectorAll('.btn-follow-sm');
    
    followButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const isFollowing = this.textContent === 'Following';
            
            if (isFollowing) {
                this.textContent = 'Follow';
                this.style.background = 'var(--primary-color)';
                showNotification('Unfollowed successfully', 'info');
            } else {
                this.textContent = 'Following';
                this.style.background = 'var(--success-color)';
                showNotification('Following successfully!', 'success');
            }
        });
    });
}

// Reading progress functionality
function initReadingProgress() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    progressBars.forEach(bar => {
        const card = bar.closest('.fyp-card');
        
        card.addEventListener('click', function() {
            // Simulate reading progress update
            const currentWidth = parseInt(bar.style.width) || 0;
            const newWidth = Math.min(currentWidth + 20, 100);
            
            bar.style.width = newWidth + '%';
            
            const progressText = bar.parentElement.querySelector('.progress-text');
            progressText.textContent = newWidth + '% read';
            
            if (newWidth >= 100) {
                showNotification('Article completed!', 'success');
            }
        });
    });
}

// Animation functionality
function initAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe recommendation sections
    const sections = document.querySelectorAll('.recommendation-section');
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'all 0.6s ease-out';
        observer.observe(section);
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getNotificationColor(type) {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#6366f1'
    };
    return colors[type] || '#6366f1';
}

// Card click functionality
document.addEventListener('click', function(e) {
    const card = e.target.closest('.fyp-card');
    if (card && !e.target.closest('.btn-save')) {
        // Simulate article view
        const title = card.querySelector('h3').textContent;
        showNotification(`Opening "${title}"...`, 'info');
        
        // In a real application, this would navigate to the article page
        // window.location.href = `article.php?id=${card.dataset.id}`;
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('fypSearch');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('fypSearch');
        if (searchInput && document.activeElement === searchInput) {
            searchInput.value = '';
            showAllSections();
        }
    }
});

// Add CSS for notifications
const notificationStyles = `
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .notification-content i {
        font-size: 1.1rem;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);
