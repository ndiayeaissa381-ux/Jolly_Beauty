<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../includes/config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$user = currentUser();
$pageTitle = 'Mes Favoris — Jolly Beauty';

// Récupérer les favoris directement depuis la base de données
$favorites = [];
try {
    $userId = (int)$_SESSION['jb_user']['id'];
    $stmt = getDB()->prepare("
        SELECT p.*, f.created_at as favorite_date,
               GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order) as images
        FROM favorites f
        JOIN products p ON p.id = f.product_id
        LEFT JOIN product_images pi ON pi.product_id = p.id
        WHERE f.user_id = ? AND p.active = 1
        GROUP BY p.id
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$userId]);
    $favoritesRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traiter les images comme dans l'API
    foreach ($favoritesRaw as $fav) {
        $images = !empty($fav['images']) ? explode(',', $fav['images']) : [];
        $fav['images'] = array_map(function($img) {
            return trim($img);
        }, $images);
        $favorites[] = $fav;
    }
} catch (Exception $e) {
    error_log('Erreur favorites: ' . $e->getMessage());
    $favorites = [];
}

include __DIR__ . '/../includes/header.php';
?>

<div class="dash-page">
  <nav class="dash-nav">
    <a href="<?= $jbBase ?>/index.php" class="dash-nav-logo">Jolly <span>Beauty</span></a>
    <div class="dash-nav-right">
      <span class="dash-nav-email"><?= sanitize($user['email']) ?></span>
      <form method="POST" style="display:inline">
        <input type="hidden" name="action" value="logout">
        <button class="dash-logout-btn">Se déconnecter</button>
      </form>
    </div>
  </nav>

  <div class="dash-container">
    <div class="dash-eyebrow">Mes Favoris</div>
    <h1 class="dash-welcome">Mes <em>Favoris</em> ♡</h1>
    <p class="dash-sub">
      <?php if (empty($favorites)): ?>
        Vous n'avez pas encore de favoris. Explorez nos collections et ajoutez les pièces que vous adorez !
      <?php else: ?>
        <?= count($favorites) ?> <?= count($favorites) > 1 ? 'pièces' : 'pièce' ?> sauvegardée(s)
      <?php endif; ?>
    </p>

    <?php if (!empty($favorites)): ?>
    <div class="products-grid" style="margin-top: 40px;">
      <?php foreach($favorites as $product):
        $img = !empty($product['images']) && is_array($product['images']) ? $product['images'][0] : null;
      ?>
      <div class="product-card">
        <div class="product-card__img-wrap">
          <?php if ($img): ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($product['name']) ?>" loading="lazy">
          <?php else: ?>
            <div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3">🌸</div>
          <?php endif; ?>
          <button class="favorite-btn favorited" 
                  data-product-id="<?= htmlspecialchars($product['id']) ?>"
                  data-product-name="<?= htmlspecialchars($product['name']) ?>"
                  onclick="toggleFavorite('<?= htmlspecialchars($product['id']) ?>', this)"
                  title="Retirer des favoris">♥</button>
        </div>
        <div class="product-card__body">
          <div class="product-card__cat"><?= sanitize($product['category'] ?? '') ?></div>
          <a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($product['slug']) ?>">
            <div class="product-card__name"><?= sanitize($product['name']) ?></div>
          </a>
          <div class="product-card__foot">
            <div class="product-card__price"><?= formatPrice($product['price']) ?></div>
            <button class="product-card__add" 
                    onclick="addToCart({
                      id:'<?= $product['id'] ?>',
                      name:'<?= addslashes(sanitize($product['name'])) ?>',
                      price:<?= floatval($product['price']) ?>,
                      image:'<?= addslashes($img ?? '') ?>',
                      category:'<?= addslashes(sanitize($product['category'] ?? '')) ?>'
                    })">+</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 60px 20px;">
      <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;">♡</div>
      <h3 style="color: var(--dark); margin-bottom: 12px;">Pas encore de favoris</h3>
      <p style="color: var(--muted); margin-bottom: 30px;">
        Explorez nos collections et cliquez sur le cœur ♡ pour ajouter vos pièces préférées à vos favoris.
      </p>
      <a href="<?= $jbBase ?>/bijoux.php" class="btn btn--rose">Découvrir les bijoux</a>
    </div>
    <?php endif; ?>

    <div style="margin-top: 60px; text-align: center;">
      <a href="<?= $jbBase ?>/login.php" class="btn btn--outline">← Retour à mon espace</a>
    </div>
  </div>
</div>

<style>
/* Assurer que le conteneur principal a une hauteur minimale */
.dash-container {
  min-height: 60vh;
}

/* Style pour les cartes produits dans les favoris */
.product-card {
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* Bouton favori amélioré */
.favorite-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.95);
  color: var(--rose-deep);
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.favorite-btn:hover {
  background: white;
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.favorite-btn.favorited {
  color: #e74c3c;
  background: rgba(231, 76, 60, 0.1);
}

/* Amélioration du message "pas de favoris" */
.dash-sub {
  margin: 20px 0 40px 0;
  font-size: 1.1rem;
  line-height: 1.6;
}

/* Style pour la grille de produits */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 30px;
  margin-top: 40px;
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
