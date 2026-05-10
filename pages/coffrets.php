<?php
/**
 * Page Coffrets - Design élégant avec sidebar, filtres et grille produits
 */
require_once __DIR__ . '/../includes/config.php';

$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Coffrets — Jolly Beauty';

// Récupérer les produits de la catégorie coffrets
$products = getProducts('coffrets', '', 'default', 500);
$productCount = count($products);

$extraCss = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/category-rich.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="coffrets-page">
  <!-- Hero Section -->
  <section class="cat-hero coffrets-hero">
    <div class="cat-hero__content">
      <h1 class="cat-hero__title">Coffrets</h1>
      <p class="cat-hero__tagline">Offrez l'art du bien-être.</p>
      <hr class="cat-hero__rule">
      <p class="cat-hero__desc">
        Des coffrets cadeaux pensés avec amour,<br>
        pour choyer vos proches avec nos meilleurs soins et bijoux.
      </p>
    </div>
    <div class="cat-hero__visual">
      <img src="<?= $jbBase ?>/assets/images/coffrets/coffret-prestige-1.jpg" alt="Collection Coffrets Jolly Beauty">
    </div>
  </section>

  <!-- Filter Tabs -->
  <div class="cat-filter-tabs">
    <a href="#" class="cat-filter-tab active" data-filter="all">Tous les coffrets</a>
    <a href="#" class="cat-filter-tab" data-filter="bijoux">Coffrets bijoux</a>
    <a href="#" class="cat-filter-tab" data-filter="soins">Coffrets soins</a>
    <a href="#" class="cat-filter-tab" data-filter="mixtes">Coffrets mixtes</a>
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
        <a href="#" class="sidebar-link active" data-filter="all">Tous les coffrets</a>
        <a href="#" class="sidebar-link" data-filter="bijoux">Coffrets bijoux</a>
        <a href="#" class="sidebar-link" data-filter="soins">Coffrets soins</a>
        <a href="#" class="sidebar-link" data-filter="mixtes">Coffrets mixtes</a>
      </nav>

      <hr class="sidebar-divider">

      <h3 class="sidebar-group-title">Prix</h3>
      <nav class="sidebar-nav">
        <a href="#" class="sidebar-link">Moins de 50 €</a>
        <a href="#" class="sidebar-link">50 € – 100 €</a>
        <a href="#" class="sidebar-link">Plus de 100 €</a>
      </nav>

      <div class="sidebar-quality">
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">🎁</span>
          <div class="sidebar-quality-text">
            <strong>Emballage cadeau</strong>
            <span>Offert pour tous nos coffrets, prêts à offrir.</span>
          </div>
        </div>
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">💌</span>
          <div class="sidebar-quality-text">
            <strong>Message personnalisé</strong>
            <span>Carte dédicace incluse sur demande.</span>
          </div>
        </div>
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">✨</span>
          <div class="sidebar-quality-text">
            <strong>Qualité premium</strong>
            <span>Soins et bijoux sélectionnés avec soin.</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Products Grid -->
    <section class="cat-products">
      <div class="products-grid-3" id="coffrets-products-grid">
        <?php
        // Products mock data for display if no products in DB
        $mockProducts = [
            ['name' => 'Coffret Découverte', 'price' => 49.90, 'image' => 'coffret-luxe.jpg', 'sub' => 'soins'],
            ['name' => 'Coffret Prestige', 'price' => 89.90, 'image' => 'coffret-prestige-1.jpg', 'sub' => 'mixtes'],
            ['name' => 'Coffret Bijoux', 'price' => 69.90, 'image' => 'coffret-prestige-2.jpg', 'sub' => 'bijoux'],
            ['name' => 'Coffret Rituel', 'price' => 59.90, 'image' => 'coffret-luxe.jpg', 'sub' => 'soins'],
            ['name' => 'Coffret Luxe', 'price' => 129.90, 'image' => 'coffret-prestige-1.jpg', 'sub' => 'mixtes'],
            ['name' => 'Coffret Mini', 'price' => 39.90, 'image' => 'coffret-prestige-2.jpg', 'sub' => 'bijoux'],
        ];

        $displayProducts = !empty($products) ? $products : $mockProducts;

        foreach ($displayProducts as $i => $p):
            $img = !empty($p['images'][0]) ? $p['images'][0] : $jbBase . '/assets/images/coffrets/' . ($p['image'] ?? 'coffret-prestige-1.jpg');
            $name = $p['name'] ?? 'Coffret';
            $price = $p['price'] ?? 59.90;
            $slug = !empty($p['slug']) ? $p['slug'] : '#';
            $sub = $p['sub'] ?? 'all';
        ?>
        <article class="prod-card" data-sub="<?= $sub ?>">
          <div class="prod-card__img-wrap">
            <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($slug) ?>">
              <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
            </a>
          </div>
          <div class="prod-card__body">
            <h3 class="prod-card__name"><?= htmlspecialchars($name) ?></h3>
            <p class="prod-card__price"><?= number_format($price, 2, ',', ' ') ?> €</p>
            <button type="button" class="prod-card__add-btn" onclick="addToCart({id: '<?= md5($name) ?>', name: '<?= htmlspecialchars($name) ?>', price: <?= str_replace(',', '.', $price) ?>, image: '<?= htmlspecialchars($img) ?>', category: 'coffrets'})">
              Ajouter au panier
              <span class="cart-icon">🛒</span>
            </button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <!-- Promo Banner -->
  <section class="coffrets-promo">
    <div class="coffrets-promo__image">
      <img src="<?= $jbBase ?>/assets/images/coffrets/coffret-prestige-2.jpg" alt="Coffret cadeau Jolly Beauty">
    </div>
    <div class="coffrets-promo__content">
      <h2>Un cadeau qui fait du bien,<br>à coup sûr.</h2>
      <p>Nos coffrets sont composés avec amour pour offrir une expérience de bien-être unique.</p>
      <a href="<?= $jbBase ?>/coffrets.php" class="coffrets-promo__btn">DÉCOUVRIR TOUS LES COFFRETS</a>
    </div>
  </section>

  <!-- Features Bar -->
  <section class="coffrets-features">
    <div class="coffrets-features__container">
      <div class="coffrets-feature">
        <span class="coffrets-feature__icon">🎁</span>
        <div class="coffrets-feature__text">
          <h4>EMBALLAGE CADEAU</h4>
          <p>Offert et soigné</p>
        </div>
      </div>
      <div class="coffrets-feature">
        <span class="coffrets-feature__icon">💌</span>
        <div class="coffrets-feature__text">
          <h4>CARTE DÉDICACE</h4>
          <p>Message personnalisé</p>
        </div>
      </div>
      <div class="coffrets-feature">
        <span class="coffrets-feature__icon">🚚</span>
        <div class="coffrets-feature__text">
          <h4>LIVRAISON RAPIDE</h4>
          <p>2-5 jours ouvrés</p>
        </div>
      </div>
      <div class="coffrets-feature">
        <span class="coffrets-feature__icon">💝</span>
        <div class="coffrets-feature__text">
          <h4>SATISFAIT OU REMBOURSÉ</h4>
          <p>14 jours de rétractation</p>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Cart Toast -->
<div class="cart-toast" id="cartToast">✓ Produit ajouté au panier</div>

<style>
/* Additional styles for coffrets page */
.coffrets-hero {
  background: linear-gradient(135deg, #F7F3EF 0%, #f5e4dc 50%, #ede0d5 100%);
}

.products-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

@media (max-width: 1100px) {
  .products-grid-3 { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .products-grid-3 { grid-template-columns: repeat(2, 1fr); gap: 16px; }
}

@media (max-width: 480px) {
  .products-grid-3 { grid-template-columns: 1fr; }
}

.prod-card__body {
  padding: 16px 0;
  text-align: center;
}

.prod-card__name {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  font-weight: 500;
  color: #2b2b2b;
  margin-bottom: 8px;
}

.prod-card__price {
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  font-weight: 600;
  color: #2b2b2b;
  margin-bottom: 12px;
}

.prod-card__add-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  border: 1px solid #d8a7a7;
  background: transparent;
  color: #4a4a4a;
  font-family: 'Inter', sans-serif;
  font-size: 0.7rem;
  font-weight: 500;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 4px;
}

.prod-card__add-btn:hover {
  background: #d8a7a7;
  color: #fff;
}

.cart-icon {
  font-size: 0.9rem;
}

/* Promo Banner */
.coffrets-promo {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: #F7F3EF;
  margin: 0 5% 60px;
  border-radius: 16px;
  overflow: hidden;
}

.coffrets-promo__image {
  min-height: 300px;
}

.coffrets-promo__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.coffrets-promo__content {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 60px;
  text-align: center;
}

.coffrets-promo__content h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  font-weight: 400;
  color: #2b2b2b;
  margin-bottom: 16px;
  line-height: 1.4;
}

.coffrets-promo__content p {
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  color: #6f6f6f;
  margin-bottom: 24px;
}

.coffrets-promo__btn {
  display: inline-block;
  padding: 14px 28px;
  background: #d8a7a7;
  color: #fff;
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  text-decoration: none;
  border-radius: 4px;
  transition: background 0.3s ease;
  align-self: center;
}

.coffrets-promo__btn:hover {
  background: #c48b8b;
}

@media (max-width: 900px) {
  .coffrets-promo {
    grid-template-columns: 1fr;
  }
  .coffrets-promo__image {
    min-height: 200px;
  }
  .coffrets-promo__content {
    padding: 40px 24px;
  }
}

/* Features Bar */
.coffrets-features {
  background: #fff;
  padding: 40px 5%;
  border-top: 1px solid rgba(216,167,167,0.2);
}

.coffrets-features__container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
}

.coffrets-feature {
  display: flex;
  align-items: center;
  gap: 16px;
}

.coffrets-feature__icon {
  font-size: 1.8rem;
  color: #d8a7a7;
}

.coffrets-feature__text h4 {
  font-family: 'Inter', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #2b2b2b;
  margin-bottom: 4px;
}

.coffrets-feature__text p {
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  color: #6f6f6f;
  margin: 0;
}

@media (max-width: 900px) {
  .coffrets-features__container {
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
  }
}

@media (max-width: 480px) {
  .coffrets-features__container {
    grid-template-columns: 1fr;
    gap: 24px;
  }
}
</style>

<script>
// Simple filter functionality
function jbSortProducts(sortType) {
  const grid = document.getElementById('coffrets-products-grid');
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
