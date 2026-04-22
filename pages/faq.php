<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'FAQ — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/slider/hero-3.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>FAQ</h1>
      <p>Les réponses aux questions les plus fréquentes.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Commande</h2>
      <p><strong>Comment suivre ma commande ?</strong><br>
        Vous recevez un email de confirmation, puis un email d’expédition avec le suivi. Vous pouvez aussi nous écrire via <a href="<?= $jbBase ?>/contact.php" style="color:var(--rose-deep);font-weight:600">Contact</a> avec votre <strong>référence</strong>.</p>

      <h2 style="margin-top:18px">Produits</h2>
      <p><strong>Les bijoux résistent-ils à l’eau ?</strong><br>
        Nous indiquons les caractéristiques sur chaque fiche. En général, on recommande d’éviter l’eau salée / chlore pour préserver l’éclat longtemps.</p>

      <h2 style="margin-top:18px">Retours</h2>
      <p><strong>Comment retourner un article ?</strong><br>
        Les modalités détaillées sont sur <a href="<?= $jbBase ?>/livraison-retours.php" style="color:var(--rose-deep);font-weight:600">Livraison &amp; retours</a>. En résumé : non porté, emballage d’origine, demande auprès du service client.</p>

      <h2 style="margin-top:18px">Délai de réponse</h2>
      <p>Notre équipe répond en général sous <strong>24–48h</strong> (jours ouvrés).</p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
