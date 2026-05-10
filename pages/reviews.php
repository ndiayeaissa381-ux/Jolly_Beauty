<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?mode=login', true, 302);
    exit;
}

$pageTitle = 'Mes Avis — Jolly Beauty';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg = $jbBase . '/assets/images/soins/soin-visage-1.jpg';
$extraCss = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

$me = currentUser();
$error = '';
$success = '';

// Récupérer les produits achetés par l'utilisateur pour lesquels il peut laisser un avis
$db = getDB();
$stmt = $db->prepare("
    SELECT DISTINCT p.id, p.name, p.slug, c.slug AS category,
           GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list
    FROM products p
    JOIN categories c ON c.id = p.category_id
    JOIN order_items oi ON oi.product_id = p.id
    JOIN orders o ON o.id = oi.order_id
    LEFT JOIN product_images pi ON pi.product_id = p.id
    WHERE o.user_id = ? AND o.status IN ('delivered', 'shipped') AND p.active = 1
    GROUP BY p.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$me['id']]);
$purchasedProducts = $stmt->fetchAll();

// Hydrater les images des produits
$purchasedProducts = array_map(function($product) {
    $product['images'] = $product['img_list'] ? explode('||', $product['img_list']) : [];
    unset($product['img_list']);
    
    // Normaliser les URLs d'images
    $cat = $product['category'] ?? '';
    $product['images'] = array_values(array_filter(array_map(
        fn($u, $i) => resolveProductImageUrl((string)$u, $cat, (int)$i),
        $product['images'],
        array_keys($product['images'])
    )));
    
    if (empty($product['images'])) {
        $fb = resolveProductImageUrl('', $cat, 0);
        if ($fb) {
            $product['images'] = [$fb];
        }
    }
    return $product;
}, $purchasedProducts);

// Récupérer les avis déjà laissés par l'utilisateur
$stmt = $db->prepare("
    SELECT r.*, p.name as product_name, p.slug as product_slug
    FROM reviews r
    JOIN products p ON p.id = r.product_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$me['id']]);
$userReviews = $stmt->fetchAll();

// Traitement du formulaire d'ajout d'avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $body = trim($_POST['body'] ?? '');
    
    if ($productId <= 0) {
        $error = 'Produit invalide.';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Veuillez choisir une note entre 1 et 5 étoiles.';
    } elseif (strlen($body) < 10) {
        $error = 'Votre avis doit contenir au moins 10 caractères.';
    } else {
        // Vérifier si l'utilisateur a bien acheté ce produit
        $hasPurchased = false;
        foreach ($purchasedProducts as $product) {
            if ((int)$product['id'] === $productId) {
                $hasPurchased = true;
                break;
            }
        }
        
        if (!$hasPurchased) {
            $error = 'Vous ne pouvez laisser un avis que sur les produits que vous avez achetés.';
        } else {
            // Vérifier si un avis existe déjà
            $checkStmt = $db->prepare('SELECT id FROM reviews WHERE product_id = ? AND user_id = ? LIMIT 1');
            $checkStmt->execute([$productId, $me['id']]);
            if ($checkStmt->fetch()) {
                $error = 'Vous avez déjà laissé un avis pour ce produit.';
            } else {
                try {
                    $insertStmt = $db->prepare('
                        INSERT INTO reviews (product_id, user_id, author, rating, body, verified, created_at)
                        VALUES (?, ?, ?, ?, ?, 1, NOW())
                    ');
                    $insertStmt->execute([
                        $productId,
                        $me['id'],
                        $me['name'],
                        $rating,
                        $body
                    ]);
                    
                    // Mettre à jour la note moyenne du produit
                    $updateStmt = $db->prepare('
                        UPDATE products p
                        SET rating = (
                            SELECT AVG(rating) FROM reviews WHERE product_id = ?
                        ),
                        reviews = (
                            SELECT COUNT(*) FROM reviews WHERE product_id = ?
                        )
                        WHERE id = ?
                    ');
                    $updateStmt->execute([$productId, $productId, $productId]);
                    
                    $success = 'Votre avis a été ajouté avec succès ! Merci pour votre retour.';
                    
                    // Rafraîchir la liste des avis
                    $stmt = $db->prepare("
                        SELECT r.*, p.name as product_name, p.slug as product_slug
                        FROM reviews r
                        JOIN products p ON p.id = r.product_id
                        WHERE r.user_id = ?
                        ORDER BY r.created_at DESC
                    ");
                    $stmt->execute([$me['id']]);
                    $userReviews = $stmt->fetchAll();
                    
                } catch (PDOException $e) {
                    $error = 'Une erreur est survenue. Veuillez réessayer.';
                }
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Mes Avis</h1>
      <p>Partagez votre expérience et aidez d'autres clientes à faire leur choix.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <?php if ($error !== ''): ?>
      <div class="sp-alert sp-alert--err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success !== ''): ?>
      <div class="sp-alert sp-alert--ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Section pour ajouter un nouvel avis -->
    <?php if (!empty($purchasedProducts)): ?>
      <div class="sp-form-card">
        <h2>Laisser un avis</h2>
        <p class="lead">Choisissez un produit que vous avez acheté et partagez votre expérience.</p>

        <form method="post" action="<?= $jbBase ?>/pages/reviews.php">
          <input type="hidden" name="add_review" value="1">
          
          <div class="sp-field">
            <label for="product_id">Produit *</label>
            <select id="product_id" name="product_id" required class="sp-select">
              <option value="">Choisissez un produit...</option>
              <?php foreach ($purchasedProducts as $product): 
                // Vérifier si un avis existe déjà pour ce produit
                $hasReviewed = false;
                foreach ($userReviews as $review) {
                    if ((int)$review['product_id'] === (int)$product['id']) {
                        $hasReviewed = true;
                        break;
                    }
                }
                if (!$hasReviewed):
              ?>
                <option value="<?= (int)$product['id'] ?>">
                  <?= htmlspecialchars($product['name']) ?>
                </option>
              <?php endif; endforeach; ?>
            </select>
          </div>

          <div class="sp-field">
            <label>Note *</label>
            <div class="rating-stars" id="ratingStars">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star" data-rating="<?= $i ?>">★</span>
              <?php endfor; ?>
              <input type="hidden" id="rating" name="rating" value="0" required>
            </div>
          </div>

          <div class="sp-field">
            <label for="body">Votre avis *</label>
            <textarea id="body" name="body" required placeholder="Décrivez votre expérience avec ce produit..." rows="5" minlength="10"></textarea>
          </div>

          <button type="submit" class="sp-submit">Publier mon avis</button>
        </form>
      </div>
    <?php else: ?>
      <div class="sp-form-card">
        <h2>Laisser un avis</h2>
        <p class="lead">Vous n'avez pas encore de produits éligibles pour laisser un avis.</p>
        <p>Les avis ne peuvent être laissés que sur les produits que vous avez achetés et qui ont été livrés.</p>
        <div style="margin-top:20px;">
          <a href="<?= $jbBase ?>/bijoux.php" class="btn btn-primary">Découvrir la boutique →</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Section des avis déjà laissés -->
    <?php if (!empty($userReviews)): ?>
      <div class="sp-form-card" style="margin-top:30px;">
        <h2>Mes avis publiés</h2>
        <p class="lead">Retrouvez tous les avis que vous avez partagés.</p>

        <div class="reviews-list">
          <?php foreach ($userReviews as $review): 
            $productImg = '';
            // Récupérer l'image du produit depuis les produits achetés
            foreach ($purchasedProducts as $product) {
                if ((int)$product['id'] === (int)$review['product_id']) {
                    $productImg = !empty($product['images'][0]) ? $product['images'][0] : '';
                    break;
                }
            }
          ?>
            <div class="review-item">
              <div class="review-header">
                <div class="review-product">
                  <?php if ($productImg): ?>
                    <img src="<?= htmlspecialchars($productImg) ?>" alt="<?= htmlspecialchars($review['product_name']) ?>" class="review-product-img">
                  <?php endif; ?>
                  <div class="review-product-info">
                    <h4><a href="<?= $jbBase ?>/product.php?slug=<?= urlencode($review['product_slug']) ?>"><?= htmlspecialchars($review['product_name']) ?></a></h4>
                    <div class="review-rating">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?= $i <= (int)$review['rating'] ? 'filled' : '' ?>">★</span>
                      <?php endfor; ?>
                    </div>
                  </div>
                </div>
                <div class="review-date">
                  <?= date('d/m/Y', strtotime($review['created_at'])) ?>
                </div>
              </div>
              <div class="review-body">
                <p><?= nl2br(htmlspecialchars($review['body'])) ?></p>
              </div>
              <?php if ($review['verified']): ?>
                <div class="review-verified">
                  ✓ Avis vérifié - Client ayant acheté ce produit
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap">
      <a href="<?= $jbBase ?>/login.php" class="btn btn-outline">← Retour à mon espace</a>
      <a href="<?= $jbBase ?>/bijoux.php" class="btn btn-primary">Découvrir la boutique →</a>
    </div>
  </div>
</div>

<style>
.sp-select {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  font-family: var(--font-sans);
  font-size: .86rem;
  color: var(--dark);
  background: var(--white);
  outline: none;
  transition: border .2s, box-shadow .2s;
}

.sp-select:focus {
  border-color: var(--rose-deep);
  box-shadow: 0 0 0 3px rgba(212,120,138,.12);
}

.rating-stars {
  display: flex;
  gap: 8px;
  font-size: 2rem;
}

.star {
  cursor: pointer;
  color: #ddd;
  transition: color 0.2s;
}

.star:hover,
.star.filled {
  color: #f39c12;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.review-item {
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 20px;
  background: var(--white);
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.review-product {
  display: flex;
  gap: 12px;
  align-items: center;
}

.review-product-img {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  object-fit: cover;
}

.review-product-info h4 {
  margin: 0 0 4px 0;
  font-size: 1rem;
}

.review-product-info h4 a {
  color: var(--dark);
  text-decoration: none;
}

.review-product-info h4 a:hover {
  color: var(--rose-deep);
}

.review-rating {
  display: flex;
  gap: 2px;
}

.review-rating .star {
  font-size: 1rem;
  cursor: default;
}

.review-date {
  color: var(--muted);
  font-size: 0.85rem;
}

.review-body p {
  margin: 0;
  line-height: 1.6;
  color: var(--text);
}

.review-verified {
  margin-top: 12px;
  padding: 6px 12px;
  background: rgba(92, 158, 122, 0.1);
  color: #3d7a5a;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
}

@media (max-width: 768px) {
  .review-header {
    flex-direction: column;
    gap: 12px;
  }
  
  .review-product {
    width: 100%;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('filled');
                } else {
                    s.classList.remove('filled');
                }
            });
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#f39c12';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    document.getElementById('ratingStars').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value);
        
        stars.forEach((s, index) => {
            if (index < currentRating) {
                s.style.color = '#f39c12';
            } else {
                s.style.color = '#ddd';
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
