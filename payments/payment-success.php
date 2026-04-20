<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/stripe-config.php';
$pageTitle = 'Paiement réussi — Jolly Beauty';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

// Récupérer les paramètres
$sessionId = sanitize($_GET['session_id'] ?? '');
$orderId = (int)($_GET['order_id'] ?? 0);

$orderData = null;
$paymentVerified = false;

if ($sessionId && $orderId) {
    try {
        // Vérifier la session Stripe en utilisant la fonction sécurisée
        $sessionData = verifyStripeSession($sessionId);
        
        // Vérifier que le paiement est réussi
        if ($sessionData['payment_status'] === 'paid' && $sessionData['metadata']['order_id'] == $orderId) {
            $paymentVerified = true;
            
            // Mettre à jour le statut de la commande
            $db = getDB();
            $stmt = $db->prepare('UPDATE orders SET status = \'paid\', stripe_payment_intent_id = ?, paid_at = NOW() WHERE id = ?');
            $stmt->execute([$sessionData['payment_intent'] ?? null, $orderId]);
            
            // Récupérer les données de la commande
            $stmt = $db->prepare('
                SELECT o.*, oi.name as item_name, oi.price, oi.qty 
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                WHERE o.id = ?
            ');
            $stmt->execute([$orderId]);
            $orderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log('Payment verification error: ' . $e->getMessage());
    }
}

// Si non vérifié, récupérer quand même les données de base
if (!$orderData && $orderId) {
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $orderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Order fetch error: ' . $e->getMessage());
    }
}

// Vider le panier si paiement réussi
if ($paymentVerified) {
    $_SESSION['cart'] = [];
    try {
        localStorage.removeItem('jolly_cart');
        sessionStorage.removeItem('jb_checkout_syncing');
    } catch (e) {}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Poppins:wght@300;400;500;600&display=swap">
<link rel="stylesheet" href="<?= $jbBase ?>/assets/css/style.css">
<style>
body{margin:0;background:#F7EFF2;color:var(--c-dark,#2C1A1D);min-height:100vh;font-family:'Poppins',var(--font-sans,sans-serif);}
.success-container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
.success-card{max-width:560px;background:#fff;border-radius:14px;padding:48px 40px;text-align:center;box-shadow:0 10px 30px rgba(44,26,29,.06);border:1px solid rgba(242,167,176,.18);}
.check-icon{width:72px;height:72px;background:#10B981;border-radius:50%;display:grid;place-items:center;margin:0 auto 24px;}
.check-icon svg{width:32px;height:32px;stroke:#fff;stroke-width:2.5;fill:none;}
.success-title{font-family:'Playfair Display',var(--font-serif,serif);font-size:2rem;color:#2C1A1D;margin-bottom:12px;}
.success-message{color:var(--c-muted,#A07880);margin-bottom:8px;}
.order-number{font-size:1.1rem;font-weight:700;color:#10B981;background:#F0FDF4;padding:10px 24px;border-radius:10px;display:inline-block;margin:10px 0 24px;}
.order-details{background:#F9FAFB;border-radius:10px;padding:20px;margin:20px 0;text-align:left;}
.order-details h3{font-family:'Playfair Display',var(--font-serif,serif);font-size:1.1rem;color:#2C1A1D;margin-bottom:15px;}
.order-item{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #E5E7EB;}
.order-item:last-child{border-bottom:none;}
.item-name{font-weight:500;color:#2C1A1D;}
.item-qty{color:var(--c-muted,#A07880);font-size:0.9rem;}
.item-price{font-weight:600;color:#2C1A1D;}
.order-total{display:flex;justify-content:space-between;align-items:center;padding:15px 0;border-top:2px solid #E5E7EB;margin-top:10px;font-size:1.1rem;font-weight:700;color:#2C1A1D;}
.btn-primary{display:inline-block;padding:12px 32px;background:#D4788A;color:#fff;text-decoration:none;border-radius:50px;font-weight:600;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;transition:background .2s;}
.btn-primary:hover{background:#B85C6E;}
.btn-secondary{display:inline-block;padding:10px 24px;background:#F3F4F6;color:#2C1A1D;text-decoration:none;border-radius:50px;font-weight:500;font-size:.78rem;margin-left:10px;transition:background .2s;}
.btn-secondary:hover{background:#E5E7EB;}
.error-card{max-width:560px;background:#FEF2F2;border:1px solid #FECACA;border-radius:14px;padding:48px 40px;text-align:center;}
.error-icon{width:72px;height:72px;background:#EF4444;border-radius:50%;display:grid;place-items:center;margin:0 auto 24px;}
.error-icon svg{width:32px;height:32px;stroke:#fff;stroke-width:2.5;fill:none;}
@media(max-width:600px){.success-card{padding:32px 24px;}.success-title{font-size:1.5rem;}}
</style>
</head>
<body>
<div class="success-container">
<?php if ($paymentVerified && $orderData): ?>
  <div class="success-card">
    <div class="check-icon">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1 class="success-title">Paiement réussi !</h1>
    <p class="success-message">Merci pour votre commande. Vous recevrez une confirmation par email.</p>
    <div class="order-number"><?= htmlspecialchars($orderData[0]['order_ref'] ?? '') ?></div>
    
    <?php if (count($orderData) > 1): ?>
    <div class="order-details">
      <h3>Récapitulatif de votre commande</h3>
      <?php 
      $total = 0;
      foreach ($orderData as $item): 
        if ($item['item_name']): 
          $itemTotal = $item['price'] * $item['qty'];
          $total += $itemTotal;
      ?>
      <div class="order-item">
        <div>
          <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
          <div class="item-qty">Quantité: <?= $item['qty'] ?></div>
        </div>
        <div class="item-price"><?= formatPrice($itemTotal) ?></div>
      </div>
      <?php 
        endif; 
      endforeach; 
      ?>
      <div class="order-total">
        <span>Total payé</span>
        <span><?= formatPrice($total) ?></span>
      </div>
    </div>
    <?php endif; ?>
    
    <p style="font-size:.85rem;color:var(--c-muted,#A07880);margin:20px 0;">
      Nous préparons votre commande avec soin et vous tiendrons informé de sa livraison.
    </p>
    
    <div style="margin-top:30px;">
      <a href="<?= $jbBase ?>/index.php" class="btn-primary">Continuer vos achats</a>
      <a href="<?= $jbBase ?>/contact.php" class="btn-secondary">Contactez-nous</a>
    </div>
  </div>
<?php else: ?>
  <div class="error-card">
    <div class="error-icon">
      <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </div>
    <h1 class="success-title">Erreur de vérification</h1>
    <p class="success-message">Nous n'avons pas pu vérifier votre paiement. Veuillez nous contacter.</p>
    <div style="margin-top:30px;">
      <a href="<?= $jbBase ?>/index.php" class="btn-primary">Retour à l'accueil</a>
      <a href="<?= $jbBase ?>/contact.php" class="btn-secondary">Contactez-nous</a>
    </div>
  </div>
<?php endif; ?>
</div>

<?php if ($paymentVerified): ?>
<script>
try {
    localStorage.removeItem('jolly_cart');
    sessionStorage.removeItem('jb_checkout_syncing');
} catch (e) {}
</script>
<?php endif; ?>
</body>
</html>
