/**
 * Filtres / tri client sur la grille .jb-products-grid (category.php rich).
 * Attend des cartes .jb-rich-card avec data-name, data-price, data-stock, data-sub (minuscules).
 */
(function () {
    const slug = (window.JB_CATEGORY_SLUG || 'all').toLowerCase();

    function cards() {
        return Array.from(document.querySelectorAll('#jb-products-grid .jb-rich-card'));
    }

    function updateCount(n) {
        const el = document.getElementById('jb-filter-count');
        if (!el) return;
        const labels = { bijoux: 'bijoux', soins: 'soins', coffrets: 'coffrets', all: 'articles' };
        const w = labels[slug] || 'articles';
        el.textContent = n + ' ' + w + ' disponibles';
    }

    function subMatches(card, checked) {
        if (!checked.length) return true;
        const sub = (card.dataset.sub || '').toLowerCase();
        return checked.some((v) => {
            if (slug === 'bijoux') {
                if (v === 'bracelets') return sub.includes('bracelet');
                if (v === 'bagues') return sub.includes('bague');
                if (v === 'colliers') return sub.includes('collier');
            }
            if (slug === 'soins') {
                if (v === 'corps') return sub.includes('corps');
                if (v === 'visage') return sub.includes('visage');
                if (v === 'rituels') return sub.includes('rituel') || sub.includes('beauté');
            }
            return sub.includes(v);
        });
    }

    function priceMatches(price, checked, mode) {
        if (!checked.length) return true;
        return checked.some((r) => {
            if (mode === 'bijoux' || mode === 'soins') {
                if (r === '0-25') return price < 25;
                if (r === '25-50') return price >= 25 && price <= 50;
                if (r === '50+') return price > 50;
            }
            if (mode === 'coffrets') {
                if (r === '0-50') return price < 50;
                if (r === '50-100') return price >= 50 && price <= 100;
                if (r === '100+') return price > 100;
            }
            return false;
        });
    }

    window.jbFilterProducts = function jbFilterProducts() {
        const grid = document.getElementById('jb-products-grid');
        if (!grid) return;

        const filt = document.querySelector('.jb-filters');
        const checkedSub = filt ? Array.from(filt.querySelectorAll('.jb-filter-sub:checked')).map((c) => c.value) : [];
        const checkedPrice = filt ? Array.from(filt.querySelectorAll('.jb-filter-price:checked')).map((c) => c.value) : [];
        const inStock = filt ? filt.querySelector('.jb-filter-stock:checked') : null;

        let visible = 0;
        cards().forEach((card) => {
            const price = parseFloat(card.dataset.price || '0');
            const stock = parseInt(card.dataset.stock || '0', 10);
            let show = true;

            if (slug === 'bijoux' || slug === 'soins') {
                show = subMatches(card, checkedSub) && priceMatches(price, checkedPrice, slug);
            } else if (slug === 'coffrets') {
                show = priceMatches(price, checkedPrice, 'coffrets');
            } else if (slug !== 'all') {
                show = priceMatches(price, checkedPrice, 'bijoux');
            }

            if (inStock && show) show = stock > 0;

            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        updateCount(visible);
    };

    window.jbSortProducts = function jbSortProducts(sortBy) {
        const grid = document.getElementById('jb-products-grid');
        if (!grid) return;
        const list = cards();
        list.sort((a, b) => {
            if (sortBy === 'default') {
                return parseInt(a.dataset.idx || '0', 10) - parseInt(b.dataset.idx || '0', 10);
            }
            const an = (a.dataset.name || '').toLowerCase();
            const bn = (b.dataset.name || '').toLowerCase();
            const ap = parseFloat(a.dataset.price || '0');
            const bp = parseFloat(b.dataset.price || '0');
            if (sortBy === 'name') return an.localeCompare(bn, 'fr');
            if (sortBy === 'price_asc') return ap - bp;
            if (sortBy === 'price_desc') return bp - ap;
            if (sortBy === 'newest') return parseInt(b.dataset.id || '0', 10) - parseInt(a.dataset.id || '0', 10);
            return 0;
        });
        list.forEach((el) => grid.appendChild(el));
    };

    window.jbResetFilters = function jbResetFilters() {
        document.querySelectorAll('.jb-filters input[type="checkbox"]').forEach((c) => {
            c.checked = false;
        });
        cards().forEach((c) => {
            c.style.display = '';
        });
        const n = cards().length;
        updateCount(n);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.jb-add-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const raw = btn.getAttribute('data-add');
                if (!raw) return;
                try {
                    const o = JSON.parse(raw);
                    if (typeof addToCart === 'function') addToCart(o);
                } catch (e) {
                    console.warn(e);
                }
            });
        });
    });
})();
