// Global layout interactions: page loader + nav helpers
(function() {
    const loader = document.getElementById('page-loader');
    if (!loader) return;

    const hideLoader = () => {
        loader.classList.add('is-hidden');
    };

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
})();
