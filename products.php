<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Boutique — Jolly Beauty';

$cat   = sanitize($_GET['category'] ?? 'all');
$q     = sanitize($_GET['q'] ?? '');
$sort  = sanitize($_GET['sort'] ?? 'default');
$products = getProducts($cat === 'all' ? null : $cat, $q, $sort);

$catLabels = ['all'=>'Tout', 'bijoux'=>'Bijoux', 'soins'=>'Soins & Rituels', 'coffrets'=>'Coffrets'];
include __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="breadcrumb"><a href="/Jolly_Beauty/index.php">Accueil</a> → <?= $catLabels[$cat] ?? 'Boutique' ?></div>
  <h1><?= $q ? 'Résultats pour "'.htmlspecialchars($q).'"' : ($catLabels[$cat] ?? 'Boutique') ?></h1>
  <p><?= $q ? count($products).' produit(s) trouvé(s)' : 'Découvrez toute notre collection' ?></p>
</div>

<div class="filter-bar">
  <?php foreach($catLabels as $key => $label): ?>
  <a href="/Jolly_Beauty/products.php?category=<?= $key ?>" class="filter-tab <?= $cat===$key?'active':'' ?>"><?= $label ?></a>
  <?php endforeach; ?>
  <select class="filter-sort" onchange="window.location='/Jolly_Beauty/products.php?category=<?= $cat ?>&sort='+this.value">
    <option value="default" <?= $sort==='default'?'selected':'' ?>>Par défaut</option>
    <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Prix croissant</option>
    <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Prix décroissant</option>
    <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Nouveautés</option>
  </select>
</div>

<section class="products-section">
  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 20px;">
      <p style="font-size:3rem;margin-bottom:16px;">🌸</p>
      <p style="color:var(--muted);margin-bottom:24px;">Aucun produit trouvé.</p>
      <a href="/Jolly_Beauty/products.php" class="btn btn--rose">Voir toute la collection</a>
    </div>
  <?php else: ?>
    <div class="products-grid-full">
      <?php
      $imgDir = __DIR__ . '/images/';
      $allImgs = is_dir($imgDir) ? array_values(array_filter(scandir($imgDir), fn($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f))) : [];
      foreach($products as $i => $p):
        $img = !empty($p['image']) ? '/Jolly_Beauty/images/'.basename($p['image']) : ($allImgs[$i % max(1,count($allImgs))] ?? null);
      ?>
      <div class="product-card" data-reveal data-reveal-delay="<?= $i % 3 ?>">
        <div class="product-card__img-wrap">
          <?php if ($img): ?><img src="<?= htmlspecialchars(str_starts_with($img,'/') ? $img : '/Jolly_Beauty/images/'.$img) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3">🌸</div><?php endif; ?>
          <?php if (!empty($p['badge'])): ?><span class="product-card__badge"><?= sanitize($p['badge']) ?></span><?php endif; ?>
          <div class="product-card__actions">
            <a href="/Jolly_Beauty/product.php?slug=<?= urlencode($p['slug']) ?>" class="card-action-btn" title="Voir">👁</a>
            <button class="card-action-btn" title="Favoris">♡</button>
          </div>
        </div>
        <div class="product-card__body">
          <div class="product-card__cat"><?= sanitize($p['category'] ?? '') ?></div>
          <a href="/Jolly_Beauty/product.php?slug=<?= urlencode($p['slug']) ?>"><div class="product-card__name"><?= sanitize($p['name']) ?></div></a>
          <div class="product-card__foot">
            <div class="product-card__price"><?= formatPrice($p['price']) ?></div>
            <button class="product-card__add" onclick="addToCart({id:'<?= $p['id'] ?>',name:'<?= addslashes(sanitize($p['name'])) ?>',price:<?= floatval($p['price']) ?>,image:'<?= addslashes($img ?? '') ?>'})" title="Ajouter">+</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
const revealObs = new IntersectionObserver(entries => entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('revealed'); }), {threshold:.1});
document.querySelectorAll('[data-reveal]').forEach(el => revealObs.observe(el));
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>