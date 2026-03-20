<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Commande — Jolly Beauty';

// Build cart from session or POST data
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
}

// Handle order submission
$orderPlaced = false;
$orderNumber = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $firstName  = sanitize($_POST['first_name'] ?? '');
    $lastName   = sanitize($_POST['last_name'] ?? '');
    $email      = sanitize($_POST['email'] ?? '');
    $phone      = sanitize($_POST['phone'] ?? '');
    $address    = sanitize($_POST['address'] ?? '');
    $city       = sanitize($_POST['city'] ?? '');
    $zip        = sanitize($_POST['zip'] ?? '');
    $country    = sanitize($_POST['country'] ?? 'France');
    $promoCode  = sanitize($_POST['promo_code'] ?? '');

    if (!$firstName) $errors[] = 'Prénom requis.';
    if (!$lastName)  $errors[] = 'Nom requis.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if (!$address)   $errors[] = 'Adresse requise.';
    if (!$city)      $errors[] = 'Ville requise.';
    if (!$zip)       $errors[] = 'Code postal requis.';

    if (empty($errors) && !empty($cartItems)) {
        try {
            $db = getDB();
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }

            // Promo
            $discount = 0;
            if ($promoCode) {
                $promo = validatePromoCode($promoCode, $subtotal);
                if ($promo) {
                    $discount = $promo['discount_type'] === 'percent'
                        ? $subtotal * ($promo['discount_value'] / 100)
                        : $promo['discount_value'];
                }
            }

            $shipping = $subtotal >= 60 ? 0 : 5.90;
            $total = max(0, $subtotal - $discount) + $shipping;

            // Insert order
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, guest_email, guest_name, shipping_address, total_amount, promo_code, discount_amount, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $userId = isLoggedIn() ? currentUser()['id'] : null;
            $guestName = "$firstName $lastName";
            $shippingAddr = json_encode([
                'address' => $address, 'city' => $city,
                'zip' => $zip, 'country' => $country,
                'phone' => $phone
            ]);
            $stmt->execute([$userId, $email, $guestName, $shippingAddr, $total, $promoCode ?: null, $discount]);
            $orderId = $db->lastInsertId();

            // Insert items
            foreach ($cartItems as $item) {
                $stmtItem = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtItem->execute([$orderId, $item['id'] ?? null, $item['name'], $item['qty'], $item['price']]);
            }

            $orderNumber = 'JB-' . str_pad($orderId, 5, '0', STR_PAD_LEFT);
            $_SESSION['cart'] = [];
            $orderPlaced = true;

        } catch (Exception $e) {
            $errors[] = 'Erreur lors de la commande. Veuillez réessayer.';
        }
    }
}

// Compute totals for display
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] ?? 0) * ($item['qty'] ?? 1);
}
$shipping = $subtotal >= 60 ? 0 : 5.90;
$total = $subtotal + $shipping;

include __DIR__ . '/includes/header.php';
?>

<style>
.checkout-hero{background:var(--c-dark);padding:60px 0 30px;text-align:center;}
.checkout-hero h1{font-family:var(--font-serif);font-size:clamp(2rem,4vw,3rem);color:var(--c-cream);}
.checkout-hero .breadcrumb{color:var(--c-muted);font-size:.85rem;margin-top:.5rem;}
.checkout-hero .breadcrumb a{color:var(--c-gold);text-decoration:none;}

.checkout-wrap{max-width:1100px;margin:0 auto;padding:60px 20px;display:grid;grid-template-columns:1fr 380px;gap:40px;align-items:start;}
@media(max-width:768px){.checkout-wrap{grid-template-columns:1fr;}}

/* Form card */
.checkout-form-card{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.06);overflow:hidden;}
.checkout-section{padding:28px 32px;border-bottom:1px solid #f0ebe3;}
.checkout-section:last-child{border-bottom:none;}
.checkout-section h2{font-family:var(--font-serif);font-size:1.15rem;color:var(--c-dark);margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.checkout-section h2 span.step-num{width:28px;height:28px;background:var(--c-gold);color:#fff;border-radius:50%;display:grid;place-items:center;font-family:var(--font-sans);font-size:.8rem;font-weight:600;flex-shrink:0;}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.form-row.single{grid-template-columns:1fr;}
@media(max-width:500px){.form-row{grid-template-columns:1fr;}}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group label{font-size:.8rem;font-weight:600;color:var(--c-dark);letter-spacing:.04em;text-transform:uppercase;}
.form-group input,.form-group select{padding:11px 14px;border:1.5px solid #e8e0d5;border-radius:7px;font-family:var(--font-sans);font-size:.92rem;color:var(--c-dark);background:#fafaf8;transition:border-color .2s;}
.form-group input:focus,.form-group select:focus{outline:none;border-color:var(--c-gold);}

/* Payment info */
.payment-info{display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fdf9f3;border:1.5px dashed #e8d9b8;border-radius:8px;font-size:.85rem;color:var(--c-muted);}
.payment-info svg{flex-shrink:0;color:var(--c-gold);}

/* Promo */
.promo-row{display:flex;gap:10px;}
.promo-row input{flex:1;padding:10px 14px;border:1.5px solid #e8e0d5;border-radius:7px;font-size:.9rem;}
.promo-row input:focus{outline:none;border-color:var(--c-gold);}
.promo-row button{padding:10px 20px;background:var(--c-dark);color:var(--c-cream);border:none;border-radius:7px;font-size:.85rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .2s;}
.promo-row button:hover{background:var(--c-gold);}

/* Submit */
.btn-place-order{width:100%;padding:16px;background:var(--c-gold);color:#fff;border:none;border-radius:8px;font-family:var(--font-sans);font-size:1rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;margin-top:10px;transition:background .3s,transform .2s;}
.btn-place-order:hover{background:#a8874e;transform:translateY(-1px);}

/* Errors */
.error-box{background:#fff0f0;border:1px solid #ffcccc;border-radius:8px;padding:14px 18px;margin-bottom:20px;}
.error-box p{color:#c0392b;font-size:.88rem;margin:.2rem 0;}

/* Order summary */
.order-summary-card{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.06);padding:28px 28px;position:sticky;top:100px;}
.order-summary-card h2{font-family:var(--font-serif);font-size:1.1rem;color:var(--c-dark);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #f0ebe3;}
.summary-items{display:flex;flex-direction:column;gap:14px;margin-bottom:20px;}
.summary-item{display:flex;align-items:center;gap:12px;}
.summary-item img{width:52px;height:52px;object-fit:cover;border-radius:8px;background:#f5f0ea;}
.summary-item-info{flex:1;}
.summary-item-info .name{font-size:.9rem;font-weight:600;color:var(--c-dark);}
.summary-item-info .qty{font-size:.78rem;color:var(--c-muted);}
.summary-item-price{font-size:.92rem;font-weight:700;color:var(--c-dark);}
.summary-divider{border:none;border-top:1px solid #f0ebe3;margin:14px 0;}
.summary-line{display:flex;justify-content:space-between;align-items:center;font-size:.88rem;color:var(--c-muted);margin:.4rem 0;}
.summary-line.total{font-size:1.05rem;font-weight:700;color:var(--c-dark);margin-top:10px;}
.summary-line .free{color:#2ecc71;font-weight:600;}
.secure-badges{display:flex;justify-content:center;gap:16px;margin-top:18px;flex-wrap:wrap;}
.secure-badges span{font-size:.72rem;color:var(--c-muted);display:flex;align-items:center;gap:5px;}
.cart-empty-note{text-align:center;padding:40px 20px;color:var(--c-muted);}
.cart-empty-note a{color:var(--c-gold);font-weight:600;}

/* Success */
.order-success{max-width:580px;margin:80px auto;text-align:center;padding:20px;}
.order-success .check-icon{width:72px;height:72px;background:var(--c-gold);border-radius:50%;display:grid;place-items:center;margin:0 auto 24px;}
.order-success h2{font-family:var(--font-serif);font-size:2rem;color:var(--c-dark);margin-bottom:12px;}
.order-success p{color:var(--c-muted);margin-bottom:8px;}
.order-success .order-num{font-size:1.1rem;font-weight:700;color:var(--c-gold);background:#fdf9f3;padding:10px 24px;border-radius:8px;display:inline-block;margin:10px 0 24px;}
.order-success .btn-gold{display:inline-block;padding:12px 32px;background:var(--c-gold);color:#fff;text-decoration:none;border-radius:8px;font-weight:700;}
</style>

<?php if ($orderPlaced): ?>
<div style="background:var(--c-cream);padding:80px 20px;">
  <div class="order-success">
    <div class="check-icon">
      <svg width="32" height="32" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h2>Commande confirmée !</h2>
    <p>Merci pour votre achat. Vous recevrez une confirmation par email.</p>
    <div class="order-num"><?= htmlspecialchars($orderNumber) ?></div>
    <p style="font-size:.85rem;">Nous préparons votre commande avec soin et vous enverrons un email de suivi dès l'expédition.</p>
    <br>
    <a href="/Jolly_Beauty/" class="btn-gold">Continuer mes achats</a>
  </div>
</div>
<?php else: ?>

<div class="checkout-hero">
  <h1>Finaliser la commande</h1>
  <div class="breadcrumb">
    <a href="/Jolly_Beauty/">Accueil</a> → <a href="/Jolly_Beauty/products.php">Boutique</a> → Commande
  </div>
</div>

<div style="background:var(--c-cream);min-height:60vh;">
<?php if (empty($cartItems)): ?>
  <div class="cart-empty-note" style="padding:80px 20px;">
    <p style="font-size:1.1rem;">Votre panier est vide.</p>
    <a href="/Jolly_Beauty/products.php" style="color:var(--c-gold);font-weight:600;">← Retour à la boutique</a>
  </div>
<?php else: ?>
<div class="checkout-wrap">
  <!-- LEFT: FORM -->
  <div>
    <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/Jolly_Beauty/checkout.php">
      <!-- Contact -->
      <div class="checkout-form-card" style="margin-bottom:20px;">
        <div class="checkout-section">
          <h2><span class="step-num">1</span>Informations de contact</h2>
          <div class="form-row">
            <div class="form-group">
              <label>Prénom *</label>
              <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required placeholder="Amina">
            </div>
            <div class="form-group">
              <label>Nom *</label>
              <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required placeholder="Diallo">
            </div>
          </div>
          <div class="form-row" style="margin-top:14px;">
            <div class="form-group">
              <label>Email *</label>
              <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? (currentUser()['email'] ?? '')) ?>" required placeholder="amina@example.com">
            </div>
            <div class="form-group">
              <label>Téléphone</label>
              <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="+33 6 00 00 00 00">
            </div>
          </div>
        </div>
      </div>

      <!-- Shipping -->
      <div class="checkout-form-card" style="margin-bottom:20px;">
        <div class="checkout-section">
          <h2><span class="step-num">2</span>Adresse de livraison</h2>
          <div class="form-row single">
            <div class="form-group">
              <label>Adresse *</label>
              <input type="text" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required placeholder="12 rue de la Paix">
            </div>
          </div>
          <div class="form-row" style="margin-top:14px;">
            <div class="form-group">
              <label>Ville *</label>
              <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required placeholder="Paris">
            </div>
            <div class="form-group">
              <label>Code postal *</label>
              <input type="text" name="zip" value="<?= htmlspecialchars($_POST['zip'] ?? '') ?>" required placeholder="75001">
            </div>
          </div>
          <div class="form-row single" style="margin-top:14px;">
            <div class="form-group">
              <label>Pays</label>
              <select name="country">
                <option value="France" <?= (($_POST['country'] ?? '') === 'France') ? 'selected' : '' ?>>France</option>
                <option value="Belgique" <?= (($_POST['country'] ?? '') === 'Belgique') ? 'selected' : '' ?>>Belgique</option>
                <option value="Suisse" <?= (($_POST['country'] ?? '') === 'Suisse') ? 'selected' : '' ?>>Suisse</option>
                <option value="Canada" <?= (($_POST['country'] ?? '') === 'Canada') ? 'selected' : '' ?>>Canada</option>
                <option value="Sénégal" <?= (($_POST['country'] ?? '') === 'Sénégal') ? 'selected' : '' ?>>Sénégal</option>
                <option value="Maroc" <?= (($_POST['country'] ?? '') === 'Maroc') ? 'selected' : '' ?>>Maroc</option>
              </select>
            </div>
          </div>
        </div>

        <div class="checkout-section">
          <h2><span class="step-num">3</span>Code promo</h2>
          <div class="promo-row">
            <input type="text" name="promo_code" id="promo_code" value="<?= htmlspecialchars($_POST['promo_code'] ?? '') ?>" placeholder="Ex : JOLLY10">
            <button type="button" onclick="applyPromo()">Appliquer</button>
          </div>
          <p id="promo-msg" style="font-size:.82rem;margin-top:8px;color:var(--c-muted);"></p>
        </div>

        <div class="checkout-section">
          <h2><span class="step-num">4</span>Paiement</h2>
          <div class="payment-info">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Paiement sécurisé à la livraison ou par virement. Aucune donnée bancaire requise.
          </div>
          <button type="submit" name="place_order" class="btn-place-order" style="margin-top:20px;">
            Confirmer la commande →
          </button>
          <p style="text-align:center;font-size:.78rem;color:var(--c-muted);margin-top:10px;">
            En passant commande, vous acceptez nos conditions générales de vente.
          </p>
        </div>
      </div>
    </form>
  </div>

  <!-- RIGHT: SUMMARY -->
  <div>
    <div class="order-summary-card">
      <h2>Récapitulatif</h2>
      <div class="summary-items">
        <?php foreach ($cartItems as $item): ?>
        <div class="summary-item">
          <?php if (!empty($item['image'])): ?>
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
          <?php else: ?>
            <div style="width:52px;height:52px;background:#f0ebe3;border-radius:8px;"></div>
          <?php endif; ?>
          <div class="summary-item-info">
            <div class="name"><?= htmlspecialchars($item['name']) ?></div>
            <div class="qty">Qté : <?= (int)($item['qty'] ?? 1) ?></div>
          </div>
          <div class="summary-item-price"><?= formatPrice(($item['price'] ?? 0) * ($item['qty'] ?? 1)) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <hr class="summary-divider">
      <div class="summary-line">
        <span>Sous-total</span>
        <span><?= formatPrice($subtotal) ?></span>
      </div>
      <div class="summary-line">
        <span>Livraison</span>
        <?php if ($shipping == 0): ?>
          <span class="free">Gratuite 🎁</span>
        <?php else: ?>
          <span><?= formatPrice($shipping) ?></span>
        <?php endif; ?>
      </div>
      <hr class="summary-divider">
      <div class="summary-line total">
        <span>Total</span>
        <span><?= formatPrice($total) ?></span>
      </div>
      <?php if ($subtotal < 60): ?>
      <p style="font-size:.75rem;color:var(--c-muted);margin-top:10px;text-align:center;">
        Plus que <?= formatPrice(60 - $subtotal) ?> pour la livraison gratuite !
      </p>
      <?php endif; ?>
      <div class="secure-badges">
        <span><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Paiement sécurisé</span>
        <span><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>Livraison rapide</span>
        <span><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>Retours 14j</span>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<script>
async function applyPromo() {
  const code = document.getElementById('promo_code').value.trim();
  const msg  = document.getElementById('promo-msg');
  if (!code) { msg.textContent = 'Veuillez saisir un code.'; msg.style.color = '#c0392b'; return; }
  msg.textContent = 'Vérification…';
  msg.style.color = 'var(--c-muted)';
  try {
    const res  = await fetch('/Jolly_Beauty/api/check_promo.php?code=' + encodeURIComponent(code));
    const data = await res.json();
    if (data.valid) {
      msg.textContent = '✓ Code valide : ' + data.label;
      msg.style.color = '#2ecc71';
    } else {
      msg.textContent = '✗ Code invalide ou expiré.';
      msg.style.color = '#c0392b';
    }
  } catch { msg.textContent = 'Erreur réseau.'; msg.style.color = '#c0392b'; }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>