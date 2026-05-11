<?php
require_once __DIR__ . '/../includes/config.php';
$slug    = sanitize($_GET['slug'] ?? '');
$product = getProductBySlug($slug);

// Si pas de produit en base, essayer les données mock
if (!$product) {
    $mockProducts = [
        'bracelet-soleil' => [
            'id' => 'mock-1',
            'name' => 'Bracelet Soleil',
            'price' => 29.90,
            'description' => 'Un bracelet élégant qui capture la lumière du soleil avec ses délicates chaînes dorées.',
            'category' => 'bijoux',
            'images' => ['/assets/images/bijoux/bijoux-bracelet-1.jpg'],
            'stock' => 10
        ],
        'bague-torsadee' => [
            'id' => 'mock-2',
            'name' => 'Bague Torsadée',
            'price' => 24.90,
            'description' => 'Une bague raffinée avec un design torsadé unique, parfaite pour toutes les occasions.',
            'category' => 'bijoux',
            'images' => ['/assets/images/bijoux/bijoux-bague-1.jpg'],
            'stock' => 8
        ],
        'collier-lumiere' => [
            'id' => 'mock-3',
            'name' => 'Collier Lumière',
            'price' => 33.90,
            'description' => 'Un collier scintillant qui illumine votre silhouette avec sa pierre centrale brillante.',
            'category' => 'bijoux',
            'images' => ['/assets/images/bijoux/bijoux-collier-1.jpg'],
            'stock' => 12
        ],
        'bracelet-perles' => [
            'id' => 'mock-4',
            'name' => 'Bracelet Perles',
            'price' => 27.90,
            'description' => 'Un bracelet délicat orné de perles nacrées pour un look féminin et élégant.',
            'category' => 'bijoux',
            'images' => ['/assets/images/bijoux/bijoux-bracelet-2.jpg'],
            'stock' => 15
        ],
        'bague-douceur' => [
            'id' => 'mock-5',
            'name' => 'Bague Douceur',
            'price' => 26.90,
            'description' => 'Une bague douce et confortable avec un design minimaliste et moderne.',
            'category' => 'bijoux',
            'images' => ['/assets/images/bijoux/bijoux-bague-2.jpg'],
            'stock' => 9
        ],
        'creoles-eclat' => [
            'id' => 'mock-6',
            'name' => 'Créoles Éclat',
            'price' => 28.90,
            'description' => 'Des créoles classiques avec un éclat moderne, parfaites pour un usage quotidien.',
            'category' => 'bijoux',
            'images' => ['/assets/images/bijoux/bracelet-charms-eclat.jpg'],
            'stock' => 11
        ],
        // Soins & Rituels
        'gommage-corps-rose-sucre' => [
            'id' => 'mock-soins-1',
            'name' => 'Gommage Corps Rose & Sucre',
            'price' => 26.90,
            'description' => 'Un gommage doux au sucre et à la rose pour une peau satinée et hydratée.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-corps-1.jpg'],
            'stock' => 15
        ],
        'creme-hydratante-fleur-rose' => [
            'id' => 'mock-soins-2',
            'name' => 'Crème Hydratante Fleur de Rose',
            'price' => 28.90,
            'description' => 'Une crème légère et fondante pour hydrater et illuminer le visage.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/creme-visage.jpg'],
            'stock' => 12
        ],
        'huile-nourrissante-amande' => [
            'id' => 'mock-soins-3',
            'name' => 'Huile Nourrissante Amande Douce',
            'price' => 22.90,
            'description' => 'Une huile sèche multi-usages pour nourrir corps et cheveux.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-corps-2.jpg'],
            'stock' => 18
        ],
        'beurre-corps-karite-vanille' => [
            'id' => 'mock-soins-4',
            'name' => 'Beurre Corps Karité & Vanille',
            'price' => 24.90,
            'description' => 'Un beurre riche et fondant pour hydrater en profondeur.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-visage-1.jpg'],
            'stock' => 10
        ],
        'savon-surgras-fleur-oranger' => [
            'id' => 'mock-soins-5',
            'name' => 'Savon Surgras Fleur d\'Oranger',
            'price' => 8.90,
            'description' => 'Un savon artisanal surgras au parfum délicat de fleur d\'oranger.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-visage-2.jpg'],
            'stock' => 25
        ],
        'eau-florale-rose-damas' => [
            'id' => 'mock-soins-6',
            'name' => 'Eau Florale Rose de Damas',
            'price' => 16.90,
            'description' => 'Une eau florale apaisante pour tonifier et rafraîchir la peau.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-corps-1.jpg'],
            'stock' => 20
        ],
        'masque-visage-argile-rose' => [
            'id' => 'mock-soins-7',
            'name' => 'Masque Visage Argile Rose',
            'price' => 19.90,
            'description' => 'Un masque purifiant à l\'argile rose pour une peau nette et éclatante.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-visage-1.jpg'],
            'stock' => 14
        ],
        'baume-levres-nourrissant' => [
            'id' => 'mock-soins-8',
            'name' => 'Baume à Lèvres Nourrissant',
            'price' => 7.90,
            'description' => 'Un baume protecteur pour des lèvres douces et hydratées.',
            'category' => 'soins',
            'images' => ['/assets/images/soins/soin-visage-2.jpg'],
            'stock' => 30
        ],
        // Coffrets
        'coffret-decouverte' => [
            'id' => 'mock-coffret-1',
            'name' => 'Coffret Découverte',
            'price' => 49.90,
            'description' => 'Un coffret idéal pour découvrir l\'univers Jolly Beauty.',
            'category' => 'coffrets',
            'images' => ['/assets/images/coffrets/coffret-luxe.jpg'],
            'stock' => 8
        ],
        'coffret-prestige' => [
            'id' => 'mock-coffret-2',
            'name' => 'Coffret Prestige',
            'price' => 89.90,
            'description' => 'Notre coffret signature avec nos meilleures créations.',
            'category' => 'coffrets',
            'images' => ['/assets/images/coffrets/coffret-prestige-1.jpg'],
            'stock' => 6
        ],
        'coffret-bijoux' => [
            'id' => 'mock-coffret-3',
            'name' => 'Coffret Bijoux',
            'price' => 69.90,
            'description' => 'Un coffret élégant avec une sélection de bijoux raffinés.',
            'category' => 'coffrets',
            'images' => ['/assets/images/coffrets/coffret-prestige-2.jpg'],
            'stock' => 10
        ],
        'coffret-rituel' => [
            'id' => 'mock-coffret-4',
            'name' => 'Coffret Rituel',
            'price' => 59.90,
            'description' => 'Un coffret bien-être pour un moment de détente absolu.',
            'category' => 'coffrets',
            'images' => ['/assets/images/coffrets/coffret-luxe.jpg'],
            'stock' => 12
        ],
        'coffret-luxe' => [
            'id' => 'mock-coffret-5',
            'name' => 'Coffret Luxe',
            'price' => 129.90,
            'description' => 'Notre coffret d\'exception avec une sélection premium.',
            'category' => 'coffrets',
            'images' => ['/assets/images/coffrets/coffret-prestige-1.jpg'],
            'stock' => 5
        ],
        'coffret-mini' => [
            'id' => 'mock-coffret-6',
            'name' => 'Coffret Mini',
            'price' => 39.90,
            'description' => 'Un petit coffret parfait pour offrir un moment de plaisir.',
            'category' => 'coffrets',
            'images' => ['/assets/images/coffrets/coffret-prestige-2.jpg'],
            'stock' => 15
        ]
    ];
    
    $product = $mockProducts[$slug] ?? null;
}

if (!$product) { header('Location: ' . BASE_URL . '/bijoux.php'); exit; }
$pageTitle = sanitize($product['name']) . ' — Jolly Beauty';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

// Récupère les images du produit depuis la base de données ou mock data
$images = !empty($product['images']) ? $product['images'] : [];
$mainImg = !empty($images[0]) ? $jbBase . $images[0] : null;

// Produits associés améliorés
$related = [];
$category = $product['category'] ?? null;

// 1. D'abord essayer de trouver des produits de la même catégorie
if ($category) {
    $sameCategory = getProducts($category, '', 'default', 8);
    $sameCategory = array_filter($sameCategory, fn($p) => $p['id'] !== $product['id']);
    
    // Trier par pertinence : produits en vedette d'abord
    usort($sameCategory, function($a, $b) {
        if ($a['featured'] && !$b['featured']) return -1;
        if (!$a['featured'] && $b['featured']) return 1;
        return 0;
    });
    
    $related = array_slice($sameCategory, 0, 4);
}

// 2. Si pas assez de produits, ajouter des produits similaires (même gamme de prix)
if (count($related) < 4) {
    $allProducts = getProducts(null, '', 'default', 20);
    $allProducts = array_filter($allProducts, fn($p) => $p['id'] !== $product['id'] && !in_array($p, $related));
    
    // Filtrer par gamme de prix similaire (±30%)
    $priceRange = $product['price'] * 0.3;
    $similarPrice = array_filter($allProducts, function($p) use ($product, $priceRange) {
        return abs($p['price'] - $product['price']) <= $priceRange;
    });
    
    // Ajouter les meilleurs produits similaires
    $related = array_merge($related, array_slice($similarPrice, 0, 4 - count($related)));
}

// 3. Compléter avec des produits populaires si nécessaire
if (count($related) < 4) {
    $remaining = array_filter(getProducts(null, '', 'default', 10), 
        fn($p) => $p['id'] !== $product['id'] && !in_array($p, $related));
    $related = array_merge($related, array_slice($remaining, 0, 4 - count($related)));
}

// Limiter à 4 produits au final
$related = array_slice($related, 0, 4);

include __DIR__ . '/../includes/header.php';
?>

<div style="background:var(--rose-pale);padding:16px 6%;font-size:.76rem;color:var(--muted);" class="breadcrumb">
  <a href="<?= $jbBase ?>/index.php">Accueil</a> ›
  <?php $cat = $product['category'] ?? ''; $catHref = $jbBase . '/' . (in_array($cat, ['bijoux','soins','coffrets','produits'], true) ? $cat . '.php' : 'bijoux.php'); ?>
  <a href="<?= htmlspecialchars($catHref) ?>"><?= ucfirst(sanitize($cat)) ?></a> ›
  <?= sanitize($product['name']) ?>
</div>

<section class="product-section">
  <div class="product-layout">

    <!-- GALLERY AMÉLIORÉE -->
    <div class="product-gallery">
      <div class="gallery-main" id="gallery-main">
        <?php if ($mainImg): ?>
          <img src="<?= htmlspecialchars($mainImg) ?>" 
               alt="<?= sanitize($product['name']) ?>" 
               id="gallery-main-img"
               class="gallery-zoomable"
               onerror="console.error('Image failed to load:', this.src); this.style.display='none'; this.parentElement.innerHTML='<div style=\'width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3\'>🌸</div>';"
               onclick="openZoom(this.src)">
        <?php else: ?>
          <div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:6rem;opacity:.25">🌸</div>
        <?php endif; ?>
        <div class="gallery-zoom-hint">🔍 Cliquez pour zoomer</div>
      </div>
      <?php if (count($images) > 1): ?>
      <div class="gallery-thumbs">
        <?php foreach(array_slice($images,0,6) as $i=>$img):
          $thumbUrl = $jbBase . $img;
        ?>
        <div class="gallery-thumb-wrap <?= $i===0?'active':'' ?>">
          <img src="<?= htmlspecialchars($thumbUrl) ?>"
               class="gallery-thumb"
               onclick="switchImg(this,'<?= htmlspecialchars($thumbUrl) ?>')"
               alt="<?= sanitize($product['name']) ?>"
               loading="lazy">
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- INFO -->
    <div class="product-info">
      <div class="product-cat"><?= ucfirst(sanitize($product['category'] ?? '')) ?></div>
      <h1 class="product-name"><?= sanitize($product['name']) ?></h1>
      <div class="product-price">
        <?php if (!empty($product['old_price'])): ?><del><?= formatPrice($product['old_price']) ?></del><?php endif; ?>
        <?= formatPrice($product['price']) ?>
      </div>
      <p class="product-accroche"><?= sanitize($product['description'] ?? 'Un bijou délicat qui accompagne chaque moment de votre journée.') ?></p>

      <div class="product-highlights">
        <div class="product-highlight">Design délicat et féminin</div>
        <div class="product-highlight">Léger et confortable</div>
        <div class="product-highlight">Idéal pour un cadeau</div>
        <div class="product-highlight">Paiement sécurisé · Expédition rapide</div>
      </div>

      <hr class="product-divider">

      <?php if (!empty($product['sizes'])): ?>
      <div>
        <div class="size-label">Taille</div>
        <div class="size-btns">
          <?php foreach($product['sizes'] as $s): ?>
          <button class="size-btn" onclick="selectSize(this)"><?= trim(sanitize($s)) ?></button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php else: ?>
      <div>
        <div class="size-label">Taille — Guide</div>
        <div class="size-btns">
          <button class="size-btn" onclick="selectSize(this)">S — 16cm</button>
          <button class="size-btn active" onclick="selectSize(this)">M — 18cm</button>
          <button class="size-btn" onclick="selectSize(this)">L — 20cm</button>
        </div>
      </div>
      <?php endif; ?>

      <div class="qty-row">
        <div class="size-label" style="margin:0">Quantité</div>
        <div class="qty-box">
          <button class="qty-btn" onclick="changeQtyInput(-1)">−</button>
          <input type="number" id="qty-input" value="1" min="1" max="<?= (int)($product['stock'] ?? 99) ?>" class="qty-val"
            data-id="<?= htmlspecialchars($product['id']) ?>"
            data-name="<?= htmlspecialchars($product['name']) ?>"
            data-price="<?= floatval($product['price']) ?>"
            data-image="<?= htmlspecialchars($mainImg ?? '') ?>"
            data-stock="<?= (int)($product['stock'] ?? 99) ?>"
            style="border:none;text-align:center;font-family:var(--font-sans);background:transparent;width:32px;">
          <button class="qty-btn" onclick="changeQtyInput(1)">+</button>
        </div>
      </div>

      <div class="product-cta-row">
        <button class="btn btn--rose" style="flex:1" onclick="addProductToCart()">Ajouter au panier</button>
        <button class="btn-wishlist" 
                data-product-id="<?= htmlspecialchars($product['id']) ?>"
                data-product-name="<?= htmlspecialchars($product['name']) ?>"
                onclick="toggleFavorite('<?= htmlspecialchars($product['id']) ?>', this)"
                title="Ajouter aux favoris"
                style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;">♡</button>
        <button class="share-btn" onclick="shareProduct()" title="Partager ce produit">
          📤 Partager
        </button>
      </div>

      <div class="trust-row">
        <span class="trust-item">🔒 Paiement sécurisé</span>
        <span class="trust-item">🚚 Livraison 48h</span>
        <span class="trust-item">↩ Retours 30 jours</span>
      </div>

      <div class="accordion">
        <div class="accordion-item">
          <button class="accordion-btn open" onclick="toggleAcc(this)">Description <span class="accordion-icon">+</span></button>
          <div class="accordion-body open"><p><?= nl2br(sanitize($product['description'] ?? "Un bijou pensé pour sublimer votre féminité avec simplicité et élégance. Chaque pièce est conçue comme un symbole précieux qui accompagne votre quotidien.")) ?></p></div>
        </div>
        <div class="accordion-item">
          <button class="accordion-btn" onclick="toggleAcc(this)">Matériaux &amp; entretien <span class="accordion-icon">+</span></button>
          <div class="accordion-body"><ul><li>Acier inoxydable plaqué or 18k</li><li>Résistant à l'eau</li><li>Hypoallergénique</li><li>Fermoir sécurisé</li></ul></div>
        </div>
        <div class="accordion-item">
          <button class="accordion-btn" onclick="toggleAcc(this)">Livraison &amp; retours <span class="accordion-icon">+</span></button>
          <div class="accordion-body"><p>Expédition sous 48h · Livraison suivie · Livraison gratuite dès 60€ · Retours gratuits sous 30 jours.</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- RELATED -->
<?php if (!empty($related)): ?>
<section class="bestsellers" style="background:var(--rose-pale);">
  <div class="section-head">
    <div><h2 class="section-title">Vous aimerez <em>aussi</em></h2></div>
  </div>
  <div class="products-grid">
    <?php foreach($related as $i => $p):
      $img = !empty($p['images'][0]) ? $p['images'][0] : null;
    ?>
    <div class="product-card">
      <div class="product-card__img-wrap" style="position: relative;">
        <?php if ($img): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3">🌸</div><?php endif; ?>
        <button class="favorite-btn" 
                data-product-id="<?= htmlspecialchars($p['id']) ?>"
                data-product-name="<?= sanitize($p['name']) ?>"
                onclick="toggleFavorite('<?= htmlspecialchars($p['id']) ?>', this)"
                title="Ajouter aux favoris"
                style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;">♡</button>
      </div>
      <div class="product-card__body">
        <div class="product-card__cat"><?= sanitize($p['category'] ?? '') ?></div>
        <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($p['slug']) ?>"><div class="product-card__name"><?= sanitize($p['name']) ?></div></a>
        <div class="product-card__foot">
          <div class="product-card__price"><?= formatPrice($p['price']) ?></div>
          <button class="product-card__add" onclick="addToCart({id:'<?= $p['id'] ?>',name:'<?= addslashes(sanitize($p['name'])) ?>',price:<?= floatval($p['price']) ?>,image:'<?= addslashes($img ?? '') ?>',category:'<?= addslashes(sanitize($p['category'] ?? '')) ?>'})">+</button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Modal de zoom -->
<div id="zoom-modal" class="zoom-modal" onclick="closeZoom()">
    <div class="zoom-content">
        <img id="zoom-img" src="" alt="<?= sanitize($product['name']) ?>">
        <button class="zoom-close" onclick="closeZoom()">✕</button>
    </div>
</div>

<!-- Script amélioré -->
<script>
function addProductToCart() {
    const qtyInput = document.getElementById('qty-input');
    const id = qtyInput.dataset.id;
    const name = qtyInput.dataset.name;
    const price = parseFloat(qtyInput.dataset.price);
    const image = qtyInput.dataset.image;
    const qty = parseInt(qtyInput.value) || 1;
    
    if (typeof addToCart === 'function') {
        for (let i = 0; i < qty; i++) {
            addToCart({
                id: id,
                name: name,
                price: price,
                image: image,
                category: '<?= addslashes($product['category'] ?? '') ?>'
            });
        }
        showToast('Ajouté au panier !');
    }
}

function changeQtyInput(delta) {
    const input = document.getElementById('qty-input');
    const current = parseInt(input.value) || 1;
    const max = parseInt(input.dataset.max) || 99;
    const newVal = Math.max(1, Math.min(max, current + delta));
    input.value = newVal;
}

function selectSize(btn) {
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function switchImg(thumb, src) {
    const mainImg = document.getElementById('gallery-main-img');
    mainImg.src = src;
    document.querySelectorAll('.gallery-thumb-wrap').forEach(t => t.classList.remove('active'));
    thumb.parentElement.classList.add('active');
}

function toggleAcc(btn) {
    const body = btn.nextElementSibling;
    const isOpen = body.classList.contains('open');
    btn.classList.toggle('open');
    body.classList.toggle('open');
    btn.querySelector('.accordion-icon').textContent = isOpen ? '+' : '−';
}

// Fonctionnalités de zoom
function openZoom(src) {
    const modal = document.getElementById('zoom-modal');
    const zoomImg = document.getElementById('zoom-img');
    zoomImg.src = src;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeZoom() {
    const modal = document.getElementById('zoom-modal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Partage du produit
function shareProduct() {
    const url = window.location.href;
    const title = '<?= sanitize($product['name']) ?> — Jolly Beauty';
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: 'Découvrez ce magnifique bijou chez Jolly Beauty !',
            url: url
        });
    } else {
        // Fallback : copier dans le presse-papiers
        navigator.clipboard.writeText(url).then(() => {
            showToast('Lien copié dans le presse-papiers !');
        });
    }
}

// Navigation au clavier pour la galerie
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('zoom-modal');
    if (modal.style.display === 'flex') {
        if (e.key === 'Escape') closeZoom();
        return;
    }
    
    const thumbs = document.querySelectorAll('.gallery-thumb');
    const activeThumb = document.querySelector('.gallery-thumb-wrap.active .gallery-thumb');
    if (!activeThumb) return;
    
    let currentIndex = Array.from(thumbs).indexOf(activeThumb);
    
    if (e.key === 'ArrowLeft' && currentIndex > 0) {
        thumbs[currentIndex - 1].click();
    } else if (e.key === 'ArrowRight' && currentIndex < thumbs.length - 1) {
        thumbs[currentIndex + 1].click();
    }
});

</script>

<style>
/* Améliorations de la galerie */
.product-gallery {
    position: relative;
}

.gallery-main {
    position: relative;
    cursor: zoom-in;
    overflow: hidden;
    border-radius: 12px;
}

.gallery-main img {
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 1;
}

.gallery-main img:hover {
    transform: scale(1.02);
}

.gallery-zoom-hint {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-main:hover .gallery-zoom-hint {
    opacity: 1;
}

.gallery-thumbs {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
    margin-top: 15px;
}

.gallery-thumb-wrap {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.gallery-thumb-wrap:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.gallery-thumb-wrap.active {
    border-color: var(--rose-deep);
}

.gallery-thumb {
    width: 100%;
    height: 80px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-thumb:hover {
    transform: scale(1.05);
}

/* Modal de zoom */
.zoom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    cursor: zoom-out;
}

.zoom-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
}

.zoom-content img {
    width: 100%;
    height: auto;
    max-height: 90vh;
    object-fit: contain;
}

.zoom-close {
    position: absolute;
    top: -40px;
    right: 0;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.3s ease;
}

.zoom-close:hover {
    background: rgba(255,255,255,0.3);
}

/* Bouton de partage */
.share-btn {
    background: var(--rose-deep);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    margin-left: 10px;
}

.share-btn:hover {
    background: var(--rose-dark);
    transform: translateY(-2px);
}

/* Responsive design */
@media (max-width: 768px) {
    .product-layout {
        flex-direction: column;
        gap: 30px;
    }
    
    .gallery-thumbs {
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        gap: 8px;
    }
    
    .gallery-thumb {
        height: 60px;
    }
    
    .zoom-content {
        max-width: 95%;
        max-height: 95%;
    }
    
    .gallery-zoom-hint {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}

@media (max-width: 480px) {
    .gallery-thumbs {
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
    }
    
    .gallery-thumb {
        height: 50px;
    }
    
    .zoom-close {
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.5);
    }
    
    .share-btn {
        margin-left: 0;
        margin-top: 10px;
        width: 100%;
    }
}

/* Amélioration des transitions */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

/* Animation de chargement */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.product-section > * {
    animation: fadeIn 0.6s ease forwards;
}

.product-section > *:nth-child(2) { animation-delay: 0.1s; }
.product-section > *:nth-child(3) { animation-delay: 0.2s; }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>