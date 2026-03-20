<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Jolly Beauty — La beauté des moments doux';
$products  = getFeaturedProducts();
include __DIR__ . '/includes/header.php';
?>

<?php
// Récupère toutes les images disponibles pour le slider
$imgDir  = __DIR__ . '/images/';
$allImgs = is_dir($imgDir)
  ? array_values(array_filter(scandir($imgDir), fn($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)))
  : [];

// Logo = 1ère image, slides = toutes les autres (ou fallback sur la 1ère si pas d'autres)
$logoImg   = !empty($allImgs) ? '/Jolly_Beauty/images/' . $allImgs[0] : null;
$slideImgs = count($allImgs) > 1 ? array_slice($allImgs, 1) : $allImgs;
?>

<!-- HERO -->
<section class="hero">
  <span class="hero-sparkle">✦</span>
  <span class="hero-sparkle">✦</span>
  <span class="hero-sparkle">✦</span>

  <!-- GAUCHE : Slider d'images + logo en overlay -->
  <div class="hero__left hero__left--slider">

    <!-- SLIDER -->
    <div class="hero-slider" id="hero-slider">
      <?php if (!empty($slideImgs)): ?>
        <?php foreach($slideImgs as $i => $img): ?>
        <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>">
          <img src="/Jolly_Beauty/images/<?= htmlspecialchars($img) ?>" alt="Jolly Beauty">
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="hero-slide active">
          <div style="width:100%;height:100%;background:linear-gradient(160deg,var(--blush),var(--rose-pale));display:flex;align-items:center;justify-content:center;font-size:8rem;opacity:.25">🌸</div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Overlay dégradé gauche -->
    <div class="hero-slider-overlay"></div>

    <!-- LOGO en bas à gauche -->
    <?php if ($logoImg): ?>
    <div class="hero-slider-logo">
      <img src="<?= htmlspecialchars($logoImg) ?>" alt="Jolly Beauty Logo">
    </div>
    <?php else: ?>
    <div class="hero-slider-logo hero-slider-logo--text">
      Jolly <em>Beauty</em>
    </div>
    <?php endif; ?>

    <!-- Flèches de navigation -->
    <?php if (count($slideImgs) > 1): ?>
    <button class="hero-arrow hero-arrow--prev" onclick="goToSlide(window._heroSlide - 1)" aria-label="Image précédente">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="hero-arrow hero-arrow--next" onclick="goToSlide(window._heroSlide + 1)" aria-label="Image suivante">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 6 15 12 9 18"/></svg>
    </button>
    <?php endif; ?>

    <!-- Indicateurs de slides -->
    <?php if (count($slideImgs) > 1): ?>
    <div class="hero-slider-dots" id="hero-dots">
      <?php foreach($slideImgs as $i => $img): ?>
      <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $i ?>)" aria-label="Slide <?= $i+1 ?>"></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- DROITE : Texte + CTA -->
  <div class="hero__right hero__right--content">
    <div class="hero__eyebrow">Nouveauté 2026</div>
    <h1 class="hero__title">
      Rituels de douceur<em>pour la peau</em>
    </h1>
    <p class="hero__sub">Des textures gourmandes et des parfums délicats pour transformer chaque soin en rituel de bien-être.</p>
    <div class="hero__actions">
      <a href="/Jolly_Beauty/products.php" class="btn btn--dark">Découvrir la collection</a>
      <a href="/Jolly_Beauty/products.php?category=coffrets" class="btn btn--outline">Coffrets Cadeaux</a>
    </div>

    <!-- Badge produit flottant -->
    <div class="hero-badge">
      <div class="hero-badge__brand">Jolly Beauty</div>
      <div class="hero-badge__name">Beurre de Karité Fouetté</div>
      <div class="hero-badge__sub">nourrit &amp; adoucit la peau</div>
    </div>
  </div>
</section>

<style>
/* ── HERO SLIDER ─────────────────────────────────────────── */
.hero {
  grid-template-columns: 1.1fr 1fr !important;
}

/* Côté gauche devient le slider */
.hero__left--slider {
  position: relative;
  padding: 0 !important;
  overflow: hidden;
  min-height: calc(100vh - var(--nav-h) - 38px);
}
@media(max-width:768px){
  .hero__left--slider { min-height: 65vw; order: 1; }
}

/* Slider container */
.hero-slider {
  position: absolute; inset: 0;
  width: 100%; height: 100%;
}
.hero-slide {
  position: absolute; inset: 0;
  opacity: 0; transition: opacity .9s ease;
}
.hero-slide.active { opacity: 1; }
.hero-slide img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center;
  display: block;
}

/* Overlay dégradé droit pour fondre avec le contenu */
.hero-slider-overlay {
  position: absolute; inset: 0; z-index: 1;
  background:
    linear-gradient(to right, transparent 55%, var(--rose-pale) 100%),
    linear-gradient(to bottom, transparent 60%, rgba(44,26,29,.35) 100%);
  pointer-events: none;
}

/* Logo en bas à gauche */
.hero-slider-logo {
  position: absolute; bottom: 32px; left: 28px; z-index: 3;
  animation: badgeFloat 5s ease-in-out infinite;
}
.hero-slider-logo img {
  height: 64px; width: auto;
  object-fit: contain;
  filter: drop-shadow(0 4px 16px rgba(44,26,29,.35));
  background: rgba(255,255,255,.82);
  padding: 8px 14px;
  border-radius: 12px;
  backdrop-filter: blur(8px);
}
.hero-slider-logo--text {
  font-family: var(--font-serif);
  font-size: 2rem; font-style: italic;
  color: #fff;
  text-shadow: 0 2px 12px rgba(44,26,29,.4);
  background: rgba(255,255,255,.15);
  padding: 10px 20px;
  border-radius: 12px;
  backdrop-filter: blur(8px);
}
.hero-slider-logo--text em { color: var(--rose-l); font-style: normal; }

/* Flèches */
.hero-arrow {
  position: absolute; top: 50%; z-index: 4;
  transform: translateY(-50%);
  width: 44px; height: 44px;
  background: rgba(255,255,255,.82);
  backdrop-filter: blur(8px);
  border: none; border-radius: 50%; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: var(--dark);
  box-shadow: 0 4px 18px rgba(44,26,29,.18);
  transition: background .25s, transform .25s, box-shadow .25s;
  opacity: 0;
  transition: opacity .3s, background .25s, transform .25s;
}
.hero__left--slider:hover .hero-arrow { opacity: 1; }
.hero-arrow:hover {
  background: var(--rose-deep);
  color: #fff;
  transform: translateY(-50%) scale(1.08);
  box-shadow: 0 6px 24px rgba(212,120,138,.35);
}
.hero-arrow--prev { left: 16px; }
.hero-arrow--next { right: 16px; }
@media(max-width:768px){
  .hero-arrow { opacity: 1; width: 36px; height: 36px; }
  .hero-arrow--prev { left: 10px; }
  .hero-arrow--next { right: 10px; }
}

/* Points de navigation */
.hero-slider-dots {
  position: absolute; bottom: 28px; left: 50%; z-index: 3;
  transform: translateX(-50%);
  display: flex; gap: 8px; align-items: center;
}
.hero-dot {
  width: 7px; height: 7px; border-radius: 50%;
  border: none; cursor: pointer; padding: 0;
  background: rgba(255,255,255,.45);
  transition: all .3s;
}
.hero-dot.active {
  background: #fff;
  width: 22px; border-radius: 4px;
}

/* Côté droit = contenu texte */
.hero__right--content {
  display: flex !important;
  flex-direction: column;
  justify-content: center;
  padding: 80px 8% 80px 6% !important;
  position: relative; z-index: 2;
  background: var(--rose-pale);
  overflow: visible !important;
}
.hero__right--content::before { display: none !important; }
.hero__right--content img { display: none; }

/* Repositionne le badge */
.hero__right--content .hero-badge {
  position: relative !important;
  bottom: auto !important; left: auto !important;
  margin-top: 32px;
  animation: none;
}

@media(max-width:768px){
  .hero { grid-template-columns: 1fr !important; }
  .hero__left--slider { min-height: 70vw; order: 1; }
  .hero__right--content { order: 2; padding: 44px 6% 36px !important; }
  .hero-slider-logo img { height: 48px; }
}
</style>

<script>
// ── HERO SLIDER JS ────────────────────────────────────────
(function() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots   = document.querySelectorAll('.hero-dot');
  let current  = 0;
  let timer;

  function goToSlide(n) {
    slides[current].classList.remove('active');
    if (dots[current]) dots[current].classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('active');
    if (dots[current]) dots[current].classList.add('active');
    window._heroSlide = current;
    // Reset autoplay
    clearInterval(timer);
    if (slides.length > 1) timer = setInterval(() => goToSlide(current + 1), 4500);
  }

  window.goToSlide = goToSlide;
  window._heroSlide = 0;

  function autoplay() {
    timer = setInterval(() => goToSlide(current + 1), 4500);
  }

  // Pause au survol
  const slider = document.getElementById('hero-slider');
  if (slider) {
    slider.addEventListener('mouseenter', () => clearInterval(timer));
    slider.addEventListener('mouseleave', () => { if(slides.length > 1) autoplay(); });

    // Swipe tactile
    let touchX = 0;
    slider.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, {passive:true});
    slider.addEventListener('touchend',   e => {
      const diff = touchX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) goToSlide(diff > 0 ? current + 1 : current - 1);
    });
  }

  if (slides.length > 1) autoplay();
})();
</script>

<!-- BESTSELLERS -->
<section class="bestsellers" id="bestsellers">
  <div class="section-head" data-reveal>
    <div>
      <h2 class="section-title">Nos <em>meilleures ventes</em></h2>
      <p class="section-sub">Les produits préférés de notre communauté</p>
    </div>
  </div>

  <div class="products-grid">
    <?php
    $fallback = [
      ['name'=>'Beurre de Karité Fouetté','cat'=>'Soins','price'=>'34,90 €','badge'=>'Best-seller'],
      ['name'=>'Huile Précieuse Éclat','cat'=>'Soins','price'=>'29,90 €','badge'=>'Nouveau'],
      ['name'=>'Eau de Soin Florale','cat'=>'Rituels','price'=>'24,90 €','badge'=>''],
      ['name'=>'Coffret Rituel Douceur','cat'=>'Coffrets','price'=>'59,90 €','badge'=>'Cadeau idéal'],
    ];
    $imgDir = __DIR__ . '/images/';
    $allImgs = is_dir($imgDir) ? array_values(array_filter(scandir($imgDir), fn($f) => preg_match('/\.(jpg|jpeg|png|webp)$/i', $f))) : [];

    if (!empty($products)):
      foreach($products as $i => $p):
        $img = !empty($p['image']) ? '/Jolly_Beauty/images/' . basename($p['image']) : ($allImgs[$i+2] ?? null);
    ?>
    <div class="product-card" data-reveal data-reveal-delay="<?= $i % 4 ?>">
      <div class="product-card__img-wrap">
        <?php if ($img): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3">🌸</div><?php endif; ?>
        <?php if (!empty($p['badge'])): ?><span class="product-card__badge"><?= sanitize($p['badge']) ?></span><?php endif; ?>
        <div class="product-card__actions">
          <button class="card-action-btn" title="Aperçu" onclick="window.location='/Jolly_Beauty/product.php?slug=<?= urlencode($p['slug']) ?>'">👁</button>
          <button class="card-action-btn" title="Favoris">♡</button>
        </div>
      </div>
      <div class="product-card__body">
        <div class="product-card__cat"><?= sanitize($p['category'] ?? '') ?></div>
        <a href="/Jolly_Beauty/product.php?slug=<?= urlencode($p['slug']) ?>"><div class="product-card__name"><?= sanitize($p['name']) ?></div></a>
        <div class="product-card__foot">
          <div class="product-card__price"><?= formatPrice($p['price']) ?></div>
          <button class="product-card__add" onclick="addToCart({id:'<?= $p['id'] ?>',name:'<?= addslashes(sanitize($p['name'])) ?>',price:<?= floatval($p['price']) ?>,image:'<?= addslashes($img ?? '') ?>'})" title="Ajouter au panier">+</button>
        </div>
      </div>
    </div>
    <?php endforeach;
    else:
      foreach($fallback as $i => $p):
        $img = $allImgs[$i+2] ?? null;
    ?>
    <div class="product-card" data-reveal data-reveal-delay="<?= $i ?>">
      <div class="product-card__img-wrap">
        <?php if ($img): ?><img src="/Jolly_Beauty/images/<?= htmlspecialchars($img) ?>" alt="<?= $p['name'] ?>" loading="lazy"><?php else: ?><div style="width:100%;height:100%;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:.3">🌸</div><?php endif; ?>
        <?php if ($p['badge']): ?><span class="product-card__badge"><?= $p['badge'] ?></span><?php endif; ?>
        <div class="product-card__actions">
          <button class="card-action-btn">👁</button>
          <button class="card-action-btn">♡</button>
        </div>
      </div>
      <div class="product-card__body">
        <div class="product-card__cat"><?= $p['cat'] ?></div>
        <div class="product-card__name"><?= $p['name'] ?></div>
        <div class="product-card__foot">
          <div class="product-card__price"><?= $p['price'] ?></div>
          <button class="product-card__add" onclick="addToCart({id:'demo-<?= $i ?>',name:'<?= addslashes($p['name']) ?>',price:<?= floatval(str_replace([' €',','],['','.'],$p['price'])) ?>,image:''})">+</button>
        </div>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>

  <div class="view-all-wrap" data-reveal>
    <a href="/Jolly_Beauty/products.php" class="btn btn--outline">Voir toute la boutique</a>
  </div>
</section>

<!-- CATEGORIES -->
<section class="categories" id="collections">
  <div class="section-head" data-reveal>
    <div>
      <h2 class="section-title">Nos <em>collections</em></h2>
      <p class="section-sub">Bijoux délicats, soins sensoriels et coffrets pour se chouchouter</p>
    </div>
  </div>
  <div class="cat-grid">
    <a href="/Jolly_Beauty/products.php?category=bijoux" class="cat-card" data-reveal>
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,#F8D7DA,#F2A7B0);"></div>
      <div class="cat-card__body">
        <div class="cat-card__name">Bijoux</div>
        <div class="cat-card__cta">Découvrir →</div>
      </div>
    </a>
    <a href="/Jolly_Beauty/products.php?category=soins" class="cat-card" data-reveal data-reveal-delay="1">
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,#FDE8EC,#F2A7B0);"></div>
      <div class="cat-card__body">
        <div class="cat-card__name">Soins &amp; Rituels</div>
        <div class="cat-card__cta">Découvrir →</div>
      </div>
    </a>
    <a href="/Jolly_Beauty/products.php?category=coffrets" class="cat-card" data-reveal data-reveal-delay="2">
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,#F8D7DA,#D4788A);"></div>
      <div class="cat-card__body">
        <div class="cat-card__name">Coffrets</div>
        <div class="cat-card__cta">Découvrir →</div>
      </div>
    </a>
  </div>
</section>

<!-- BRAND STORY -->
<section class="brand-story" id="notre-histoire">
  <div class="brand-story__img" data-reveal>
    <?php $storyImg = $allImgs[2] ?? null; ?>
    <?php if ($storyImg): ?>
      <img src="/Jolly_Beauty/images/<?= htmlspecialchars($storyImg) ?>" alt="Jolly Beauty — Notre histoire">
    <?php else: ?>
      <div style="width:100%;height:100%;background:linear-gradient(160deg,var(--blush),var(--rose-pale));display:flex;align-items:center;justify-content:center;font-size:6rem;opacity:.25">🌹</div>
    <?php endif; ?>
  </div>
  <div class="brand-story__content" data-reveal data-reveal-delay="1">
    <div class="brand-story__tag">Notre histoire</div>
    <h2 class="brand-story__title">Rituels de<br><em>douceur pour soi</em></h2>
    <p class="brand-story__text">Jolly Beauty est née d'un désir simple : créer un espace où la féminité peut s'exprimer avec douceur. Dans un quotidien souvent rapide et exigeant, nous avons imaginé une parenthèse — un moment suspendu où chaque femme peut se reconnecter à elle-même.</p>
    <blockquote class="brand-story__quote">"Chez Jolly Beauty, nous croyons que la beauté réside dans les émotions et les moments que l'on crée pour soi."</blockquote>
    <a href="/Jolly_Beauty/products.php" class="btn btn--dark" style="align-self:flex-start">Découvrir nos collections</a>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials">
  <div style="text-align:center;margin-bottom:44px;" data-reveal>
    <h2 class="section-title">Elles <em>nous font confiance</em></h2>
    <p class="section-sub" style="margin:8px auto 0;max-width:420px;">Des milliers de femmes ont déjà adopté leurs rituels Jolly Beauty</p>
  </div>
  <div class="testimonials-grid">
    <?php
    $testis = [
      ['text'=>"Les bijoux sont absolument magnifiques ! Qualité incroyable pour le prix, je recommande à 100%. J'ai reçu plein de compliments.", 'name'=>'Amina D.', 'date'=>'Mars 2026', 'emoji'=>'💖'],
      ['text'=>"Le beurre de karité fouetté est une merveille. Ma peau est tellement douce depuis que je l'utilise. Texture divine, parfum subtil.", 'name'=>'Léa M.', 'date'=>'Février 2026', 'emoji'=>'✨'],
      ['text'=>"J'ai offert le coffret Rituel Douceur à ma meilleure amie pour son anniversaire. Elle était aux anges ! Emballage magnifique.", 'name'=>'Sarah K.', 'date'=>'Janvier 2026', 'emoji'=>'🌸'],
    ];
    foreach ($testis as $i => $t):
    ?>
    <div class="testi-card" data-reveal data-reveal-delay="<?= $i ?>">
      <div class="testi-stars">★★★★★</div>
      <p class="testi-text"><?= $t['text'] ?></p>
      <div class="testi-author">
        <div class="testi-avatar"><?= $t['emoji'] ?></div>
        <div>
          <div class="testi-name"><?= $t['name'] ?></div>
          <div class="testi-date"><?= $t['date'] ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- NEWSLETTER -->
<section class="newsletter" id="contact">
  <h2 data-reveal>Rejoignez la communauté<br><em style="font-style:italic">Jolly Beauty</em></h2>
  <p data-reveal>Recevez en avant-première nos nouvelles collections, conseils beauté et offres exclusives réservées à nos abonnées.</p>
  <form class="newsletter-form" data-reveal onsubmit="submitNewsletter(event, this)">
    <input type="email" name="email" placeholder="Votre adresse email" required>
    <button type="submit">S'inscrire</button>
  </form>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
// Newsletter
async function submitNewsletter(e, form) {
  e.preventDefault();
  const btn = form.querySelector('button');
  btn.textContent = '...'; btn.disabled = true;
  try {
    const fd = new FormData(form);
    const r = await fetch('/Jolly_Beauty/api/newsletter.php', {method:'POST', body:fd});
    const d = await r.json();
    showToast(d.message || 'Merci !');
    if (d.success) form.reset();
  } catch { showToast('Une erreur est survenue.'); }
  btn.textContent = "S'inscrire"; btn.disabled = false;
}

// Reveal on scroll
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('revealed'); });
}, { threshold: 0.1 });
document.querySelectorAll('[data-reveal]').forEach(el => revealObserver.observe(el));
</script>