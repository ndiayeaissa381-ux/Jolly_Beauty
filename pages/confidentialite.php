<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Confidentialité — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/slider/hero-2.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Politique de confidentialité</h1>
      <p>Transparence sur les données, dans le respect du RGPD.</p>
    </div>
  </section>

  <div class="sp-form-wrap" style="margin-top:22px">
    <div class="sp-form-card">
      <h2>Données collectées</h2>
      <p>Exemples : identité, coordonnées, historique de commande, messages envoyés via le formulaire de contact, inscription newsletter.</p>

      <h2 style="margin-top:18px">Finalités</h2>
      <ul style="padding-left:18px;line-height:1.85;color:var(--text)">
        <li>traiter et livrer vos commandes</li>
        <li>répondre à vos demandes</li>
        <li>améliorer le service (sous réserve de base légale)</li>
      </ul>

      <h2 style="margin-top:18px">Vos droits</h2>
      <p>Vous pouvez demander l’accès, la rectification, l’effacement, la limitation, la portabilité, et retirer un consentement lorsque applicable.</p>

      <h2 style="margin-top:18px">Contact</h2>
      <p>Écrivez-nous : <a href="mailto:contact@jollybeauty.fr" style="color:var(--rose-deep);font-weight:600">contact@jollybeauty.fr</a> — merci d’indiquer l’objet (ex. “RGPD”).</p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
