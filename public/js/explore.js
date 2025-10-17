// Explore Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Search Functionality
    const exploreSearch = document.getElementById('exploreSearch');
    if (exploreSearch) {
        exploreSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    function performSearch() {
        const query = exploreSearch.value.trim();
        if (query) {
            // In real app, filter articles or make API call
            console.log('Searching for:', query);
        }
    }

    // Filter Buttons
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            // In real app, filter articles based on selection
            console.log('Filter selected:', filter);
        });
    });

    // View Toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const articlesContainer = document.getElementById('articlesContainer');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            articlesContainer.className = `articles-container ${view}-view`;
        });
    });

    // Save Article Button
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            this.classList.toggle('saved');
            const icon = this.querySelector('i');
            
            if (this.classList.contains('saved')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });
    });

    // Article Card Click
    document.querySelectorAll('.explore-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-save')) {
                window.location.href = 'index.php?page=article&id=1';
            }
        });
    });

    // Follow Author Button
    document.querySelectorAll('.btn-follow-sm').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.textContent === 'Follow') {
                this.textContent = 'Following';
                this.style.background = '#e2e8f0';
                this.style.color = '#334155';
            } else {
                this.textContent = 'Follow';
                this.style.background = '';
                this.style.color = '';
            }
        });
    });

    // Pagination
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.disabled && !this.classList.contains('active')) {
                document.querySelectorAll('.page-btn').forEach(b => {
                    if (!isNaN(b.textContent)) {
                        b.classList.remove('active');
                    }
                });
                
                if (!isNaN(this.textContent)) {
                    this.classList.add('active');
                }
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // In real app, load new page of articles
                console.log('Loading page:', this.textContent);
            }
        });
    });

    // Category Dropdown
    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category || this.dataset.sort;
            console.log('Selected:', category);
            
            // Update button text
            const toggle = this.closest('.filter-dropdown').querySelector('.dropdown-toggle');
            const text = this.textContent;
            const icon = toggle.querySelector('i').outerHTML;
            const chevron = '<i class="fas fa-chevron-down"></i>';
            toggle.innerHTML = icon + ' ' + text + ' ' + chevron;
        });
    });

    // Tags Click
    document.querySelectorAll('.tag').forEach(tag => {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            const tagName = this.textContent;
            console.log('Tag selected:', tagName);
            exploreSearch.value = tagName;
            performSearch();
        });
    });

    // Author Item Click
    document.querySelectorAll('.author-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-follow-sm')) {
                window.location.href = 'index.php?page=profile&id=1';
            }
        });
    });
});
