<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Paiement annulé — Jolly Beauty';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

$orderId = (int)($_GET['order_id'] ?? 0);
$orderData = null;

if ($orderId) {
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Mettre à jour le statut de la commande
        if ($orderData && $orderData['status'] === 'pending_payment') {
            $stmt = $db->prepare('UPDATE orders SET status = \'cancelled\', notes = CONCAT(IFNULL(notes, \'\'), \' | Paiement annulé par l\'utilisateur\') WHERE id = ?');
            $stmt->execute([$orderId]);
        }
    } catch (Exception $e) {
        error_log('Payment cancellation error: ' . $e->getMessage());
    }
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
.cancel-container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
.cancel-card{max-width:560px;background:#fff;border-radius:14px;padding:48px 40px;text-align:center;box-shadow:0 10px 30px rgba(44,26,29,.06);border:1px solid rgba(242,167,176,.18);}
.cancel-icon{width:72px;height:72px;background:#F59E0B;border-radius:50%;display:grid;place-items:center;margin:0 auto 24px;}
.cancel-icon svg{width:32px;height:32px;stroke:#fff;stroke-width:2.5;fill:none;}
.cancel-title{font-family:'Playfair Display',var(--font-serif,serif);font-size:2rem;color:#2C1A1D;margin-bottom:12px;}
.cancel-message{color:var(--c-muted,#A07880);margin-bottom:20px;line-height:1.6;}
.order-info{background:#FEF3C7;border:1px solid #FCD34D;border-radius:10px;padding:20px;margin:20px 0;}
.order-info h3{font-family:'Playfair Display',var(--font-serif,serif);font-size:1.1rem;color:#92400E;margin-bottom:10px;}
.order-number{font-weight:700;color:#92400E;}
.btn-primary{display:inline-block;padding:12px 32px;background:#D4788A;color:#fff;text-decoration:none;border-radius:50px;font-weight:600;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;transition:background .2s;}
.btn-primary:hover{background:#B85C6E;}
.btn-secondary{display:inline-block;padding:10px 24px;background:#F3F4F6;color:#2C1A1D;text-decoration:none;border-radius:50px;font-weight:500;font-size:.78rem;margin-left:10px;transition:background .2s;}
.btn-secondary:hover{background:#E5E7EB;}
.btn-retour{display:inline-block;padding:12px 32px;background:#F59E0B;color:#fff;text-decoration:none;border-radius:50px;font-weight:600;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;transition:background .2s;margin-top:20px;}
.btn-retour:hover{background:#D97706;}
.reasons-list{text-align:left;background:#F9FAFB;border-radius:10px;padding:20px;margin:20px 0;}
.reasons-list h4{font-family:'Playfair Display',var(--font-serif,serif);font-size:1rem;color:#2C1A1D;margin-bottom:15px;}
.reasons-list ul{list-style:none;padding:0;margin:0;}
.reasons-list li{padding:8px 0;color:var(--c-muted,#A07880);position:relative;padding-left:25px;}
.reasons-list li:before{content:"•";color:#F59E0B;font-weight:bold;position:absolute;left:0;font-size:1.2rem;}
@media(max-width:600px){.cancel-card{padding:32px 24px;}.cancel-title{font-size:1.5rem;}}
</style>
</head>
<body>
<div class="cancel-container">
  <div class="cancel-card">
    <div class="cancel-icon">
      <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <h1 class="cancel-title">Paiement annulé</h1>
    <p class="cancel-message">
      Votre paiement a été annulé. Aucun montant n'a été débité de votre compte.
    </p>
    
    <?php if ($orderData): ?>
    <div class="order-info">
      <h3>Référence de commande</h3>
      <div class="order-number"><?= htmlspecialchars($orderData['order_ref']) ?></div>
      <p style="margin-top:10px;font-size:.9rem;color:#92400E;">
        Votre commande a été sauvegardée. Vous pouvez la compléter ultérieurement.
      </p>
    </div>
    <?php endif; ?>
    
    <div class="reasons-list">
      <h4>Pourquoi annuler ?</h4>
      <ul>
        <li>Vous souhaitez modifier votre adresse de livraison</li>
        <li>Vous voulez ajouter ou supprimer des produits</li>
        <li>Vous préférez utiliser un autre moyen de paiement</li>
        <li>Vous avez besoin de plus de temps pour finaliser votre achat</li>
      </ul>
    </div>
    
    <p style="font-size:.85rem;color:var(--c-muted,#A07880);margin:20px 0;">
      Votre panier est toujours disponible. Vous pouvez reprendre votre commande quand vous le souhaitez.
    </p>
    
    <div style="margin-top:30px;">
      <a href="<?= $jbBase ?>/checkout.php" class="btn-retour">→ Retour à la commande</a>
    </div>
    
    <div style="margin-top:20px;">
      <a href="<?= $jbBase ?>/index.php" class="btn-primary">Continuer vos achats</a>
      <a href="<?= $jbBase ?>/contact.php" class="btn-secondary">Besoin d'aide ?</a>
    </div>
  </div>
</div>
</body>
</html>
