
/* ==========================================================
   ZORA — Visual Motion System
   ========================================================== */

(() => {
    'use strict';

    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    let animePromise = null;

    // Load Anime.js
    function getAnime() {
        if (reduceMotion) {
            return Promise.resolve(null);
        }

        if (window.anime) {
            return Promise.resolve(window.anime);
        }

        if (animePromise) {
            return animePromise;
        }

        animePromise = new Promise((resolve) => {
            const script = document.createElement('script');

            script.src =
                'https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js';

            script.async = true;

            script.onload = () => {
                resolve(window.anime || null);
            };

            script.onerror = () => {
                resolve(null);
            };

            document.head.appendChild(script);
        });

        return animePromise;
    }

    // Mobile navigation
    function initMobileNavigation() {
        const toggle = document.querySelector('.mobile-menu-toggle');
        const nav = document.querySelector('.main-nav');

        if (!toggle || !nav) return;

        toggle.setAttribute(
            'aria-expanded',
            nav.classList.contains('open') ? 'true' : 'false'
        );

        toggle.setAttribute(
            'aria-label',
            'Buka atau tutup navigasi'
        );

        toggle.addEventListener('click', () => {
            const opened = nav.classList.toggle('open');

            toggle.setAttribute(
                'aria-expanded',
                opened ? 'true' : 'false'
            );

            if (opened && !reduceMotion) {
                const links = nav.querySelectorAll('a');

                links.forEach((link, index) => {
                    link.animate(
                        [
                            {
                                opacity: 0,
                                transform: 'translateY(-7px)'
                            },
                            {
                                opacity: 1,
                                transform: 'translateY(0)'
                            }
                        ],
                        {
                            duration: 260,
                            delay: index * 35,
                            easing: 'ease-out'
                        }
                    );
                });
            }
        });

        // Close navigation outside the menu
        document.addEventListener('click', (event) => {
            if (
                !nav.contains(event.target) &&
                !toggle.contains(event.target)
            ) {
                nav.classList.remove('open');

                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close navigation using Escape
        document.addEventListener('keydown', (event) => {
            if (
                event.key === 'Escape' &&
                nav.classList.contains('open')
            ) {
                nav.classList.remove('open');

                toggle.setAttribute('aria-expanded', 'false');

                toggle.focus();
            }
        });
    }

    // Header shadow on scroll
    function initHeaderScroll() {
        const header = document.querySelector('.site-header');

        if (!header) return;

        const update = () => {
            header.classList.toggle(
                'is-scrolled',
                window.scrollY > 8
            );
        };

        update();

        window.addEventListener('scroll', update, {
            passive: true
        });
    }

    // Hero entrance animation
    function initHeroAnimation() {
        const copy = document.querySelector('.hero-copy');
        const visual = document.querySelector('.hero-visual');

        if (!copy && !visual) return;

        const items = copy
            ? Array.from(
                copy.querySelectorAll(
                    '.eyebrow, h1, p, .btn-zora, .hero-note'
                )
            )
            : [];

        if (reduceMotion) {
            items.forEach((item) => {
                item.classList.add('zora-motion-ready');
            });

            if (visual) {
                visual.classList.add('zora-motion-ready');
            }

            return;
        }

        items.forEach((item) => {
            item.classList.add('zora-hero-item');
        });

        if (visual) {
            visual.classList.add('zora-hero-visual');
        }

        getAnime().then((anime) => {

            if (anime && items.length) {
                anime({
                    targets: items,
                    opacity: [0, 1],
                    translateY: [24, 0],
                    duration: 760,
                    delay: anime.stagger(95, {
                        start: 80
                    }),
                    easing: 'easeOutCubic',

                    complete: () => {
                        items.forEach((item) => {
                            item.classList.add('zora-motion-ready');
                        });
                    }
                });

            } else {
                items.forEach((item, index) => {
                    item.classList.add('zora-motion-fallback');

                    item.style.animationDelay =
                        `${80 + index * 95}ms`;
                });
            }

            // Hero image animation
            if (visual) {
                visual.classList.add('zora-motion-ready');

                if (typeof visual.animate === 'function') {
                    visual.animate(
                        [
                            {
                                opacity: 0,
                                transform: 'translateY(16px) scale(.985)'
                            },
                            {
                                opacity: 1,
                                transform: 'translateY(0) scale(1)'
                            }
                        ],
                        {
                            duration: 850,
                            delay: 150,
                            easing: 'cubic-bezier(.2,.7,.25,1)',
                            fill: 'both'
                        }
                    );
                }
            }
        });
    }

    // Reveal elements when scrolling
    function initScrollReveal() {

        const selector = [
            '.section-heading',
            '.category-card',
            '.product-card',
            '.service-strip > div',
            '.page-banner .container > *',
            '.product-detail > *',
            '.standard-page > .page-title',
            '.standard-page > .cart-layout > *',
            '.standard-page > .checkout-layout > *',
            '.auth-card',
            '.account-panel',
            '.admin-stats > div',
            '.admin-product-list',
            '.admin-product-layout > .form-card',
            '.empty-state'
        ].join(',');

        const elements = Array.from(
            document.querySelectorAll(selector)
        );

        if (!elements.length) return;

        elements.forEach((element, index) => {
            element.classList.add('zora-reveal');

            const position = index % 4;

            element.style.setProperty(
                '--zora-reveal-delay',
                `${position * 65}ms`
            );
        });

        if (
            reduceMotion ||
            !('IntersectionObserver' in window)
        ) {
            elements.forEach((element) => {
                element.classList.add('is-visible');
            });

            return;
        }

        const observer = new IntersectionObserver(
            (entries, activeObserver) => {

                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;

                    entry.target.classList.add('is-visible');

                    activeObserver.unobserve(entry.target);
                });

            },
            {
                threshold: 0.12,
                rootMargin: '0px 0px -32px 0px'
            }
        );

        elements.forEach((element) => {
            observer.observe(element);
        });
    }

    // Button interaction
    function initButtonFeedback() {
        document.querySelectorAll(
            '.btn-zora, .header-cart, .mobile-menu-toggle, .subtle-button, .remove-button'
        ).forEach((element) => {
            element.classList.add('zora-interactive');
        });
    }

    // Smooth anchor scrolling
    function initSmoothAnchors() {
        document.querySelectorAll('a[href^="#"]').forEach((link) => {

            link.addEventListener('click', (event) => {

                const href = link.getAttribute('href');

                if (!href || href === '#') return;

                const target = document.querySelector(href);

                if (!target) return;

                event.preventDefault();

                target.scrollIntoView({
                    behavior: reduceMotion ? 'auto' : 'smooth',
                    block: 'start'
                });
            });
        });
    }

    // Initialize all animations
    document.addEventListener('DOMContentLoaded', () => {

        initMobileNavigation();

        initHeaderScroll();

        initHeroAnimation();

        initScrollReveal();

        initButtonFeedback();

        initSmoothAnchors();

    });

})();
