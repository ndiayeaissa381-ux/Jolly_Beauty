<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Commande — Jolly Beauty';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

// Panier session (rempli par api/cart-sync.php depuis le JS)
$cartItems = [];
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItems = array_values(array_filter($_SESSION['cart'], 'is_array'));
    foreach ($cartItems as $i => $item) {
        if (!isset($item['qty']) && isset($item['quantity'])) {
            $cartItems[$i]['qty'] = (int)$item['quantity'];
        }
        $cartItems[$i]['qty'] = max(1, (int)($cartItems[$i]['qty'] ?? 1));
    }
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
                $q = (int)($item['qty'] ?? 1);
                $subtotal += (float)($item['price'] ?? 0) * $q;
            }

            $discount = 0.0;
            if ($promoCode) {
                $promo = validatePromoCode($promoCode, $subtotal);
                if ($promo) {
                    $discount = $promo['discount_type'] === 'percent'
                        ? $subtotal * ((float)$promo['discount_value'] / 100)
                        : (float)$promo['discount_value'];
                }
            }

            $shipping = $subtotal >= 60 ? 0 : 5.90;
            $total = max(0.0, $subtotal - $discount) + $shipping;

            $orderRef = '#JB' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $userId = isLoggedIn() ? (int)currentUser()['id'] : null;
            $guestName = trim($firstName . ' ' . $lastName);
            $addrLine = $address . ($country ? ' — ' . $country : '');

            $stmt = $db->prepare('
                INSERT INTO orders (order_ref, user_id, guest_email, guest_name, total, status,
                    shipping_name, shipping_addr, shipping_city, shipping_zip, promo_code, discount, notes)
                VALUES (?, ?, ?, ?, ?, \'pending\', ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $orderRef,
                $userId,
                $email,
                $guestName,
                round($total, 2),
                $guestName,
                $addrLine,
                $city,
                $zip,
                $promoCode !== '' ? $promoCode : null,
                round($discount, 2),
                $phone !== '' ? 'Tél: ' . $phone : null,
            ]);
            $orderId = (int)$db->lastInsertId();

            foreach ($cartItems as $item) {
                $pid = isset($item['id']) && is_numeric($item['id']) ? (int)$item['id'] : null;
                $stmtItem = $db->prepare('
                    INSERT INTO order_items (order_id, product_id, name, price, qty)
                    VALUES (?, ?, ?, ?, ?)
                ');
                $stmtItem->execute([
                    $orderId,
                    $pid,
                    (string)($item['name'] ?? ''),
                    round((float)($item['price'] ?? 0), 2),
                    (int)($item['qty'] ?? 1),
                ]);
            }

            $orderNumber = $orderRef;
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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Poppins:wght@300;400;500;600&display=swap">
<link rel="stylesheet" href="<?= $jbBase ?>/assets/css/style.css">
</head>
<body class="checkout-admin-body">

<?php if (!$orderPlaced): ?>
<script>
(function () {
    try {
        if (!<?= empty($cartItems) ? 'true' : 'false' ?>) {
            sessionStorage.removeItem('jb_checkout_syncing');
            return;
        }
        var raw = localStorage.getItem('jolly_cart');
        if (!raw || raw === '[]') return;
        if (sessionStorage.getItem('jb_checkout_syncing') === '1') return;
        sessionStorage.setItem('jb_checkout_syncing', '1');
        fetch(<?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?> + '/api/cart-sync.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: raw
        }).then(function () { window.location.reload(); })
          .catch(function () { sessionStorage.removeItem('jb_checkout_syncing'); });
    } catch (e) {
        sessionStorage.removeItem('jb_checkout_syncing');
    }
})();
</script>
<?php endif; ?>

<style>
.checkout-admin-body{margin:0;background:#F7EFF2;color:var(--c-dark,#2C1A1D);min-height:100vh;}
.co-app{display:flex;min-height:100vh;font-family:'Poppins',var(--font-sans,sans-serif);}
.co-sb{width:248px;flex-shrink:0;background:#2C1A1D;display:flex;flex-direction:column;}
.co-sb-logo{padding:26px 22px 20px;border-bottom:1px solid rgba(255,255,255,.07);}
.co-sb-logo .wm{font-family:'Playfair Display',var(--font-serif,serif);font-style:italic;font-size:1.45rem;color:#fff;}
.co-sb-logo .tg{font-size:.58rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:#F2A7B0;margin-top:2px;}
.co-sb-nav{flex:1;padding:16px 10px;}
.co-sb-nav .ns{font-size:.57rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.28);padding:14px 12px 6px;}
.co-sb-nav a{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:9px;font-size:.78rem;font-weight:500;color:rgba(255,255,255,.65);text-decoration:none;margin-bottom:2px;transition:background .18s,color .18s;}
.co-sb-nav a:hover{background:rgba(255,255,255,.07);color:#fff;}
.co-sb-bot{padding:14px 12px;border-top:1px solid rgba(255,255,255,.07);}
.co-sb-bot a{font-size:.74rem;color:rgba(255,255,255,.45);text-decoration:none;}
.co-sb-bot a:hover{color:#F2A7B0;}
.co-main{flex:1;display:flex;flex-direction:column;min-width:0;}
.co-tb{background:#fff;padding:12px 28px;min-height:62px;display:flex;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid rgba(242,167,176,.25);box-shadow:0 2px 14px rgba(192,92,107,.09);flex-wrap:wrap;}
.co-tb-title{font-family:'Playfair Display',var(--font-serif,serif);font-size:1.25rem;font-weight:500;color:#2C1A1D;margin:0;}
.co-bc{font-size:.75rem;color:var(--c-muted,#A07880);margin-top:4px;}
.co-bc a{color:#D4788A;text-decoration:none;}
.co-bc a:hover{text-decoration:underline;}
.co-tb-actions{display:flex;gap:10px;flex-wrap:wrap;}
.tbtn{display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:50px;font-size:.72rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;border:none;cursor:pointer;font-family:inherit;transition:background .2s;}
.tbtn.t-rose{background:#D4788A;color:#fff;}
.tbtn.t-rose:hover{background:#B85C6E;}
.tbtn.t-ghost{background:#FDE8EC;color:#2C1A1D;}
.tbtn.t-ghost:hover{background:#F8D7DA;}
.co-ct{padding:28px;flex:1;}

.checkout-wrap{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 380px;gap:40px;align-items:start;}
@media(max-width:768px){.checkout-wrap{grid-template-columns:1fr;}}

.checkout-form-card{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(44,26,29,.06);border:1px solid rgba(242,167,176,.18);overflow:hidden;}
.checkout-section{padding:28px 32px;border-bottom:1px solid #f0ebe3;}
.checkout-section:last-child{border-bottom:none;}
.checkout-section h2{font-family:'Playfair Display',var(--font-serif,serif);font-size:1.15rem;color:var(--c-dark,#2C1A1D);margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.checkout-section h2 span.step-num{width:28px;height:28px;background:#D4788A;color:#fff;border-radius:50%;display:grid;place-items:center;font-family:var(--font-sans,sans-serif);font-size:.8rem;font-weight:600;flex-shrink:0;}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.form-row.single{grid-template-columns:1fr;}
@media(max-width:500px){.form-row{grid-template-columns:1fr;}}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group label{font-size:.8rem;font-weight:600;color:var(--c-dark,#2C1A1D);letter-spacing:.04em;text-transform:uppercase;}
.form-group input,.form-group select{padding:11px 14px;border:1.5px solid #F0D8DC;border-radius:10px;font-family:inherit;font-size:.92rem;color:var(--c-dark,#2C1A1D);background:#fafaf8;transition:border-color .2s;}
.form-group input:focus,.form-group select:focus{outline:none;border-color:#D4788A;}

.payment-info{display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fdf9f3;border:1.5px dashed #e8d9b8;border-radius:8px;font-size:.85rem;color:var(--c-muted,#A07880);}
.payment-info svg{flex-shrink:0;color:#D4788A;}

.promo-row{display:flex;gap:10px;}
.promo-row input{flex:1;padding:10px 14px;border:1.5px solid #F0D8DC;border-radius:10px;font-size:.9rem;}
.promo-row input:focus{outline:none;border-color:#D4788A;}
.promo-row button{padding:10px 20px;background:#2C1A1D;color:#fff;border:none;border-radius:50px;font-size:.78rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;white-space:nowrap;transition:background .2s;}
.promo-row button:hover{background:#D4788A;}

.btn-place-order{width:100%;padding:16px;background:#D4788A;color:#fff;border:none;border-radius:50px;font-family:inherit;font-size:.78rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;margin-top:10px;transition:background .3s,transform .2s;}
.btn-place-order:hover{background:#B85C6E;transform:translateY(-1px);}

.error-box{background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:14px 18px;margin-bottom:20px;}
.error-box p{color:#991B1B;font-size:.88rem;margin:.2rem 0;}

.order-summary-card{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(44,26,29,.06);border:1px solid rgba(242,167,176,.18);padding:28px;position:sticky;top:24px;}
.order-summary-card h2{font-family:'Playfair Display',var(--font-serif,serif);font-size:1.1rem;color:var(--c-dark,#2C1A1D);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #f0ebe3;}
.summary-items{display:flex;flex-direction:column;gap:14px;margin-bottom:20px;}
.summary-item{display:flex;align-items:center;gap:12px;}
.summary-item img{width:52px;height:52px;object-fit:cover;border-radius:10px;background:#f5f0ea;}
.summary-item-info{flex:1;}
.summary-item-info .name{font-size:.9rem;font-weight:600;color:var(--c-dark,#2C1A1D);}
.summary-item-info .qty{font-size:.78rem;color:var(--c-muted,#A07880);}
.summary-item-price{font-size:.92rem;font-weight:700;color:var(--c-dark,#2C1A1D);}
.summary-divider{border:none;border-top:1px solid #f0ebe3;margin:14px 0;}
.summary-line{display:flex;justify-content:space-between;align-items:center;font-size:.88rem;color:var(--c-muted,#A07880);margin:.4rem 0;}
.summary-line.total{font-size:1.05rem;font-weight:700;color:var(--c-dark,#2C1A1D);margin-top:10px;}
.summary-line .free{color:#166534;font-weight:600;}
.secure-badges{display:flex;justify-content:center;gap:16px;margin-top:18px;flex-wrap:wrap;}
.secure-badges span{font-size:.72rem;color:var(--c-muted,#A07880);display:flex;align-items:center;gap:5px;}
.cart-empty-note{text-align:center;padding:60px 20px;color:var(--c-muted,#A07880);max-width:480px;margin:0 auto;}
.cart-empty-note a{color:#D4788A;font-weight:600;}

.order-success-card{max-width:560px;margin:0 auto;background:#fff;border-radius:14px;padding:48px 40px;text-align:center;box-shadow:0 10px 30px rgba(44,26,29,.06);border:1px solid rgba(242,167,176,.18);}
.order-success .check-icon{width:72px;height:72px;background:#D4788A;border-radius:50%;display:grid;place-items:center;margin:0 auto 24px;}
.order-success h2{font-family:'Playfair Display',var(--font-serif,serif);font-size:2rem;color:var(--c-dark,#2C1A1D);margin-bottom:12px;}
.order-success p{color:var(--c-muted,#A07880);margin-bottom:8px;}
.order-success .order-num{font-size:1.1rem;font-weight:700;color:#B85C6E;background:#FDF4F6;padding:10px 24px;border-radius:10px;display:inline-block;margin:10px 0 24px;}
.order-success .btn-gold{display:inline-block;padding:12px 32px;background:#D4788A;color:#fff;text-decoration:none;border-radius:50px;font-weight:600;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;}
.order-success .btn-gold:hover{background:#B85C6E;}

@media(max-width:960px){
  .co-app{flex-direction:column;}
  .co-sb{width:100%;flex-direction:row;flex-wrap:wrap;align-items:center;}
  .co-sb-logo{flex:1;min-width:200px;border-bottom:none;border-right:1px solid rgba(255,255,255,.07);}
  .co-sb-nav{display:flex;flex-wrap:wrap;flex:2;padding:12px;}
  .co-sb-nav .ns{width:100%;}
  .co-sb-bot{width:100%;text-align:center;}
}
</style>

<div class="co-app">
  <aside class="co-sb">
    <div class="co-sb-logo">
      <div class="wm">Jolly Beauty</div>
      <div class="tg">Paiement &amp; livraison</div>
    </div>
    <nav class="co-sb-nav">
      <div class="ns">Navigation</div>
      <a href="<?= $jbBase ?>/pages/index.php">Accueil</a>
      <a href="<?= $jbBase ?>/bijoux.php">Bijoux</a>
      <a href="<?= $jbBase ?>/soins-rituels.php">Soins &amp; Rituels</a>
      <a href="<?= $jbBase ?>/coffrets.php">Coffrets</a>
      <a href="<?= $jbBase ?>/pages/category.php?c=all">Toute la collection</a>
    </nav>
    <div class="co-sb-bot">
      <a href="<?= $jbBase ?>/login.php">Mon compte</a>
    </div>
  </aside>

  <main class="co-main">
    <header class="co-tb">
      <div>
        <h1 class="co-tb-title"><?= $orderPlaced ? 'Commande confirmée' : 'Finaliser la commande' ?></h1>
        <div class="co-bc">
          <a href="<?= $jbBase ?>/pages/index.php">Accueil</a>
          <?php if (!$orderPlaced): ?> → <a href="<?= $jbBase ?>/pages/category.php?c=all">Collection</a> → <span>Commande</span><?php endif; ?>
        </div>
      </div>
      <div class="co-tb-actions">
        <a class="tbtn t-ghost" href="<?= $jbBase ?>/pages/index.php">← Boutique</a>
        <?php if (!$orderPlaced && !empty($cartItems)): ?>
        <a class="tbtn t-rose" href="<?= $jbBase ?>/pages/index.php">Continuer les achats</a>
        <?php endif; ?>
      </div>
    </header>

    <div class="co-ct">
<?php if ($orderPlaced): ?>
      <div class="order-success">
        <div class="order-success-card">
          <div class="check-icon">
            <svg width="32" height="32" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <h2>Merci !</h2>
          <p>Vous recevrez une confirmation par email.</p>
          <div class="order-num"><?= htmlspecialchars($orderNumber) ?></div>
          <p style="font-size:.85rem;">Nous préparons votre commande avec soin.</p>
          <br>
          <a href="<?= $jbBase ?>/pages/index.php" class="btn-gold">Continuer mes achats</a>
        </div>
      </div>
<?php else: ?>
<?php if (empty($cartItems)): ?>
      <div class="cart-empty-note">
        <p style="font-size:1.1rem;color:var(--c-dark,#2C1A1D);">Votre panier est vide.</p>
        <p><a href="<?= $jbBase ?>/pages/category.php?c=all">← Retour à la collection</a></p>
      </div>
<?php else: ?>
      <div class="checkout-wrap">
        <div>
          <?php if (!empty($errors)): ?>
          <div class="error-box">
            <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="<?= $jbBase ?>/pages/checkout.php">
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
                <p id="promo-msg" style="font-size:.82rem;margin-top:8px;color:var(--c-muted,#A07880);"></p>
              </div>

              <div class="checkout-section">
                <h2><span class="step-num">4</span>Paiement</h2>
                
                <!-- Options de paiement -->
                <div class="payment-methods" style="margin-bottom:20px;">
                  <div class="payment-option" data-method="stripe" style="margin-bottom:15px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:15px;background:#FDF9F3;border:2px solid #E8D9B8;border-radius:10px;transition:border-color .2s;">
                      <input type="radio" name="payment_method" value="stripe" checked style="margin:0;">
                      <div style="flex:1;">
                        <div style="font-weight:600;color:#2C1A1D;margin-bottom:5px;">Paiement en ligne sécurisé</div>
                        <div style="font-size:.85rem;color:var(--c-muted,#A07880);">Carte, Apple Pay, Google Pay, PayPal</div>
                      </div>
                      <div style="display:flex;gap:8px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#000"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#4285F4"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#003087"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 2.419A.641.641 0 0 1 5.577 2h4.605c2.606 0 4.706 1.635 4.706 4.901 0 3.325-1.91 4.901-4.681 4.901H8.058l-.8 4.682a.641.641 0 0 1-.633.537h-1.95a.641.641 0 0 1-.633-.74l1.034-5.953zm4.706-11.436c1.421 0 2.37-.822 2.37-2.37 0-1.485-.949-2.37-2.37-2.37H9.271l-.8 4.74h2.311zm4.515 11.436h-1.95a.641.641 0 0 1-.633-.74l1.034-5.953h1.95a.641.641 0 0 1 .633.74l-1.034 5.953a.641.641 0 0 1-.633.537zm2.749-11.436h1.95a.641.641 0 0 1 .633.74l-1.034 5.953h-1.95a.641.641 0 0 1-.633-.74l1.034-5.953a.641.641 0 0 1 .633-.537z"/></svg>
                      </div>
                    </label>
                  </div>
                  
                  <div class="payment-option" data-method="offline" style="margin-bottom:15px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:15px;background:#FAFAFA;border:2px solid #E5E7EB;border-radius:10px;transition:border-color .2s;">
                      <input type="radio" name="payment_method" value="offline" style="margin:0;">
                      <div style="flex:1;">
                        <div style="font-weight:600;color:#2C1A1D;margin-bottom:5px;">Paiement à la livraison</div>
                        <div style="font-size:.85rem;color:var(--c-muted,#A07880);">Paiement par virement ou à la réception</div>
                      </div>
                    </label>
                  </div>
                </div>

                <div class="payment-info">
                  <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                  Paiement 100% sécurisé avec cryptage SSL
                </div>
                
                <div id="stripe-payment-section" style="margin-top:20px;">
                  <button type="button" id="stripe-checkout-btn" class="btn-place-order" style="background:#635BFF;">
                    Payer avec Stripe →
                  </button>
                </div>
                
                <div id="offline-payment-section" style="display:none;margin-top:20px;">
                  <button type="submit" name="place_order" class="btn-place-order">
                    Confirmer la commande →
                  </button>
                </div>
                
                <p style="text-align:center;font-size:.78rem;color:var(--c-muted,#A07880);margin-top:15px;">
                  En passant commande, vous acceptez nos conditions générales de vente.
                </p>
              </div>
            </div>
          </form>
        </div>

        <div>
          <div class="order-summary-card">
            <h2>Récapitulatif</h2>
            <div class="summary-items">
              <?php foreach ($cartItems as $item):
                  $imgUrl = jb_cart_item_image_url($item);
                  ?>
              <div class="summary-item">
                <?php if ($imgUrl !== ''): ?>
                  <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>">
                <?php else: ?>
                  <div style="width:52px;height:52px;background:#f0ebe3;border-radius:10px;"></div>
                <?php endif; ?>
                <div class="summary-item-info">
                  <div class="name"><?= htmlspecialchars($item['name'] ?? '') ?></div>
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
                <span class="free">Gratuite</span>
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
            <p style="font-size:.75rem;color:var(--c-muted,#A07880);margin-top:10px;text-align:center;">
              Plus que <?= formatPrice(60 - $subtotal) ?> pour la livraison gratuite !
            </p>
            <?php endif; ?>
            <div class="secure-badges">
              <span><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Paiement sécurisé</span>
              <span><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>Livraison rapide</span>
            </div>
          </div>
        </div>
      </div>
<?php endif; ?>
<?php endif; ?>
    </div>
  </main>
</div>

<?php if ($orderPlaced): ?>
<script>
try {
    localStorage.removeItem('jolly_cart');
    sessionStorage.removeItem('jb_checkout_syncing');
} catch (e) {}
</script>
<?php endif; ?>

<script>
// Gestion des options de paiement
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const stripeSection = document.getElementById('stripe-payment-section');
    const offlineSection = document.getElementById('offline-payment-section');
    
    if (this.value === 'stripe') {
      stripeSection.style.display = 'block';
      offlineSection.style.display = 'none';
    } else {
      stripeSection.style.display = 'none';
      offlineSection.style.display = 'block';
    }
  });
});

// Gestion du paiement Stripe
document.getElementById('stripe-checkout-btn').addEventListener('click', async function() {
  const form = document.querySelector('form');
  const formData = new FormData(form);
  
  // Validation des champs requis
  const requiredFields = ['first_name', 'last_name', 'email', 'address', 'city', 'zip'];
  const missingFields = [];
  
  for (const field of requiredFields) {
    const value = formData.get(field);
    if (!value || value.trim() === '') {
      missingFields.push(field);
    }
  }
  
  if (missingFields.length > 0) {
    alert('Veuillez remplir tous les champs obligatoires avant de procéder au paiement.');
    return;
  }
  
  // Email validation
  const email = formData.get('email');
  if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
    alert('Veuillez saisir une adresse email valide.');
    return;
  }
  
  // Désactiver le bouton pendant le traitement
  this.disabled = true;
  this.textContent = 'Traitement en cours...';
  
  try {
    // Préparer les données pour l'API
    const data = {
      first_name: formData.get('first_name'),
      last_name: formData.get('last_name'),
      email: email,
      phone: formData.get('phone') || '',
      address: formData.get('address'),
      city: formData.get('city'),
      zip: formData.get('zip'),
      country: formData.get('country') || 'France',
      promo_code: formData.get('promo_code') || ''
    };
    
    const response = await fetch('<?= $jbBase ?>/api/stripe-create-session.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Rediriger vers Stripe Checkout
      window.location.href = result.checkout_url;
    } else {
      throw new Error(result.error || 'Erreur lors de la création de la session de paiement');
    }
    
  } catch (error) {
    console.error('Stripe payment error:', error);
    alert('Une erreur est survenue lors de la préparation du paiement. Veuillez réessayer. ' + error.message);
  } finally {
    // Réactiver le bouton
    this.disabled = false;
    this.textContent = 'Payer avec Stripe →';
  }
});

async function applyPromo() {
  const code = document.getElementById('promo_code').value.trim();
  const msg  = document.getElementById('promo-msg');
  if (!code) { msg.textContent = 'Veuillez saisir un code.'; msg.style.color = '#c0392b'; return; }
  msg.textContent = 'Vérification…';
  msg.style.color = 'var(--c-muted,#A07880)';
  try {
    const res  = await fetch(<?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?> + '/api/check_promo.php?code=' + encodeURIComponent(code));
    const data = await res.json();
    if (data.valid) {
      msg.textContent = '✓ Code valide : ' + data.label;
      msg.style.color = '#166534';
    } else {
      msg.textContent = '✗ Code invalide ou expiré.';
      msg.style.color = '#c0392b';
    }
  } catch (e) { msg.textContent = 'Erreur réseau.'; msg.style.color = '#c0392b'; }
}
</script>
</body>
</html>
