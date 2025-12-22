// Global layout interactions: page loader + nav helpers
(function() {
    const loader = document.getElementById('page-loader');

    const hideLoader = () => {
        if (loader) loader.classList.add('is-hidden');
    };

    if (loader) {
        // Ensure the loader starts visible (avoid stale class if navigating back)
        loader.classList.remove('is-hidden');

        // Fixed visible duration aligned to the single fill + scale (~1.3s) plus fade
        const VISIBLE_MS = 1000; // adjust to taste
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(hideLoader, VISIBLE_MS);
            // Hard stop safety in case DOMContentLoaded fires super early
            setTimeout(hideLoader, VISIBLE_MS + 800);
        });

        // Show loader on same-origin navigations for a quick transition
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
            if (link.target === '_blank') return;
            const href = link.getAttribute('href') || '';
            if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            const url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
            loader.classList.remove('is-hidden');
        });
    }

    // Mobile nav toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        navMenu.addEventListener('click', (e) => {
            if (e.target.closest('a')) {
                navMenu.classList.remove('active');
            }
        });
    }

    // Explore mega dropdown interactions
    const navExplore = document.getElementById('navExplore');
    if (navExplore) {
        const trigger = navExplore.querySelector('.nav-explore-trigger');
        const dropdown = navExplore.querySelector('.mega-dropdown');
        let openTimer;
        let closeTimer;

        const setOpen = (state) => {
            if (!trigger) return;
            trigger.setAttribute('aria-expanded', state ? 'true' : 'false');
            navExplore.classList.toggle('is-open', state);
        };

        const openMenu = () => {
            clearTimeout(closeTimer);
            openTimer = setTimeout(() => setOpen(true), 80);
        };

        const closeMenu = () => {
            clearTimeout(openTimer);
            closeTimer = setTimeout(() => setOpen(false), 120);
        };

        navExplore.addEventListener('mouseenter', openMenu);
        navExplore.addEventListener('mouseleave', closeMenu);

        navExplore.addEventListener('focusin', openMenu);
        navExplore.addEventListener('focusout', (e) => {
            if (!navExplore.contains(e.relatedTarget)) closeMenu();
        });

        if (trigger) {
            trigger.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    setOpen(!navExplore.classList.contains('is-open'));
                }
            });
        }

        document.addEventListener('click', (e) => {
            if (!navExplore.contains(e.target)) setOpen(false);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') setOpen(false);
        });

        // Prevent accidental close while interacting inside dropdown
        if (dropdown) {
            dropdown.addEventListener('mouseenter', openMenu);
            dropdown.addEventListener('mouseleave', closeMenu);
        }
    }
})();
