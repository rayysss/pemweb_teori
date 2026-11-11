
/* ZORA checkout interactions */

document.addEventListener('DOMContentLoaded', () => {

    'use strict';

    const form = document.querySelector(
        '.checkout-layout form'
    );

    const paymentOptions = document.querySelectorAll(
        '.payment-options input[type="radio"]'
    );

    // Payment selection
    paymentOptions.forEach((input) => {

        input.addEventListener('change', () => {

            document.querySelectorAll(
                '.payment-options label'
            ).forEach((label) => {

                label.classList.toggle(
                    'is-selected',
                    label.contains(input) && input.checked
                );

            });

        });

    });

    if (!form) return;

    // Checkout validation
    form.addEventListener('submit', (event) => {

        if (!form.reportValidity()) {

            event.preventDefault();

            const invalid = form.querySelector(':invalid');

            if (invalid) invalid.focus();

            return;
        }

        const button = form.querySelector(
            'button[type="submit"], button:not([type])'
        );

        if (button) {

            button.disabled = true;

            button.setAttribute('aria-busy', 'true');

            button.textContent = 'Memproses pesanan…';

        }

    });

});
