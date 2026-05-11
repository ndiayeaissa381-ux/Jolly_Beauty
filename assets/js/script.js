document.addEventListener('DOMContentLoaded', () => {
    initCart();
    initSearchOverlay();
    initSmoothScroll();
    initRevealOnScroll();
});

/** Révèle les blocs [data-reveal] (CSS: opacity 0 jusqu'à .revealed) — utilisé hors index.php */
function initRevealOnScroll() {
    const nodes = document.querySelectorAll('[data-reveal]');
    if (!nodes.length) return;
    if (!('IntersectionObserver' in window)) {
        nodes.forEach(el => el.classList.add('revealed'));
        return;
    }
    const io = new IntersectionObserver(
        entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed');
                    io.unobserve(e.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.02 }
    );
    nodes.forEach(el => io.observe(el));
}

function jbBase() {
    return typeof window.JB_BASE === 'string' ? window.JB_BASE : '';
}

// ── Panier (localStorage + panneau #cart-overlay du header) ───────────────
let cart = [];

function initCart() {
    try {
        const stored = localStorage.getItem('jolly_cart');
        if (stored) {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed)) {
                cart = parsed.map(item => ({
                    ...item,
                    id: String(item.id),
                    price: parseFloat(item.price) || 0,
                    quantity: parseInt(item.quantity) || 1
                }));
            } else {
                cart = [];
            }
        } else {
            cart = [];
        }
    } catch (e) {
        console.error('Error loading cart:', e);
        cart = [];
    }
    updateCartUI();
    renderCartOverlay();
}

function saveCart() {
    localStorage.setItem('jolly_cart', JSON.stringify(cart));
    updateCartUI();
    renderCartOverlay();
    syncCartWithServer();
}

function escHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/"/g, '&quot;');
}

function addToCart(id, name, price, image, category = '') {
    if (id && typeof id === 'object') {
        const obj = id;
        id = obj.id;
        name = obj.name;
        price = parseFloat(obj.price);
        image = obj.image;
        category = obj.category || '';
    }
    price = parseFloat(price);
    const idStr = String(id);
    const existing = cart.find(item => String(item.id) === idStr);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id: idStr, name, price, image: image || '', category: category || '', quantity: 1 });
    }
    saveCart();
    showToast(`${name} ajouté au panier`);
    openCart();
}

function removeFromCart(id) {
    const idStr = String(id);
    cart = cart.filter(item => String(item.id) !== idStr);
    saveCart();
}

function updateQuantity(id, delta) {
    const item = cart.find(i => String(i.id) === String(id));
    if (!item) return;
    item.quantity = Math.max(1, item.quantity + delta);
    saveCart();
}

function clearCart() {
    cart = [];
    localStorage.removeItem('jolly_cart');
    updateCartUI();
    renderCartOverlay();
    console.log('Cart cleared');
}

function cartImageSrc(item) {
    const base = jbBase();
    const img = item.image;
    if (typeof img === 'string' && (img.startsWith('http') || img.startsWith('/'))) {
        return img;
    }
    if (typeof img === 'string' && img.length) {
        return `${base}/assets/images/${item.category}/${img}`;
    }
    return '';
}

function formatPriceEUR(price) {
    const numPrice = parseFloat(price) || 0;
    return numPrice.toFixed(2).replace('.', ',') + ' €';
}

function getCartSubtotal() {
    return cart.reduce((sum, i) => {
        const price = parseFloat(i.price) || 0;
        const qty = parseInt(i.quantity) || 1;
        const itemTotal = price * qty;
        return sum + itemTotal;
    }, 0);
}

function updateCartUI() {
    const count = cart.reduce((sum, i) => sum + (parseInt(i.quantity) || 1), 0);
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count > 0 ? String(count) : '0';
    });
}

function renderCartOverlay() {
    const container = document.getElementById('cart-items-container');
    const totalEl = document.getElementById('cart-total-price');
    const fillEl = document.getElementById('shipping-fill');
    const labelEl = document.getElementById('shipping-label');
    if (!container) return;

    const subtotal = getCartSubtotal();
    const freeFrom = 60;
    const remaining = Math.max(0, freeFrom - subtotal);
    const pct = Math.min(100, (subtotal / freeFrom) * 100);

    if (fillEl) fillEl.style.width = pct + '%';
    if (labelEl) {
        if (subtotal >= freeFrom) {
            labelEl.innerHTML = '<span style="color:#2e7d32;">Livraison gratuite débloquée !</span>';
        } else {
            labelEl.innerHTML = 'Plus que <strong>' + formatPriceEUR(remaining) + '</strong> pour la livraison gratuite !';
        }
    }

    if (cart.length === 0) {
        container.innerHTML = '<div class="empty-cart" style="padding:2rem 1rem;text-align:center;color:var(--muted);">Votre panier est vide</div>';
        if (totalEl) totalEl.textContent = '0,00 €';
        return;
    }

    let html = '';
    cart.forEach(item => {
        const imgSrc = cartImageSrc(item);
        const itemId = String(item.id);
        const itemPrice = parseFloat(item.price) || 0;
        html += `
            <div class="cart-item" data-item-id="${escHtml(itemId)}" style="display:flex;gap:12px;padding:14px 0;border-bottom:1px solid var(--blush);">
                ${imgSrc ? `<img src="${escHtml(imgSrc)}" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:10px;flex-shrink:0;">` : '<div style="width:72px;height:72px;background:var(--blush);border-radius:10px;"></div>'}
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:.88rem;">${escHtml(item.name)}</div>
                    <div style="font-size:.82rem;color:var(--muted);margin-top:4px;">${formatPriceEUR(itemPrice)}</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                        <button type="button" class="cart-qty-minus" style="width:28px;height:28px;border-radius:50%;border:1px solid var(--blush);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;">−</button>
                        <span style="min-width:24px;text-align:center;font-weight:500;">${item.quantity}</span>
                        <button type="button" class="cart-qty-plus" style="width:28px;height:28px;border-radius:50%;border:1px solid var(--blush);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;">+</button>
                        <button type="button" class="cart-remove" style="margin-left:auto;color:var(--rd);background:none;border:none;cursor:pointer;font-size:1.2rem;width:28px;height:28px;display:flex;align-items:center;justify-content:center;" title="Retirer du panier">×</button>
                    </div>
                </div>
            </div>`;
    });
    container.innerHTML = html;

    // Add event listeners for cart buttons
    container.querySelectorAll('.cart-qty-minus').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const itemId = e.target.closest('.cart-item').dataset.itemId;
            updateQuantity(itemId, -1);
        });
    });
    container.querySelectorAll('.cart-qty-plus').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const itemId = e.target.closest('.cart-item').dataset.itemId;
            updateQuantity(itemId, 1);
        });
    });
    container.querySelectorAll('.cart-remove').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const itemId = e.target.closest('.cart-item').dataset.itemId;
            removeFromCart(itemId);
        });
    });

    const shipping = subtotal >= freeFrom ? 0 : 5.9;
    const total = subtotal + shipping;
    if (totalEl) totalEl.textContent = formatPriceEUR(total);
}

function openCart() {
    document.getElementById('cart-bg')?.classList.add('open');
    document.getElementById('cart-overlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
    renderCartOverlay();
}

function closeCart() {
    document.getElementById('cart-bg')?.classList.remove('open');
    document.getElementById('cart-overlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

// Expose functions globally
window.openCart = openCart;
window.closeCart = closeCart;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateQuantity = updateQuantity;
window.clearCart = clearCart;
window.showToast = showToast;
window.toggleFavorite = toggleFavorite;

// ── Favoris ─────────────────────────────────────────────────────────
let favorites = [];

function initFavorites() {
    loadFavorites();
    updateFavoriteButtons();
}

function loadFavorites() {
    if (!isLoggedIn()) return;
    
    fetch(jbBase() + '/api/favorites.php?action=list', {
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            favorites = data.favorites || [];
            updateFavoriteButtons();
        }
    })
    .catch(() => {});
}

function updateFavoriteButtons() {
    document.querySelectorAll('.btn-wishlist, .favorite-btn').forEach(btn => {
        const productId = btn.getAttribute('data-product-id');
        if (productId) {
            const isFavorite = favorites.some(fav => String(fav.id) === String(productId));
            btn.classList.toggle('favorited', isFavorite);
            btn.textContent = isFavorite ? '♥' : '♡';
        }
    });
}

function toggleFavorite(productId, button) {
    if (!isLoggedIn()) {
        showToast('Connectez-vous pour ajouter aux favoris');
        window.location.href = jbBase() + '/login.php';
        return;
    }
    
    const isFavorite = favorites.some(fav => String(fav.id) === String(productId));
    const action = isFavorite ? 'remove' : 'add';
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(jbBase() + `/api/favorites.php?action=${action}`, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (isFavorite) {
                favorites = favorites.filter(fav => String(fav.id) !== String(productId));
                showToast('Retiré des favoris');
            } else {
                // Ajouter le produit aux favoris (besoin de plus d'infos)
                const productName = button.getAttribute('data-product-name') || 'Produit';
                favorites.push({ id: productId, name: productName });
                showToast('Ajouté aux favoris');
            }
            updateFavoriteButtons();
        } else {
            showToast(data.error || 'Erreur');
        }
    })
    .catch(() => {
        showToast('Erreur réseau');
    });
}

function isLoggedIn() {
    // Cette fonction sera définie globalement via PHP
    return typeof window.IS_LOGGED_IN !== 'undefined' && window.IS_LOGGED_IN;
}

// Initialiser les favoris au chargement
document.addEventListener('DOMContentLoaded', () => {
    initFavorites();
});

function syncCartWithServer() {
    fetch(jbBase() + '/api/cart-sync.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ cart })
    }).catch(() => {});
}

// ── Recherche (overlay #search-overlay) ──────────────────────────────────
function initSearchOverlay() {
    // Rien : boutons utilisent onclick dans le header
}

function toggleSearch() {
    const el = document.getElementById('search-overlay');
    if (!el) return;
    el.classList.add('open');
    document.getElementById('search-input')?.focus();
}

function closeSearch() {
    document.getElementById('search-overlay')?.classList.remove('open');
}

window.toggleSearch = toggleSearch;
window.closeSearch = closeSearch;

// ── Navigation Mobile ─────────────────────────────────────────────────────
function openMobileNav() {
    document.getElementById('mobile-nav')?.classList.add('open');
    // Créer l'overlay s'il n'existe pas
    let overlay = document.getElementById('mobile-nav-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'mobile-nav-overlay';
        overlay.style.cssText = 'position:fixed;inset:0;z-index:199;background:rgba(0,0,0,0.4);opacity:0;transition:opacity .3s;pointer-events:none;';
        overlay.onclick = closeMobileNav;
        document.body.appendChild(overlay);
    }
    overlay.style.pointerEvents = 'auto';
    requestAnimationFrame(() => overlay.style.opacity = '1');
    document.body.style.overflow = 'hidden';
}

function closeMobileNav() {
    document.getElementById('mobile-nav')?.classList.remove('open');
    const overlay = document.getElementById('mobile-nav-overlay');
    if (overlay) {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        setTimeout(() => overlay.remove(), 300);
    }
    document.body.style.overflow = '';
}

window.openMobileNav = openMobileNav;
window.closeMobileNav = closeMobileNav;

// ── Toast ────────────────────────────────────────────────────────────────
function showToast(message) {
    const stack = document.getElementById('toast-stack');
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    (stack || document.body).appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href.length < 2) return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

const style = document.createElement('style');
style.textContent = `
.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--text-dark, #2a2524);
    color: white;
    padding: 12px 24px;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: jbToast 3s forwards;
}
@keyframes jbToast {
    0% { opacity: 0; transform: translate(-50%, 20px); }
    12% { opacity: 1; transform: translate(-50%, 0); }
    88% { opacity: 1; }
    100% { opacity: 0; transform: translate(-50%, -12px); }
}
.search-overlay.open {
    opacity: 1 !important;
    pointer-events: auto !important;
    visibility: visible !important;
}
`;
document.head.appendChild(style);
