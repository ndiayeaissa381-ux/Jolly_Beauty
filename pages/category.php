<?php
/**
 * Boutique par catégorie (ou toute la collection si c=all).
 * bijoux.php / soins-rituels.php / coffrets.php définissent $categorySlug avant include.
 */
require_once __DIR__ . '/../includes/config.php';

$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

$slug = isset($categorySlug) ? (string) $categorySlug : (string) ($_GET['c'] ?? '');
$slug = strtolower(trim($slug));
$searchQuery = trim((string) ($_GET['q'] ?? ''));

if ($slug === '' || $slug === 'all') {
    $slug = 'all';
    $pageTitle = 'Toute la collection — Jolly Beauty';
    $heading    = 'Toute la collection';
    $subheading = 'Découvrez nos bijoux, soins et coffrets.';
    $heroImg    = $jbBase . '/assets/images/slider/hero-1.jpg';
    $catRow     = null;
} else {
    $catRow = getCategoryBySlug($slug);
    if (!$catRow) {
        header('Location: ' . BASE_URL . '/index.php', true, 302);
        exit;
    }
    $name       = (string) ($catRow['name'] ?? ucfirst($slug));
    $pageTitle  = $name . ' — Jolly Beauty';
    $heading    = $name;
    $subheading = (string) ($catRow['label'] ?? 'Collection Jolly Beauty');
    $heroMap    = [
        'bijoux'   => $jbBase . '/assets/images/slider/hero-2.jpg',
        'soins'    => $jbBase . '/assets/images/slider/hero-3.jpg',
        'coffrets' => $jbBase . '/assets/images/slider/hero-4.jpg',
        'produits' => $jbBase . '/assets/images/slider/hero-1.jpg',
    ];
    $heroImg = $heroMap[$slug] ?? ($jbBase . '/assets/images/slider/hero-2.jpg');
}

$products = $slug === 'all'
    ? getProducts(null, $searchQuery, 'default', 500)
    : getProducts($slug, $searchQuery, 'default', 500);

$productCount = count($products);

$extraCss = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/category-rich.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="jb-cat-wrap">
  <div class="jb-cat-breadcrumb">
    <a href="<?= $jbBase ?>/index.php">Accueil</a> ›
    <span><?= $slug === 'all' ? 'Collection' : htmlspecialchars($heading) ?></span>
  </div>

  <section class="jb-cat-hero">
    <div class="jb-cat-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="jb-cat-hero__overlay"></div>
    <div class="jb-cat-hero__inner">
      <h1 class="jb-cat-hero__title"><?= htmlspecialchars($heading) ?></h1>
      <p class="jb-cat-hero__sub"><?= htmlspecialchars($subheading) ?></p>
      <p style="margin-top:8px;">
        <a href="<?= $jbBase ?>/index.php" class="btn btn--outline">← Accueil</a>
      </p>
    </div>
  </section>

  <?php if ($slug === 'all'): ?>
  <section class="jb-filters" style="background:var(--jb-bg,#fef9f6);">
    <div style="max-width:1100px;margin:0 auto;padding:28px 5% 8px;">
      <form method="get" action="<?= $jbBase ?>/category.php" class="jb-cat-search">
        <input type="hidden" name="c" value="all">
        <input type="search" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Rechercher…">
        <button type="submit" class="btn btn--dark" style="border-radius:999px;">Rechercher</button>
      </form>
      <div class="jb-filters__head">
        <h2><span id="jb-filter-count"><?= (int) $productCount ?></span> articles disponibles</h2>
        <select class="jb-sort-select" aria-label="Trier" onchange="jbSortProducts(this.value)">
          <option value="default">Tri par défaut</option>
          <option value="name">Nom (A–Z)</option>
          <option value="price_asc">Prix croissant</option>
          <option value="price_desc">Prix décroissant</option>
          <option value="newest">Nouveautés</option>
        </select>
      </div>
    </div>
  </section>
  <?php else: ?>
  <section class="jb-filters">
    <div style="max-width:1100px;margin:0 auto;padding:36px 5% 8px;">
      <div class="jb-filters__head">
        <h2><span id="jb-filter-count"><?= (int) $productCount ?></span>
          <?php if ($slug === 'bijoux'): ?> bijoux disponibles<?php elseif ($slug === 'soins'): ?> soins disponibles<?php elseif ($slug === 'coffrets'): ?> coffrets disponibles<?php else: ?> produits<?php endif; ?>
        </h2>
        <select class="jb-sort-select" aria-label="Trier" onchange="jbSortProducts(this.value)">
          <option value="name">Trier par nom</option>
          <option value="price_asc">Prix croissant</option>
          <option value="price_desc">Prix décroissant</option>
          <option value="newest">Nouveautés</option>
        </select>
      </div>

      <div class="jb-filters__grid">
        <?php if ($slug === 'bijoux'): ?>
        <div class="jb-filter-group">
          <h3>Sous-catégories</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-sub" value="bracelets" onchange="jbFilterProducts()"> Bracelets</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-sub" value="bagues" onchange="jbFilterProducts()"> Bagues</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-sub" value="colliers" onchange="jbFilterProducts()"> Colliers</label>
        </div>
        <div class="jb-filter-group">
          <h3>Prix</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="0-25" onchange="jbFilterProducts()"> Moins de 25 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="25-50" onchange="jbFilterProducts()"> 25 € – 50 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="50+" onchange="jbFilterProducts()"> Plus de 50 €</label>
        </div>
        <?php elseif ($slug === 'soins'): ?>
        <div class="jb-filter-group">
          <h3>Sous-catégories</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-sub" value="corps" onchange="jbFilterProducts()"> Soins corps</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-sub" value="visage" onchange="jbFilterProducts()"> Soins visage</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-sub" value="rituels" onchange="jbFilterProducts()"> Rituels beauté</label>
        </div>
        <div class="jb-filter-group">
          <h3>Prix</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="0-25" onchange="jbFilterProducts()"> Moins de 25 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="25-50" onchange="jbFilterProducts()"> 25 € – 50 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="50+" onchange="jbFilterProducts()"> Plus de 50 €</label>
        </div>
        <?php elseif ($slug === 'coffrets'): ?>
        <div class="jb-filter-group">
          <h3>Prix</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="0-50" onchange="jbFilterProducts()"> Moins de 50 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="50-100" onchange="jbFilterProducts()"> 50 € – 100 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="100+" onchange="jbFilterProducts()"> Plus de 100 €</label>
        </div>
        <?php else: ?>
        <div class="jb-filter-group">
          <h3>Prix</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="0-25" onchange="jbFilterProducts()"> Moins de 25 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="25-50" onchange="jbFilterProducts()"> 25 € – 50 €</label>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-price" value="50+" onchange="jbFilterProducts()"> Plus de 50 €</label>
        </div>
        <?php endif; ?>

        <div class="jb-filter-group">
          <h3>Disponibilité</h3>
          <label class="jb-filter-label"><input type="checkbox" class="jb-filter-stock" value="in_stock" onchange="jbFilterProducts()"> En stock uniquement</label>
        </div>
      </div>
      <?php if ($slug === 'bijoux' || $slug === 'soins' || $slug === 'coffrets'): ?>
      <p style="margin-top:8px;font-size:0.8rem;">
        <button type="button" class="btn btn--outline" style="font-size:0.75rem;padding:8px 16px;" onclick="jbResetFilters()">Réinitialiser les filtres</button>
      </p>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="jb-products-zone">
    <div style="max-width:1100px;margin:0 auto;">
      <?php if (!empty($products)): ?>
      <div class="jb-products-grid" id="jb-products-grid">
        <?php foreach ($products as $i => $p):
          $img = !empty($p['images'][0]) ? $p['images'][0] : '';
          $cat = (string) ($p['category'] ?? '');
          $subLower = strtolower((string) ($p['sub'] ?? ''));
          $stock = (int) ($p['stock'] ?? 0);
          $desc = (string) ($p['short'] ?? '');
          if ($desc === '') {
              $desc = (string) ($p['description'] ?? '');
          }
          $descShort = mb_strlen($desc) > 120 ? mb_substr($desc, 0, 117) . '…' : $desc;
          $payload = [
              'id'       => (string) $p['id'],
              'name'     => (string) $p['name'],
              'price'    => (float) $p['price'],
              'image'    => $img,
              'category' => $cat,
          ];
          $payloadJson = htmlspecialchars(
              json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP),
              ENT_QUOTES,
              'UTF-8'
          );
          ?>
        <article class="jb-rich-card"
          data-idx="<?= (int) $i ?>"
          data-id="<?= (int) $p['id'] ?>"
          data-name="<?= htmlspecialchars(strtolower((string) $p['name']), ENT_QUOTES, 'UTF-8') ?>"
          data-price="<?= htmlspecialchars((string) $p['price'], ENT_QUOTES, 'UTF-8') ?>"
          data-stock="<?= $stock ?>"
          data-sub="<?= htmlspecialchars($subLower, ENT_QUOTES, 'UTF-8') ?>">
          <div class="jb-rich-card__img">
            <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($p['slug']) ?>">
              <?php if ($img !== ''): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string) $p['name']) ?>" loading="lazy" width="400" height="260">
              <?php else: ?>
                <div style="display:grid;place-items:center;height:100%;font-size:2.5rem;opacity:.25;">🌸</div>
              <?php endif; ?>
            </a>
            <?php if (!empty($p['badge'])): ?><span class="jb-rich-card__badge"><?= sanitize((string) $p['badge']) ?></span><?php endif; ?>
            <?php if ($stock > 0 && $stock <= 5): ?>
              <span class="jb-rich-card__badge jb-rich-card__badge--low">Plus que <?= $stock ?> en stock</span>
            <?php endif; ?>
            <?php if ($stock <= 0): ?>
              <span class="jb-rich-card__badge jb-rich-card__badge--out">Rupture</span>
            <?php endif; ?>
          </div>
          <div class="jb-rich-card__body">
            <h3><a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($p['slug']) ?>"><?= sanitize((string) $p['name']) ?></a></h3>
            <?php if ($descShort !== ''): ?><p class="jb-rich-card__desc"><?= sanitize($descShort) ?></p><?php endif; ?>
            <div class="jb-rich-card__price"><?= formatPrice((float) $p['price']) ?></div>
            <div class="jb-rich-card__actions">
              <button type="button" class="btn--solid jb-add-btn" data-add="<?= $payloadJson ?>" <?= $stock <= 0 ? ' disabled' : '' ?>>Ajouter au panier</button>
              <button type="button" class="jb-rich-card__wish" title="Favoris" aria-label="Favoris">♡</button>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="jb-empty">
        <p>Aucun produit pour cette sélection.</p>
        <p style="margin-top:16px;"><a class="btn btn--dark" href="<?= $jbBase ?>/index.php">Retour à l’accueil</a></p>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <?php
  if ($slug === 'bijoux') {
      include __DIR__ . '/../includes/partials/category-extra-bijoux.php';
  } elseif ($slug === 'soins') {
      include __DIR__ . '/includes/partials/category-extra-soins.php';
  } elseif ($slug === 'coffrets') {
      include __DIR__ . '/includes/partials/category-extra-coffrets.php';
  }
?>
</div>

<script>window.JB_CATEGORY_SLUG=<?= json_encode($slug, JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="<?= $jbBase ?>/assets/js/category-rich.js" defer></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
