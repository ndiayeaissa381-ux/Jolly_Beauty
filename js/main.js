/* ============================================================
   JOLLY BEAUTY — main.js
   ============================================================ */

// ── CART ─────────────────────────────────────────────────────
let cart = JSON.parse(localStorage.getItem('jb_cart') || '[]');

function saveCart() {
  localStorage.setItem('jb_cart', JSON.stringify(cart));
  renderCart();
}

function addToCart(item) {
  const existing = cart.find(i => i.id === item.id);
  if (existing) {
    existing.qty = (existing.qty || 1) + 1;
  } else {
    cart.push({ ...item, qty: 1 });
  }
  saveCart();
  openCart();
  showToast('✓ ' + item.name + ' ajouté au panier');
}

function removeFromCart(id) {
  cart = cart.filter(i => i.id !== id);
  saveCart();
}

function updateQty(id, delta) {
  const item = cart.find(i => i.id === id);
  if (!item) return;
  item.qty = Math.max(1, (item.qty || 1) + delta);
  saveCart();
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatEuro(val) {
  return val.toFixed(2).replace('.', ',') + ' €';
}

function renderCart() {
  const container = document.getElementById('cart-items-container');
  const countEl   = document.getElementById('cart-count');
  const totalEl   = document.getElementById('cart-total-price');
  const fillEl    = document.getElementById('shipping-fill');
  const remEl     = document.getElementById('shipping-remaining');
  const labelEl   = document.getElementById('shipping-label');

  if (!container) return;

  const totalQty   = cart.reduce((s, i) => s + (i.qty || 1), 0);
  const totalPrice = cart.reduce((s, i) => s + (i.price || 0) * (i.qty || 1), 0);

  if (countEl) countEl.textContent = totalQty;
  if (totalEl) totalEl.textContent = formatEuro(totalPrice);

  // Shipping bar
  const FREE_THRESHOLD = 60;
  if (fillEl) fillEl.style.width = Math.min(100, (totalPrice / FREE_THRESHOLD) * 100) + '%';
  if (remEl && labelEl) {
    const rem = FREE_THRESHOLD - totalPrice;
    if (rem <= 0) {
      labelEl.innerHTML = '🎁 <strong>Livraison gratuite</strong> pour cette commande !';
    } else {
      labelEl.innerHTML = '🚚 Plus que <strong>' + formatEuro(rem) + '</strong> pour la livraison gratuite !';
    }
  }

  if (cart.length === 0) {
    container.innerHTML = `
      <div class="cart-empty">
        <div class="cart-empty-icon">🛍️</div>
        <p>Votre panier est vide</p>
        <button onclick="closeCart();window.location='/Jolly_Beauty/products.php'" class="btn btn--rose btn--sm">Découvrez nos collections</button>
      </div>`;
    return;
  }

  container.innerHTML = cart.map(item => `
    <div class="cart-item">
      ${item.image ? `<img src="${escHtml(item.image)}" class="cart-item-img" alt="${escHtml(item.name)}">` : '<div class="cart-item-img" style="background:var(--blush);display:grid;place-items:center;font-size:1.5rem">🌸</div>'}
      <div class="cart-item-info">
        <div class="cart-item-name">${escHtml(item.name)}</div>
        <div class="cart-item-price">${formatEuro((item.price || 0) * (item.qty || 1))}</div>
        <div class="cart-item-qty">
          <button class="cart-qty-btn" onclick="updateQty('${escHtml(item.id)}',-1)">−</button>
          <span class="cart-qty-val">${item.qty || 1}</span>
          <button class="cart-qty-btn" onclick="updateQty('${escHtml(item.id)}',1)">+</button>
        </div>
      </div>
      <button class="cart-item-remove" onclick="removeFromCart('${escHtml(item.id)}')" title="Supprimer">✕</button>
    </div>`).join('');
}

function openCart() {
  document.getElementById('cart-overlay')?.classList.add('open');
  document.getElementById('cart-bg')?.classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeCart() {
  document.getElementById('cart-overlay')?.classList.remove('open');
  document.getElementById('cart-bg')?.classList.remove('open');
  document.body.style.overflow = '';
}

// ── SEARCH ───────────────────────────────────────────────────
function toggleSearch() {
  const o = document.getElementById('search-overlay');
  if (!o) return;
  const isOpen = o.classList.contains('open');
  if (isOpen) { closeSearch(); } else { o.classList.add('open'); document.getElementById('search-input')?.focus(); document.body.style.overflow = 'hidden'; }
}
function closeSearch() {
  document.getElementById('search-overlay')?.classList.remove('open');
  document.body.style.overflow = '';
}

// ── MOBILE NAV ───────────────────────────────────────────────
function openMobileNav()  { document.getElementById('mobile-nav')?.classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeMobileNav() { document.getElementById('mobile-nav')?.classList.remove('open'); document.body.style.overflow = ''; }

// ── TOAST ────────────────────────────────────────────────────
function showToast(msg) {
  const stack = document.getElementById('toast-stack');
  if (!stack) return;
  const t = document.createElement('div');
  t.className = 'toast';
  t.textContent = msg;
  stack.appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

// ── CURSOR ───────────────────────────────────────────────────
const cursorDot  = document.getElementById('cursor-dot');
const cursorRing = document.getElementById('cursor-ring');
let ringX = 0, ringY = 0, dotX = 0, dotY = 0;
if (cursorDot && cursorRing && window.matchMedia('(hover:hover)').matches) {
  document.addEventListener('mousemove', e => { dotX = e.clientX; dotY = e.clientY; });
  (function animCursor() {
    ringX += (dotX - ringX) * .12;
    ringY += (dotY - ringY) * .12;
    cursorDot.style.transform  = `translate(${dotX}px,${dotY}px) translate(-50%,-50%)`;
    cursorRing.style.transform = `translate(${ringX}px,${ringY}px) translate(-50%,-50%)`;
    requestAnimationFrame(animCursor);
  })();
}

// ── NAV SCROLL ───────────────────────────────────────────────
window.addEventListener('scroll', () => {
  document.getElementById('main-nav')?.classList.toggle('scrolled', window.scrollY > 40);
  document.getElementById('back-top')?.classList.toggle('visible', window.scrollY > 300);
});

// ── KEYBOARD ─────────────────────────────────────────────────
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeSearch(); closeCart(); closeMobileNav(); }
});

// ── ACCORDION ────────────────────────────────────────────────
function toggleAcc(btn) {
  const body = btn.nextElementSibling;
  const isOpen = body.classList.contains('open');
  document.querySelectorAll('.accordion-body.open').forEach(b => { b.classList.remove('open'); b.previousElementSibling.classList.remove('open'); });
  if (!isOpen) { body.classList.add('open'); btn.classList.add('open'); }
}

// ── GALLERY ──────────────────────────────────────────────────
function switchImg(thumb, src) {
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
  const main = document.querySelector('.gallery-main img');
  if (main) main.src = src;
}

// ── SIZE SELECT ──────────────────────────────────────────────
function selectSize(btn) {
  document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

// ── QTY ──────────────────────────────────────────────────────
function changeQtyInput(delta) {
  const input = document.getElementById('qty-input');
  if (!input) return;
  input.value = Math.max(1, Math.min(parseInt(input.dataset.stock || 99), parseInt(input.value || 1) + delta));
}

// ── ADD TO CART FROM PRODUCT PAGE ────────────────────────────
function addProductToCart() {
  const qty  = parseInt(document.getElementById('qty-input')?.value || 1);
  const item = {
    id:    document.getElementById('qty-input')?.dataset.id || 'p',
    name:  document.getElementById('qty-input')?.dataset.name || 'Produit',
    price: parseFloat(document.getElementById('qty-input')?.dataset.price || 0),
    image: document.getElementById('qty-input')?.dataset.image || '',
  };
  for (let i = 0; i < qty; i++) addToCart(item);
}

// ── INIT ─────────────────────────────────────────────────────
renderCart();