<?php
require_once __DIR__ . '/../includes/config.php';
$slug    = sanitize($_GET['slug'] ?? '');
$product = getProductBySlug($slug);
if (!$product) { header('Location: ' . BASE_URL . '/pages/category.php?c=all'); exit; }
$pageTitle = sanitize($product['name']) . ' — Jolly Beauty';

// Récupère les images du produit depuis la base de données
$images = !empty($product['images']) ? $product['images'] : [];
$mainImg = !empty($images[0]) ? $images[0] : null;

// Produits associés
$related = getProducts($product['category'] ?? null, '', 'default', 4);
$related = array_filter($related, fn($p) => $p['id'] !== $product['id']);
$related = array_slice(array_values($related), 0, 4);

include __DIR__ . '/../includes/header.php';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
?>

<div style="background:var(--rose-pale);padding:16px 6%;font-size:.76rem;color:var(--muted);" class="breadcrumb">
  <a href="<?= $jbBase ?>/index.php">Accueil</a> ›
  <?php $cat = $product['category'] ?? ''; $catHref = $jbBase . '/category.php?c=' . urlencode(in_array($cat, ['bijoux','soins','coffrets','produits'], true) ? $cat : 'all'); ?>
  <a href="<?= htmlspecialchars($catHref) ?>"><?= ucfirst(sanitize($cat)) ?></a> ›
  <?= sanitize($product['name']) ?>
</div>

<section class="product-section">
  <div class="product-layout">

    <!-- GALLERY -->
    <div class="product-gallery">
      <div class="gallery-main">
        <?php if ($mainImg): ?><img src="<?= htmlspecialchars($mainImg) ?>" alt="<?= sanitize($product['name']) ?>" id="gallery-main-img"><?php else: ?><div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:6rem;opacity:.25">🌸</div><?php endif; ?>
      </div>
      <?php if (count($images) > 1): ?>
      <div class="gallery-thumbs">
        <?php foreach(array_slice($images,0,4) as $i=>$img): ?>
        <img src="<?= htmlspecialchars($img) ?>" class="gallery-thumb <?= $i===0?'active':'' ?>" onclick="switchImg(this,'<?= htmlspecialchars($img) ?>')" alt="<?= sanitize($product['name']) ?>">
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
        <button class="btn-wishlist" title="Ajouter aux favoris">♡</button>
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
      <div class="product-card__img-wrap">
        <?php if ($img): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3">🌸</div><?php endif; ?>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>