<?php
/** @var string $jbBase */
$imgSoin = $jbBase . '/assets/images/soins/soin-visage-1.jpg';
$imgRitual = $jbBase . '/assets/images/produits/ritual-beaute-1.jpg';
$imgVanille = $jbBase . '/assets/images/soins/soin-corps-1.jpg';
$imgRose = $jbBase . '/assets/images/soins/soin-visage-2.jpg';
$imgKarite = $jbBase . '/assets/images/soins/creme-visage.jpg';
$imgArgan = $jbBase . '/assets/images/soins/soin-corps-2.jpg';
?>
<section class="jb-block jb-block--white">
  <div class="jb-two-col" style="padding:0 5%;">
    <div class="jb-prose">
      <h2 style="text-align:left;margin-bottom:20px;">Notre philosophie</h2>
      <p>Chez Jolly Beauty, la beauté commence par le bien-être. Nos soins et rituels transforment votre routine en un moment privilégié.</p>
      <p>Des ingrédients choisis pour leurs bienfaits et leur sensorialité, dans le respect de votre peau.</p>
    </div>
    <div>
      <img src="<?= htmlspecialchars($imgSoin) ?>" alt="Soins Jolly Beauty" loading="lazy" width="600" height="400">
    </div>
  </div>
</section>

<section class="jb-block jb-block--alt">
  <div style="max-width:960px;margin:0 auto;padding:0 5%;">
    <h2>Rituel du moment</h2>
    <div class="jb-two-col" style="background:var(--jb-white);padding:32px;border-radius:var(--jb-radius);box-shadow:var(--jb-shadow);">
      <div><img src="<?= htmlspecialchars($imgRitual) ?>" alt="Rituel beauté" loading="lazy" style="border-radius:12px;width:100%;max-height:280px;object-fit:cover;"></div>
      <div class="jb-prose">
        <h3 style="font-family:var(--font-serif,'Playfair Display',serif);font-size:1.35rem;margin-bottom:12px;">Rituel douceur</h3>
        <p>Gestes doux et textures sensorielles pour une pause respirable.</p>
        <ol style="margin:16px 0 0 18px;line-height:1.8;color:var(--jb-text-light);font-size:0.9rem;">
          <li>Gommage délicat</li>
          <li>Masque hydratant</li>
          <li>Soin nourrissant</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="jb-block jb-block--white">
  <h2>Ingrédients d’exception</h2>
  <div class="jb-size-grid" style="max-width:1000px;">
    <div class="jb-size-card" style="padding:0;overflow:hidden;">
      <img src="<?= htmlspecialchars($imgVanille) ?>" alt="" style="height:180px;width:100%;object-fit:cover;">
      <div style="padding:22px;"><h3 style="font-family:var(--font-serif,'Playfair Display',serif);font-size:1.1rem;margin-bottom:8px;">Notes vanillées</h3><p style="font-size:0.85rem;color:var(--jb-text-light);line-height:1.55;">Douceur enveloppante pour la peau.</p></div>
    </div>
    <div class="jb-size-card" style="padding:0;overflow:hidden;">
      <img src="<?= htmlspecialchars($imgRose) ?>" alt="" style="height:180px;width:100%;object-fit:cover;">
      <div style="padding:22px;"><h3 style="font-family:var(--font-serif,'Playfair Display',serif);font-size:1.1rem;margin-bottom:8px;">Rose &amp; délicatesse</h3><p style="font-size:0.85rem;color:var(--jb-text-light);line-height:1.55;">Apaisement et éclat.</p></div>
    </div>
    <div class="jb-size-card" style="padding:0;overflow:hidden;">
      <img src="<?= htmlspecialchars($imgKarite) ?>" alt="" style="height:180px;width:100%;object-fit:cover;">
      <div style="padding:22px;"><h3 style="font-family:var(--font-serif,'Playfair Display',serif);font-size:1.1rem;margin-bottom:8px;">Textures riches</h3><p style="font-size:0.85rem;color:var(--jb-text-light);line-height:1.55;">Nutrition et confort.</p></div>
    </div>
    <div class="jb-size-card" style="padding:0;overflow:hidden;">
      <img src="<?= htmlspecialchars($imgArgan) ?>" alt="" style="height:180px;width:100%;object-fit:cover;">
      <div style="padding:22px;"><h3 style="font-family:var(--font-serif,'Playfair Display',serif);font-size:1.1rem;margin-bottom:8px;">Sublimation</h3><p style="font-size:0.85rem;color:var(--jb-text-light);line-height:1.55;">Pour une peau souple et lumineuse.</p></div>
    </div>
  </div>
</section>
