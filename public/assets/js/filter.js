
/* ZORA catalog filters */

document.addEventListener('DOMContentLoaded', () => {

    'use strict';

    const form = document.querySelector('.filter-bar');

    if (!form) return;

    const submitFilters = () => {

        if (typeof form.requestSubmit === 'function') {

            form.requestSubmit();

        } else {

            form.submit();

        }
    };

    // Auto submit ketika kategori atau sorting berubah
    form.querySelectorAll('select').forEach((select) => {

        select.addEventListener('change', submitFilters);

    });

    // Search menggunakan Enter
    const searchInput = form.querySelector(
        'input[type="search"], input[name="q"], .search-field input'
    );

    if (searchInput) {

        searchInput.addEventListener('keydown', (event) => {

            if (event.key === 'Enter') {

                event.preventDefault();

                submitFilters();

            }

        });

    }

});
