
/* ZORA cart micro-interactions */

document.addEventListener('DOMContentLoaded', () => {

    'use strict';

    const animateFeedback = (element) => {

        if (
            !element ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            return;
        }

        if (typeof element.animate === 'function') {

            element.animate(
                [
                    { transform: 'scale(1)' },
                    { transform: 'scale(1.025)' },
                    { transform: 'scale(1)' }
                ],
                {
                    duration: 260,
                    easing: 'ease-out'
                }
            );
        }
    };

    // Quantity validation
    document.querySelectorAll(
        '.quantity-form input[type="number"]'
    ).forEach((input) => {

        const normalize = () => {

            const min = Number(input.min || 1);

            const max = Number(input.max || 999);

            const raw = Number.parseInt(input.value, 10);

            const safeValue = Number.isFinite(raw) ? raw : min;

            input.value = String(
                Math.min(max, Math.max(min, safeValue))
            );
        };

        input.addEventListener('change', () => {

            normalize();

            animateFeedback(input.closest('.cart-item'));

        });

        input.addEventListener('blur', normalize);

    });

    // Update cart button
    document.querySelectorAll('.quantity-form').forEach((form) => {

        form.addEventListener('submit', () => {

            const button = form.querySelector(
                'button[type="submit"], button:not([type])'
            );

            if (!button) return;

            button.disabled = true;

            button.dataset.originalText =
                button.textContent.trim();

            button.textContent = 'Memperbarui…';

        });

    });

    // Add to cart button
    document.querySelectorAll('.add-cart-form').forEach((form) => {

        form.addEventListener('submit', () => {

            const button = form.querySelector(
                'button[type="submit"], button:not([type])'
            );

            if (!button) return;

            button.disabled = true;

            button.dataset.originalText =
                button.textContent.trim();

            button.textContent = 'Menambahkan…';

        });

    });

    // Remove cart item animation
    document.querySelectorAll('.remove-button').forEach((button) => {

        button.addEventListener('click', () => {

            const item = button.closest('.cart-item');

            if (
                !item ||
                window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ) {
                return;
            }

            item.animate(
                [
                    {
                        opacity: 1,
                        transform: 'translateX(0)'
                    },
                    {
                        opacity: 0.35,
                        transform: 'translateX(8px)'
                    }
                ],
                {
                    duration: 180,
                    easing: 'ease-out'
                }
            );

        });

    });

});
