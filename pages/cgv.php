<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'CGV — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/slider/hero-1.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Conditions Générales de Vente</h1>
      <p>Version indicative — à personnaliser selon votre offre commerciale.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>1. Objet</h2>
      <p>Les présentes CGV régissent la vente des produits proposés sur le site <strong>Jolly Beauty</strong>.</p>

      <h2 style="margin-top:18px">2. Prix</h2>
      <p>Les prix sont indiqués en euros TTC, sous réserve d’erreurs manifestes, et peuvent évoluer.</p>

      <h2 style="margin-top:18px">3. Commande &amp; paiement</h2>
      <p>Le paiement est exigible lors de la validation de la commande selon les moyens proposés.</p>

      <h2 style="margin-top:18px">4. Livraison &amp; retours</h2>
      <p>Les principes généraux sont détaillés sur <a href="<?= $jbBase ?>/livraison-retours.php" style="color:var(--rose-deep);font-weight:600">Livraison &amp; retours</a>.</p>

      <h2 style="margin-top:18px">5. Droit de rétractation</h2>
      <p>Le droit légal de rétractation s’applique aux consommateurs, sous conditions, hors exceptions légales (produits personnalisés, scellés, etc.).</p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
