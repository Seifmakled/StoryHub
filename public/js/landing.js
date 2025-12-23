// Landing Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const base = (typeof window.APP_BASE === 'string') ? window.APP_BASE : '';

    // Mobile Navigation Toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Search Functionality
    const navSearch = document.getElementById('navSearch');
    if (navSearch) {
        navSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchQuery = this.value.trim();
                if (searchQuery) {
                    window.location.href = `${base}index.php?url=explore&search=${encodeURIComponent(searchQuery)}`;
                }
            }
        });
    }

    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const observeCards = (selector) => {
        document.querySelectorAll(selector).forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    };

    // Render helpers
    const timeToRead = (text) => {
        const words = (text || '').split(/\s+/).filter(Boolean).length;
        return Math.max(1, Math.ceil(words / 220)) + ' min read';
    };

    const featuredGrid = document.getElementById('featuredGrid');
    const trendingGrid = document.getElementById('trendingGrid');

    const renderFeatured = (items) => {
        if (!featuredGrid) return;
        if (!items || items.length === 0) {
            featuredGrid.innerHTML = '<div class="empty-state">No featured stories yet.</div>';
            return;
        }

        const [first, ...rest] = items;
        const cover = first.featured_image ? `${base}public/images/${first.featured_image}` : `${base}public/images/article-placeholder.jpg`;
        const authorImg = first.profile_image ? `${base}public/images/${first.profile_image}` : `${base}public/images/default-avatar.jpg`;
        const author = first.full_name || first.username || 'Author';
        const date = first.created_at ? new Date(first.created_at).toLocaleDateString() : '';

        const large = `
            <div class="featured-card featured-large" data-slug="${first.slug}">
                <div class="featured-image">
                    <img src="${cover}" alt="Article">
                    <span class="featured-badge">Featured</span>
                </div>
                <div class="featured-content">
                    <div class="featured-meta">
                        <span class="category">${first.category || 'Story'}</span>
                        <span class="reading-time"><i class="far fa-clock"></i> ${timeToRead(first.excerpt)}</span>
                    </div>
                    <h3>${first.title}</h3>
                    <p>${first.excerpt || ''}</p>
                    <div class="author-info">
                        <img src="${authorImg}" alt="Author" class="author-avatar">
                        <div class="author-details">
                            <span class="author-name">${author}</span>
                            <span class="publish-date">${date}</span>
                        </div>
                    </div>
                </div>
            </div>`;

        const smallCards = rest.slice(0, 2).map(item => {
            const coverSm = item.featured_image ? `${base}public/images/${item.featured_image}` : `${base}public/images/article-placeholder.jpg`;
            const authorSm = item.full_name || item.username || 'Author';
            return `
            <div class="featured-card featured-small" data-slug="${item.slug}">
                <div class="featured-image">
                    <img src="${coverSm}" alt="Article">
                </div>
                <div class="featured-content">
                    <span class="category">${item.category || 'Story'}</span>
                    <h4>${item.title}</h4>
                    <div class="author-info">
                        <img src="${base}public/images/${item.profile_image || 'default-avatar.jpg'}" alt="Author" class="author-avatar">
                        <span class="author-name">${authorSm}</span>
                    </div>
                </div>
            </div>`;
        }).join('');

        featuredGrid.innerHTML = `
            ${large}
            <div class="featured-small-grid">${smallCards || ''}</div>
        `;

        featuredGrid.querySelectorAll('.featured-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') return;
                const slug = card.getAttribute('data-slug');
                if (slug) window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
            });
        });

        observeCards('.featured-card');
    };

    const renderTrending = (items) => {
        if (!trendingGrid) return;
        if (!items || items.length === 0) {
            trendingGrid.innerHTML = '<div class="empty-state">No trending stories yet.</div>';
            return;
        }

        trendingGrid.innerHTML = items.slice(0, 6).map((item, idx) => {
            const cover = item.featured_image ? `${base}public/images/${item.featured_image}` : `${base}public/images/article-placeholder.jpg`;
            return `
            <div class="trending-card" data-slug="${item.slug}">
                <div class="trending-image">
                    <img src="${cover}" alt="Article">
                    <div class="trending-overlay">
                        <span class="trending-number">#${idx + 1}</span>
                    </div>
                </div>
                <div class="trending-content">
                    <span class="category">${item.category || 'Story'}</span>
                    <h3>${item.title}</h3>
                    <div class="article-stats">
                        <span><i class="fas fa-eye"></i> ${item.views || 0}</span>
                        <span><i class="fas fa-heart"></i> ${item.likes_count || 0}</span>
                        <span><i class="fas fa-comment"></i> ${item.comments_count || 0}</span>
                    </div>
                </div>
            </div>`;
        }).join('');

        trendingGrid.querySelectorAll('.trending-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') return;
                const slug = card.getAttribute('data-slug');
                if (slug) window.location.href = `${base}index.php?url=article&slug=${encodeURIComponent(slug)}`;
            });
        });

        observeCards('.trending-card');
    };

    const fetchSection = async (params) => {
        const qs = new URLSearchParams(params).toString();
        const res = await fetch(`${base}index.php?url=api-articles&${qs}`);
        if (!res.ok) throw new Error('Failed to load');
        const data = await res.json();
        return data.data || [];
    };

    (async function initSections() {
        try {
            let [featured, trending] = await Promise.all([
                fetchSection({ featured: 1, limit: 3 }),
                fetchSection({ sort: 'likes', limit: 6 })
            ]);

            // Fallbacks if nothing is featured or no likes yet
            if (!featured || featured.length === 0) {
                featured = await fetchSection({ limit: 3 });
            }
            if (!trending || trending.length === 0) {
                trending = await fetchSection({ sort: 'latest', limit: 6 });
            }

            renderFeatured(featured);
            renderTrending(trending);
        } catch (err) {
            if (featuredGrid) featuredGrid.innerHTML = '<div class="empty-state">Could not load featured stories.</div>';
            if (trendingGrid) trendingGrid.innerHTML = '<div class="empty-state">Could not load trending stories.</div>';
            console.error(err);
        }
    })();

    // Hero 3D Model (StoryHub.obj)
    (async function initHeroModel() {
        const canvas = document.getElementById('storyhubModelCanvas');
        if (!canvas) return;

        const fallback = document.getElementById('storyhubModelFallback');
        const showFallback = (message) => {
            if (!fallback) return;
            fallback.textContent = message || '3D preview unavailable.';
            fallback.hidden = false;
        };

        const container = canvas.closest('.hero-model') || canvas.parentElement;
        if (!container) {
            showFallback('3D preview unavailable.');
            return;
        }

        const modelUrl = `${base}public/models/StoryHub.obj`;

        const hasWebGL = () => {
            try {
                const testCanvas = document.createElement('canvas');
                return !!(window.WebGLRenderingContext && (testCanvas.getContext('webgl') || testCanvas.getContext('experimental-webgl')));
            } catch (_) {
                return false;
            }
        };

        if (!hasWebGL()) {
            showFallback('3D preview unavailable (WebGL not supported).');
            return;
        }

        let renderer;
        let camera;
        let scene;
        let model;
        let animationId;

        const setRendererSize = () => {
            const width = Math.max(1, container.clientWidth);
            const height = Math.max(1, container.clientHeight);
            if (renderer) renderer.setSize(width, height, false);
            if (camera) {
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
            }
        };

        try {
            let THREE;
            let OBJLoader;
            try {
                // Prefer local files via import map (works offline / when CDN is blocked)
                THREE = await import('three');
                ({ OBJLoader } = await import('three/addons/loaders/OBJLoader.js'));
            } catch (localErr) {
                console.error('Local Three.js import failed:', localErr);
                try {
                    // Fallback to CDN
                    THREE = await import('https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js');
                    ({ OBJLoader } = await import('https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/loaders/OBJLoader.js'));
                } catch (cdnErr) {
                    console.error('CDN Three.js import failed:', cdnErr);
                    showFallback('3D preview unavailable (failed to load 3D engine).');
                    return;
                }
            }

            scene = new THREE.Scene();

            renderer = new THREE.WebGLRenderer({
                canvas,
                antialias: true,
                alpha: true,
                powerPreference: 'high-performance'
            });
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

            camera = new THREE.PerspectiveCamera(45, 1, 0.1, 1000);
            camera.position.set(0, 0.9, 3.2);

            const ambient = new THREE.AmbientLight(0xffffff, 0.85);
            scene.add(ambient);

            const keyLight = new THREE.DirectionalLight(0xffffff, 0.9);
            keyLight.position.set(2.5, 4, 2);
            scene.add(keyLight);

            const fillLight = new THREE.DirectionalLight(0xffffff, 0.35);
            fillLight.position.set(-2.5, 1, 2);
            scene.add(fillLight);

            setRendererSize();

            const loader = new OBJLoader();
            loader.load(
                modelUrl,
                (obj) => {
                    model = obj;

                    // Center + scale to a consistent size
                    const box = new THREE.Box3().setFromObject(model);
                    const size = box.getSize(new THREE.Vector3());
                    const center = box.getCenter(new THREE.Vector3());

                    model.position.x -= center.x;
                    model.position.y -= center.y;
                    model.position.z -= center.z;

                    const maxDim = Math.max(size.x, size.y, size.z) || 1;
                    const scale = 1.8 / maxDim;
                    model.scale.setScalar(scale);

                    // Slight tilt so it reads better in the hero
                    model.rotation.x = -0.08;
                    model.rotation.y = 0.35;

                    // If materials are missing, make it visible with a neutral material
                    model.traverse((child) => {
                        if (!child || !child.isMesh) return;
                        if (!child.material) {
                            child.material = new THREE.MeshStandardMaterial({ color: 0xffffff, metalness: 0.1, roughness: 0.8 });
                        }
                        child.castShadow = false;
                        child.receiveShadow = false;
                    });

                    scene.add(model);
                },
                undefined,
                async (e) => {
                    console.error('OBJ load failed:', e);
                    try {
                        const res = await fetch(modelUrl, { method: 'HEAD', cache: 'no-store' });
                        if (!res.ok) {
                            showFallback(`3D preview unavailable (model not found: ${res.status}).`);
                            return;
                        }
                    } catch (headErr) {
                        console.error('OBJ HEAD check failed:', headErr);
                    }
                    showFallback('3D preview unavailable (failed to load model).');
                }
            );

            const animate = () => {
                animationId = window.requestAnimationFrame(animate);
                if (model) model.rotation.y += 0.003;
                renderer.render(scene, camera);
            };
            animate();

            if ('ResizeObserver' in window) {
                const ro = new ResizeObserver(() => setRendererSize());
                ro.observe(container);
            } else {
                window.addEventListener('resize', setRendererSize);
            }

            window.addEventListener('beforeunload', () => {
                if (animationId) window.cancelAnimationFrame(animationId);
                try {
                    renderer?.dispose?.();
                } catch (_) {
                    // ignore
                }
            });
        } catch (err) {
            console.error('3D init failed:', err);
            showFallback('3D preview unavailable (initialization error).');
        }
    })();
});
