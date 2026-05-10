<?php
/**
 * Page Bijoux - Design élégant avec sidebar, filtres et grille produits
 */
require_once __DIR__ . '/includes/config.php';

$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Bijoux — Jolly Beauty';

// Récupérer les produits de la catégorie bijoux
$products = getProducts('bijoux', '', 'default', 500);
$productCount = count($products);

$extraCss = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/category-rich.css">';

include __DIR__ . '/includes/header.php';
?>

<div class="bijoux-page">
  <!-- Hero Section -->
  <section class="cat-hero bijoux-hero">
    <div class="cat-hero__content">
      <h1 class="cat-hero__title">Bijoux</h1>
      <p class="cat-hero__tagline">L'élégance au quotidien.</p>
      <hr class="cat-hero__rule">
      <p class="cat-hero__desc">
        Des bijoux en acier inoxydable, pensés pour<br>
        sublimer chaque instant de votre vie.
      </p>
    </div>
    <div class="cat-hero__visual">
      <img src="<?= $jbBase ?>/assets/images/bijoux/bijoux-bague-1.jpg" alt="Collection Bijoux Jolly Beauty">
    </div>
  </section>

  <!-- Filter Tabs -->
  <div class="cat-filter-tabs">
    <a href="#" class="cat-filter-tab active" data-filter="all">Tous les bijoux</a>
    <a href="#" class="cat-filter-tab" data-filter="bracelets">Bracelets</a>
    <a href="#" class="cat-filter-tab" data-filter="bagues">Bagues</a>
    <a href="#" class="cat-filter-tab" data-filter="colliers">Colliers</a>
    <a href="#" class="cat-filter-tab" data-filter="boucles">Boucles d'oreilles</a>
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
        <a href="#" class="sidebar-link active">Tous les bijoux</a>
        <a href="#" class="sidebar-link">Bracelets</a>
        <a href="#" class="sidebar-link">Bagues</a>
        <a href="#" class="sidebar-link">Colliers</a>
        <a href="#" class="sidebar-link">Boucles d'oreilles</a>
      </nav>

      <hr class="sidebar-divider">

      <h3 class="sidebar-group-title">Matières</h3>
      <nav class="sidebar-nav">
        <a href="#" class="sidebar-link">Acier inoxydable</a>
        <a href="#" class="sidebar-link">Plaqué or</a>
      </nav>

      <div class="sidebar-quality">
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">💧</span>
          <div class="sidebar-quality-text">
            <strong>Résistants à l'eau</strong>
            <span>Vos bijoux vous accompagnent partout.</span>
          </div>
        </div>
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">🤍</span>
          <div class="sidebar-quality-text">
            <strong>Hypoallergéniques</strong>
            <span>Conviennent aux peaux sensibles.</span>
          </div>
        </div>
        <div class="sidebar-quality-item">
          <span class="sidebar-quality-icon">✓</span>
          <div class="sidebar-quality-text">
            <strong>Garantie couleur</strong>
            <span>Brillance et éclat longue durée.</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Products Grid -->
    <section class="cat-products">
      <div class="products-grid-3" id="bijoux-products-grid">
        <?php
        // Products mock data for display if no products in DB
        $mockProducts = [
            ['id' => 'mock-1', 'name' => 'Bracelet Soleil', 'price' => 29.90, 'image' => 'bijoux-bracelet-1.jpg', 'sub' => 'bracelets', 'slug' => 'bracelet-soleil'],
            ['id' => 'mock-2', 'name' => 'Bague Torsadée', 'price' => 24.90, 'image' => 'bijoux-bague-1.jpg', 'sub' => 'bagues', 'slug' => 'bague-torsadee'],
            ['id' => 'mock-3', 'name' => 'Collier Lumière', 'price' => 33.90, 'image' => 'bijoux-collier-1.jpg', 'sub' => 'colliers', 'slug' => 'collier-lumiere'],
            ['id' => 'mock-4', 'name' => 'Bracelet Perles', 'price' => 27.90, 'image' => 'bijoux-bracelet-2.jpg', 'sub' => 'bracelets', 'slug' => 'bracelet-perles'],
            ['id' => 'mock-5', 'name' => 'Bague Douceur', 'price' => 26.90, 'image' => 'bijoux-bague-2.jpg', 'sub' => 'bagues', 'slug' => 'bague-douceur'],
            ['id' => 'mock-6', 'name' => 'Créoles Éclat', 'price' => 28.90, 'image' => 'bracelet-charms-eclat.jpg', 'sub' => 'boucles', 'slug' => 'creoles-eclat'],
        ];

        $displayProducts = !empty($products) ? $products : $mockProducts;

        foreach ($displayProducts as $i => $p):
            $img = !empty($p['images'][0]) ? $p['images'][0] : $jbBase . '/assets/images/bijoux/' . ($p['image'] ?? 'bijoux-collier-1.jpg');
            $name = $p['name'] ?? 'Produit';
            $price = $p['price'] ?? 29.90;
            $slug = !empty($p['slug']) ? $p['slug'] : 'bracelet-charms-eclat';
        ?>
        <article class="prod-card" data-sub="<?= $p['sub'] ?? 'all' ?>">
          <div class="prod-card__img-wrap" style="position: relative;">
            <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($slug) ?>">
              <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
            </a>
            <button class="favorite-btn" 
                    data-product-id="<?= !empty($p['id']) ? htmlspecialchars($p['id']) : 'mock-' . ($i + 1) ?>"
                    data-product-name="<?= htmlspecialchars($name) ?>"
                    onclick="toggleFavorite('<?= !empty($p['id']) ? htmlspecialchars($p['id']) : 'mock-' . ($i + 1) ?>', this)"
                    title="Ajouter aux favoris"
                    style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;">♡</button>
          </div>
          <div class="prod-card__info">
            <h3 class="prod-card__name"><?= htmlspecialchars($name) ?></h3>
            <p class="prod-card__price"><?= number_format($price, 2, ',', ' ') ?> €</p>
            <button type="button" class="prod-card__btn" onclick="addToCart({id: '<?= md5($name) ?>', name: '<?= htmlspecialchars($name) ?>', price: <?= str_replace(',', '.', $price) ?>, image: '<?= htmlspecialchars($img) ?>', category: 'bijoux'})">
              AJOUTER AU PANIER 🛒
            </button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <!-- Promo Banner -->
  <section class="bijoux-promo">
    <div class="bijoux-promo__image">
      <img src="<?= $jbBase ?>/assets/images/bijoux/bijoux-bracelet-1.jpg" alt="Collection Bijoux Jolly Beauty">
    </div>
    <div class="bijoux-promo__content">
      <h2>Des bijoux faits pour durer,<br>créés pour vous.</h2>
      <p>Acier inoxydable, résistants à l'eau et hypoallergéniques.</p>
      <a href="<?= $jbBase ?>/bijoux.php" class="bijoux-promo__btn">DÉCOUVRIR LA COLLECTION</a>
    </div>
  </section>

  <!-- Features Bar -->
  <section class="bijoux-features">
    <div class="bijoux-features__container">
      <div class="bijoux-feature">
        <span class="bijoux-feature__icon">🚚</span>
        <div class="bijoux-feature__text">
          <h4>LIVRAISON OFFERTE</h4>
          <p>Dès 60€ d'achat</p>
        </div>
      </div>
      <div class="bijoux-feature">
        <span class="bijoux-feature__icon">🔒</span>
        <div class="bijoux-feature__text">
          <h4>PAIEMENT SÉCURISÉ</h4>
          <p>Transactions 100% sécurisées</p>
        </div>
      </div>
      <div class="bijoux-feature">
        <span class="bijoux-feature__icon">🔄</span>
        <div class="bijoux-feature__text">
          <h4>RETOURS FACILES</h4>
          <p>14 jours pour changer d'avis</p>
        </div>
      </div>
      <div class="bijoux-feature">
        <span class="bijoux-feature__icon">💬</span>
        <div class="bijoux-feature__text">
          <h4>SERVICE CLIENT</h4>
          <p>À votre écoute</p>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
/* Additional styles for bijoux page */
.bijoux-hero {
  background: linear-gradient(135deg, #F7F3EF 0%, #f5e4dc 50%, #ede0d5 100%);
}

/* Boutons favoris - force la visibilité */
.prod-card .favorite-btn {
  position: absolute !important;
  top: 12px !important;
  right: 12px !important;
  display: inline-flex !important;
  visibility: visible !important;
  opacity: 1 !important;
  width: 36px !important;
  height: 36px !important;
  background: rgba(255, 255, 255, 0.9) !important;
  border: 2px solid #D8A7A7 !important;
  border-radius: 50% !important;
  color: #D8A7A7 !important;
  font-size: 1.1rem !important;
  cursor: pointer !important;
  z-index: 10 !important;
  transition: all 0.3s ease !important;
  backdrop-filter: blur(4px) !important;
  align-items: center !important;
  justify-content: center !important;
  font-weight: normal !important;
  line-height: 1 !important;
  padding: 0 !important;
  margin: 0 !important;
}

.prod-card .favorite-btn:hover {
  background: rgba(216, 167, 167, 0.9) !important;
  color: white !important;
  transform: scale(1.05) !important;
}

.prod-card .favorite-btn.favorited {
  background: #e74c3c !important;
  border-color: #e74c3c !important;
  color: white !important;
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

.prod-card__info {
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

.prod-card__btn {
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

.prod-card__btn:hover {
  background: #d8a7a7;
  color: #fff;
}

/* Promo Banner */
.bijoux-promo {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: #F7F3EF;
  margin: 0 5% 60px;
  border-radius: 16px;
  overflow: hidden;
}

.bijoux-promo__image {
  min-height: 300px;
}

.bijoux-promo__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.bijoux-promo__content {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 60px;
  text-align: center;
}

.bijoux-promo__content h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  font-weight: 400;
  color: #2b2b2b;
  margin-bottom: 16px;
  line-height: 1.4;
}

.bijoux-promo__content p {
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  color: #6f6f6f;
  margin-bottom: 24px;
}

.bijoux-promo__btn {
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

.bijoux-promo__btn:hover {
  background: #c48b8b;
}

@media (max-width: 900px) {
  .bijoux-promo {
    grid-template-columns: 1fr;
  }
  .bijoux-promo__image {
    min-height: 200px;
  }
  .bijoux-promo__content {
    padding: 40px 24px;
  }
}

/* Features Bar */
.bijoux-features {
  background: #fff;
  padding: 40px 5%;
  border-top: 1px solid rgba(216,167,167,0.2);
}

.bijoux-features__container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
}

.bijoux-feature {
  display: flex;
  align-items: center;
  gap: 16px;
}

.bijoux-feature__icon {
  font-size: 1.8rem;
  color: #d8a7a7;
}

.bijoux-feature__text h4 {
  font-family: 'Inter', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #2b2b2b;
  margin-bottom: 4px;
}

.bijoux-feature__text p {
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  color: #6f6f6f;
  margin: 0;
}

@media (max-width: 900px) {
  .bijoux-features__container {
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
  }
}

@media (max-width: 480px) {
  .bijoux-features__container {
    grid-template-columns: 1fr;
    gap: 24px;
  }
}
</style>

<script>
// Simple filter functionality
function jbSortProducts(sortType) {
  const grid = document.getElementById('bijoux-products-grid');
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
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
