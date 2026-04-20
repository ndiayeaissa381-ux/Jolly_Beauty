<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Notre histoire — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/slider/hero-1.jpg';
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';
include __DIR__ . '/../includes/header.php';
?>

<div class="sp-page">
  <section class="sp-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Notre histoire</h1>
      <p>Une aventure née du désir simple de créer un espace de douceur et de soin.</p>
    </div>
  </section>

  <section class="sp-chapter">
    <div class="sp-chapter__inner">
      <div class="sp-chapter__num">01</div>
      <div class="sp-chapter__text">
        <h2>La genèse</h2>
        <p>Jolly Beauty est née d’une intuition : prendre soin de soi peut être un plaisir quotidien, jamais une corvée.</p>
        <p>Nous imaginons des bijoux délicats et des rituels sensoriels pour des instants suspendus, à la maison comme ailleurs.</p>
      </div>
    </div>
  </section>

  <section class="sp-chapter">
    <div class="sp-chapter__inner">
      <div class="sp-chapter__num">02</div>
      <div class="sp-chapter__text">
        <h2>L’inspiration</h2>
        <p>Nos collections s’inspirent des matières nobles, des formes douces et des parfums subtils — toujours avec exigence et respect de la peau.</p>
      </div>
    </div>
  </section>

  <section class="sp-chapter">
    <div class="sp-chapter__inner">
      <div class="sp-chapter__num">03</div>
      <div class="sp-chapter__text">
        <h2>Notre philosophie</h2>
        <p><strong style="color:var(--rose-deep,#b85c6e)">Douceur</strong> dans les textures et les gestes.</p>
        <p><strong style="color:var(--rose-deep,#b85c6e)">Transparence</strong> sur les matériaux et les engagements.</p>
        <p><strong style="color:var(--rose-deep,#b85c6e)">Élégance naturelle</strong>, sans artifice superflu.</p>
      </div>
    </div>
  </section>

  <section class="sp-chapter">
    <div class="sp-chapter__inner">
      <div class="sp-chapter__num">04</div>
      <div class="sp-chapter__text">
        <h2>Nos valeurs</h2>
        <p>Respect de votre peau, qualité des finitions, service attentif — et le plaisir de se faire plaisir.</p>
      </div>
    </div>
  </section>

  <section class="sp-thanks">
    <div class="sp-thanks__inner">
      <h2>Merci d’être là</h2>
      <p>Chaque commande et chaque message nous encouragent à continuer avec la même exigence et la même bienveillance.</p>
      <p class="sig">L’équipe Jolly Beauty</p>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
