<?php
/**
 * Page Soins & Rituels - Design élégant avec sidebar, filtres et grille produits
 */
require_once __DIR__ . '/../includes/config.php';

$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Soins & Rituels — Jolly Beauty';

// Récupérer les produits de la catégorie soins
$products = getProducts('soins', '', 'default', 500);
$productCount = count($products);

$extraCss = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/category-rich.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="soins-page">
  <!-- Hero Section -->
  <section class="cat-hero soins-hero">
    <div class="cat-hero__content">
      <h1 class="cat-hero__title">Soins &amp; Rituels</h1>
      <p class="cat-hero__tagline">Prenez soin de vous, naturellement.</p>
      <hr class="cat-hero__rule">
      <p class="cat-hero__desc">
        Des soins naturels, doux et efficaces<br>
        pour nourrir votre peau et apaiser votre esprit.
      </p>
    </div>
    <div class="cat-hero__visual">
      <img src="<?= $jbBase ?>/assets/images/soins/soin-corps-1.jpg" alt="Gommage Corps Rose & Sucre Jolly Beauty" style="transform: scale(0.92); object-fit: cover; width: 100%; height: 100%;">
    </div>
  </section>

  <!-- Filter Tabs -->
  <div class="cat-filter-tabs">
    <a href="#" class="cat-filter-tab active" data-filter="all">Tous les soins</a>
    <a href="#" class="cat-filter-tab" data-filter="visage">Soins visage</a>
    <a href="#" class="cat-filter-tab" data-filter="corps">Soins corps</a>
    <a href="#" class="cat-filter-tab" data-filter="cheveux">Soins cheveux</a>
    <a href="#" class="cat-filter-tab" data-filter="rituels">Rituels &amp; accessoires</a>
    <div class="cat-sort-wrap">
      <select class="cat-sort-select" aria-label="Trier" onchange="jbSortProducts(this.value)">
        <option value="default">Trier par</option>
        <option value="price_asc">Prix croissant</option>
        <option value="price_desc">Prix décroissant</option>
        <option value="name">Nom (A-Z)</option>
      </select>
      <span class="cat-sort-arrow">▼</span>
    </div>
  </div>

  <!-- Main Content: Sidebar + Products -->
  <div class="cat-body">
    <!-- Sidebar -->
    <aside class="cat-sidebar">
      <h3 class="sidebar-group-title">Catégories</h3>
      <nav class="sidebar-nav">
        <a href="#" class="sidebar-link active" data-filter="all">Tous les soins</a>
        <a href="#" class="sidebar-link" data-filter="visage">Soins visage</a>
        <a href="#" class="sidebar-link" data-filter="corps">Soins corps</a>
        <a href="#" class="sidebar-link" data-filter="cheveux">Soins cheveux</a>
        <a href="#" class="sidebar-link" data-filter="rituels">Rituels &amp; accessoires</a>
      </nav>

      <hr class="sidebar-divider">

      <h3 class="sidebar-group-title">Besoins</h3>
      <nav class="sidebar-nav">
        <a href="#" class="sidebar-link">Hydratation</a>
        <a href="#" class="sidebar-link">Nutrition</a>
        <a href="#" class="sidebar-link">Éclat</a>
        <a href="#" class="sidebar-link">Apaisement</a>
        <a href="#" class="sidebar-link">Purification</a>
      </nav>

      <div class="sidebar-quality">
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">🌿</span>
          <div class="sidebar-quality-text">
            <strong>Formules naturelles</strong>
            <span>Des ingrédients d'origine naturelle et sélectionnés avec soin.</span>
          </div>
        </div>
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">🛡️</span>
          <div class="sidebar-quality-text">
            <strong>Respect de la peau</strong>
            <span>Douceur et efficacité pour tous les types de peaux.</span>
          </div>
        </div>
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">💆</span>
          <div class="sidebar-quality-text">
            <strong>Rituels bien-être</strong>
            <span>Des textures sensorielles pour un véritable moment de détente.</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Products Grid -->
    <section class="cat-products">
      <div class="products-grid-4" id="soins-products-grid">
        <?php
        // Products mock data for display if no products in DB
        $mockProducts = [
            ['name' => 'Gommage Corps<br>Rose & Sucre', 'price' => 26.90, 'image' => 'soin-corps-1.jpg', 'sub' => 'corps'],
            ['name' => 'Crème Hydratante<br>Fleur de Rose', 'price' => 28.90, 'image' => 'creme-visage.jpg', 'sub' => 'visage'],
            ['name' => 'Huile Nourrissante<br>Amande Douce', 'price' => 22.90, 'image' => 'soin-corps-2.jpg', 'sub' => 'corps'],
            ['name' => 'Beurre Corps<br>Karité & Vanille', 'price' => 24.90, 'image' => 'soin-visage-1.jpg', 'sub' => 'corps'],
            ['name' => 'Savon Surgras<br>Fleur d\'Oranger', 'price' => 8.90, 'image' => 'soin-visage-2.jpg', 'sub' => 'corps'],
            ['name' => 'Eau Florale<br>Rose de Damas', 'price' => 16.90, 'image' => 'soin-corps-1.jpg', 'sub' => 'visage'],
            ['name' => 'Masque Visage<br>Argile Rose', 'price' => 19.90, 'image' => 'soin-visage-1.jpg', 'sub' => 'visage'],
            ['name' => 'Baume à Lèvres<br>Nourrissant', 'price' => 7.90, 'image' => 'soin-visage-2.jpg', 'sub' => 'visage'],
        ];

        $displayProducts = !empty($products) ? $products : $mockProducts;

        foreach ($displayProducts as $i => $p):
            $img = !empty($p['images'][0]) ? $p['images'][0] : $jbBase . '/assets/images/soins/' . ($p['image'] ?? 'creme-visage.jpg');
            $name = $p['name'] ?? 'Produit';
            $price = $p['price'] ?? 24.90;
            $slug = !empty($p['slug']) ? $p['slug'] : '#';
            $sub = $p['sub'] ?? 'all';
        ?>
        <article class="prod-card" data-sub="<?= $sub ?>">
          <div class="prod-card__img-wrap">
            <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($slug) ?>">
              <img src="<?= htmlspecialchars($img) ?>" alt="<?= strip_tags($name) ?>" loading="lazy">
            </a>
          </div>
          <div class="prod-card__body">
            <h3 class="prod-card__name"><?= $name ?></h3>
            <p class="prod-card__price"><?= number_format($price, 2, ',', ' ') ?> €</p>
            <button type="button" class="prod-card__add-btn" onclick="addToCart({id: '<?= md5(strip_tags($name)) ?>', name: '<?= htmlspecialchars(strip_tags($name)) ?>', price: <?= str_replace(',', '.', $price) ?>, image: '<?= htmlspecialchars($img) ?>', category: 'soins'})">
              Ajouter au panier
              <span class="cart-icon">🛒</span>
            </button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <!-- Editorial Section -->
  <section class="cat-editorial">
    <div class="cat-editorial__img">
      <img src="<?= $jbBase ?>/assets/images/soins/soin-visage-1.jpg" alt="Beurre Corps Karité & Vanille Jolly Beauty" style="object-fit: cover; width: 100%; height: 100%;">
    </div>
    <div class="cat-editorial__content">
      <h2 class="cat-editorial__title">Un moment pour vous,<br>chaque jour.</h2>
      <p class="cat-editorial__desc">
        Nos soins s'intègrent dans vos rituels pour révéler
        votre beauté naturelle et prendre soin de vous.
      </p>
      <a href="<?= $jbBase ?>/rituels.php" class="cat-editorial__btn">Découvrir les rituels</a>
    </div>
  </section>
</div>

<!-- Cart Toast -->
<div class="cart-toast" id="cartToast">✓ Produit ajouté au panier</div>

<script>
// Simple filter functionality
function jbSortProducts(sortType) {
  const grid = document.getElementById('soins-products-grid');
  const cards = Array.from(grid.querySelectorAll('.prod-card'));

  cards.sort((a, b) => {
    const nameA = a.querySelector('.prod-card__name').textContent;
    const nameB = b.querySelector('.prod-card__name').textContent;
    const priceA = parseFloat(a.querySelector('.prod-card__price').textContent.replace(' €', '').replace(',', '.'));
    const priceB = parseFloat(b.querySelector('.prod-card__price').textContent.replace(' €', '').replace(',', '.'));

    switch(sortType) {
      case 'name': return nameA.localeCompare(nameB);
      case 'price_asc': return priceA - priceB;
      case 'price_desc': return priceB - priceA;
      default: return 0;
    }
  });

  cards.forEach(card => grid.appendChild(card));
}

// Filter tabs functionality
document.querySelectorAll('.cat-filter-tab').forEach(tab => {
  tab.addEventListener('click', (e) => {
    e.preventDefault();
    document.querySelectorAll('.cat-filter-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    const filter = tab.dataset.filter;
    const cards = document.querySelectorAll('.prod-card');

    cards.forEach(card => {
      if (filter === 'all' || card.dataset.sub === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// Sidebar filter functionality
document.querySelectorAll('.sidebar-link[data-filter]').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
    link.classList.add('active');

    const filter = link.dataset.filter;
    const cards = document.querySelectorAll('.prod-card');

    cards.forEach(card => {
      if (filter === 'all' || card.dataset.sub === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });

    // Sync top tabs
    document.querySelectorAll('.cat-filter-tab').forEach(tab => {
      tab.classList.toggle('active', tab.dataset.filter === filter);
    });
  });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
