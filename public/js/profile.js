// Profile Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(`${targetTab}-tab`).classList.add('active');
        });
    });

    // Follow Button
    const followBtn = document.getElementById('followBtn');
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            if (this.innerHTML.includes('Follow')) {
                this.innerHTML = '<i class="fas fa-check"></i> Following';
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline');
            } else {
                this.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
                this.classList.remove('btn-outline');
                this.classList.add('btn-primary');
            }
        });
    }

    // Article Card Click
    document.querySelectorAll('.article-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on menu
            if (!e.target.closest('.article-menu')) {
                window.location.href = 'index.php?page=article&id=1';
            }
        });
    });

    // Edit Cover Photo
    const btnEditCover = document.querySelector('.btn-edit-cover');
    if (btnEditCover) {
        btnEditCover.addEventListener('click', function() {
            alert('Cover photo upload functionality would go here');
        });
    }

    // Edit Avatar
    const btnEditAvatar = document.querySelector('.btn-edit-avatar');
    if (btnEditAvatar) {
        btnEditAvatar.addEventListener('click', function() {
            alert('Profile picture upload functionality would go here');
        });
    }

    // Article Menu Dropdown
    document.querySelectorAll('.btn-menu').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            
            // Close all other dropdowns
            document.querySelectorAll('.menu-dropdown').forEach(d => {
                if (d !== dropdown) {
                    d.style.opacity = '0';
                    d.style.visibility = 'hidden';
                }
            });
        });
    });

    // Article Menu Actions
    document.querySelectorAll('.menu-dropdown a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const action = this.getAttribute('href').substring(1);
            
            if (action === 'edit') {
                alert('Edit article functionality would go here');
            } else if (action === 'delete') {
                if (confirm('Are you sure you want to delete this article?')) {
                    alert('Delete article functionality would go here');
                }
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
            dropdown.style.opacity = '0';
            dropdown.style.visibility = 'hidden';
        });
    });

    // Social Links
    document.querySelectorAll('.social-links a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Social link would open here');
        });
    });
});
