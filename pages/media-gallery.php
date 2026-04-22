<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Galerie Média - Jolly Beauty';
include __DIR__ . '/../includes/header.php';
?>

<!-- HERO SECTION -->
<section class="hero-gallery">
  <div class="hero-gallery__bg">
    <div class="hero-gallery__slider">
      <div class="hero-gallery__slide active">
        <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/hero-1.jpg" alt="Galerie Jolly Beauty">
      </div>
      <div class="hero-gallery__slide">
        <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/hero-2.jpg" alt="Galerie Jolly Beauty">
      </div>
      <div class="hero-gallery__slide">
        <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/hero-3.jpg" alt="Galerie Jolly Beauty">
      </div>
    </div>
    <div class="hero-gallery__overlay"></div>
  </div>
  
  <div class="hero-gallery__content">
    <div class="hero-gallery__badge">✨ Média exclusif</div>
    <h1 class="hero-gallery__title">Galerie <em>Média</em></h1>
    <p class="hero-gallery__subtitle">Plongez dans l'univers Jolly Beauty à travers nos vidéos immersives et nos plus belles créations</p>
    
    <div class="hero-gallery__stats">
      <div class="stat-item">
        <span class="stat-number">5+</span>
        <span class="stat-label">Vidéos exclusives</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">16+</span>
        <span class="stat-label">Créations uniques</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">3</span>
        <span class="stat-label">Collections</span>
      </div>
    </div>
    
    <div class="hero-gallery__actions">
      <a href="#videos" class="btn btn--light">Voir les vidéos ↓</a>
      <a href="#gallery" class="btn btn--outline">Explorer la galerie ↓</a>
    </div>
  </div>
</section>

<!-- VIDÉOS SECTION -->
<section class="media-section" id="videos">
  <div class="section-head" data-reveal>
    <div>
      <h2 class="section-title">Nos <em>Vidéos</em></h2>
      <p class="section-sub">Plongez au cœur de nos créations et découvrez notre savoir-faire</p>
    </div>
  </div>

  <div class="videos-grid" data-reveal>
    <div class="video-card">
      <div class="video-wrapper">
        <video controls preload="metadata" poster="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/hero-2.jpg">
          <source src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/videos/bijou-creation.mp4" type="video/mp4">
          Votre navigateur ne supporte pas la vidéo.
        </video>
      </div>
      <div class="video-content">
        <h3>Création de Bijoux</h3>
        <p>Découvrez notre processus de fabrication artisanale</p>
      </div>
    </div>
    
    <div class="video-card">
      <div class="video-wrapper">
        <video controls preload="metadata" poster="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/hero-3.jpg">
          <source src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/videos/soin-rituel.mp4" type="video/mp4">
          Votre navigateur ne supporte pas la vidéo.
        </video>
      </div>
      <div class="video-content">
        <h3>Rituel de Soin</h3>
        <p>Transformez votre routine en moment de bien-être</p>
      </div>
    </div>
    
    <div class="video-card">
      <div class="video-wrapper">
        <video controls preload="metadata" poster="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/hero-4.jpg">
          <source src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/videos/coffret-presentation.mp4" type="video/mp4">
          Votre navigateur ne supporte pas la vidéo.
        </video>
      </div>
      <div class="video-content">
        <h3>Coffrets Prestige</h3>
        <p>Le cadeau parfait pour chaque occasion</p>
      </div>
    </div>
    
    <div class="video-card">
      <div class="video-wrapper">
        <video controls preload="metadata" poster="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/slider-1.jpg">
          <source src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/videos/fabrication-artisanale.mp4" type="video/mp4">
          Votre navigateur ne supporte pas la vidéo.
        </video>
      </div>
      <div class="video-content">
        <h3>Fabrication Artisanale</h3>
        <p>Le savoir-faire français à l'honneur</p>
      </div>
    </div>
    
    <div class="video-card">
      <div class="video-wrapper">
        <video controls preload="metadata" poster="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/slider-2.jpg">
          <source src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/videos/packshot-produit.mp4" type="video/mp4">
          Votre navigateur ne supporte pas la vidéo.
        </video>
      </div>
      <div class="video-content">
        <h3>Packshots Produits</h3>
        <p>Découvrez nos produits sous tous les angles</p>
      </div>
    </div>
  </div>
</section>

<!-- GALERIE PHOTOS -->
<section class="media-section" id="gallery" style="background: var(--rose-light);">
  <div class="section-head" data-reveal>
    <div>
      <h2 class="section-title">Notre <em>Galerie Photos</em></h2>
      <p class="section-sub">Un aperçu visuel de nos collections favorites</p>
    </div>
  </div>

  <!-- Bijoux -->
  <div class="category-gallery" data-reveal>
    <h3 class="gallery-category-title">Bijoux <span class="gallery-category-count">(8 pièces)</span></h3>
    <div class="gallery-grid">
      <?php 
      $bijouxImages = ['bague-coeur-douceur.jpg', 'bracelet-charms-eclat.jpg', 'bijoux-bague-1.jpg', 'bijoux-bague-2.jpg', 'bijoux-bracelet-1.jpg', 'bijoux-bracelet-2.jpg', 'bijoux-collier-1.jpg', 'bijoux-collier-2.jpg'];
      foreach ($bijouxImages as $img): 
      ?>
        <div class="gallery-item">
          <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/bijoux/<?= htmlspecialchars($img) ?>" alt="Bijoux Jolly Beauty" loading="lazy">
          <div class="gallery-overlay">
            <span class="gallery-overlay-text">Voir le produit</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Soins -->
  <div class="category-gallery" data-reveal>
    <h3 class="gallery-category-title">Soins & Rituels <span class="gallery-category-count">(5 produits)</span></h3>
    <div class="gallery-grid">
      <?php 
      $soinsImages = ['beurre-karite-fouette.jpeg', 'huile-corps-nourrissante.jpeg', 'soin-corps-2.jpg', 'serum-visage-antioxydant.jpeg', 'masque-detox-visage.jpeg'];
      foreach ($soinsImages as $img): 
      ?>
        <div class="gallery-item">
          <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/produits/<?= htmlspecialchars($img) ?>" alt="Soins Jolly Beauty" loading="lazy">
          <div class="gallery-overlay">
            <span class="gallery-overlay-text">Voir le produit</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Coffrets -->
  <div class="category-gallery" data-reveal>
    <h3 class="gallery-category-title">Coffrets <span class="gallery-category-count">(3 coffrets)</span></h3>
    <div class="gallery-grid">
      <?php 
      $coffretsImages = ['coffret-luxe.jpg', 'coffret-prestige-1.jpg', 'coffret-prestige-2.jpg'];
      foreach ($coffretsImages as $img): 
      ?>
        <div class="gallery-item">
          <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/coffrets/<?= htmlspecialchars($img) ?>" alt="Coffrets Jolly Beauty" loading="lazy">
          <div class="gallery-overlay">
            <span class="gallery-overlay-text">Voir le produit</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CALL TO ACTION -->
<section class="cta-section">
  <div class="cta-content" data-reveal>
    <h2 class="cta-title">Prêt(e) à découvrir nos créations ?</h2>
    <p class="cta-text">Explorez notre boutique complète et trouvez la pièce qui vous correspond</p>
    <div class="cta-buttons">
      <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/pages/category.php?c=all" class="btn btn--dark btn--large">Voir toute la boutique</a>
      <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/coffrets.php" class="btn btn--outline btn--large">Découvrir les coffrets</a>
    </div>
  </div>
</section>

<style>
/* Hero Gallery Styles */
.hero-gallery {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
  background: var(--dark);
}

.hero-gallery__bg {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.hero-gallery__slider {
  position: relative;
  width: 100%;
  height: 100%;
}

.hero-gallery__slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 2s ease-in-out;
}

.hero-gallery__slide.active {
  opacity: 1;
}

.hero-gallery__slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-gallery__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, 
    rgba(212, 120, 138, 0.8) 0%, 
    rgba(44, 26, 29, 0.6) 100%
  );
  z-index: 2;
}

.hero-gallery__content {
  position: relative;
  z-index: 3;
  text-align: center;
  color: white;
  max-width: 900px;
  width: 100%;
  margin: 0 auto;
  padding: 0 20px;
  animation: fadeInUp 1s ease-out;
}

.hero-gallery__badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 50px;
  padding: 8px 20px;
  font-size: 0.9rem;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 2rem;
  animation: pulse 2s infinite;
}

.hero-gallery__title {
  font-family: var(--font-serif);
  font-size: clamp(3rem, 8vw, 6rem);
  font-weight: 300;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.hero-gallery__title em {
  font-style: italic;
  color: var(--rose-light);
}

.hero-gallery__subtitle {
  font-size: clamp(1.2rem, 3vw, 1.6rem);
  line-height: 1.6;
  margin-bottom: 3rem;
  opacity: 0.9;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.hero-gallery__stats {
  display: flex;
  justify-content: center;
  gap: 3rem;
  margin-bottom: 3rem;
  flex-wrap: wrap;
}

.stat-item {
  text-align: center;
}

.stat-number {
  display: block;
  font-family: var(--font-serif);
  font-size: 2.5rem;
  font-weight: 600;
  color: var(--rose-light);
  line-height: 1;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  opacity: 0.8;
}

.hero-gallery__actions {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn--light {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: white;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-block;
}

.btn--light:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.05);
    opacity: 0.8;
  }
}

/* Media Section Styles */
.media-section {
  padding: 5rem 5%;
}

/* Conserver le background pleine largeur, contenu centré */
.media-section > .section-head,
.media-section > .videos-grid,
.media-section > .category-gallery {
  max-width: 1400px;
  margin-left: auto;
  margin-right: auto;
}

.videos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2.5rem;
  margin-top: 3rem;
}

.video-card {
  background: var(--white);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.video-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.video-wrapper {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
  overflow: hidden;
}

.video-wrapper video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  background: var(--blush);
}

.video-content {
  padding: 1.5rem;
}

.video-content h3 {
  font-family: var(--font-serif);
  font-size: 1.3rem;
  color: var(--dark);
  margin: 0 0 0.5rem 0;
  font-weight: 600;
}

.video-content p {
  color: var(--text-soft, var(--muted));
  margin: 0;
  line-height: 1.5;
}

/* Gallery Styles */
.category-gallery {
  margin-top: 4rem;
}

.gallery-category-title {
  font-family: var(--font-serif);
  font-size: 1.8rem;
  color: var(--dark);
  margin-bottom: 2rem;
  text-align: center;
}

.gallery-category-count {
  font-size: 1rem;
  color: var(--text-soft, var(--muted));
  font-weight: normal;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.gallery-item {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  aspect-ratio: 1;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.gallery-item:hover {
  transform: scale(1.03);
}

.gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.gallery-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
  display: flex;
  align-items: flex-end;
  padding: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
  opacity: 1;
}

.gallery-item:hover img {
  transform: scale(1.1);
}

.gallery-overlay-text {
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
}

/* CTA Section */
.cta-section {
  padding: 5rem 5%;
  background: linear-gradient(135deg, var(--rose-deep), var(--rose-pale));
  text-align: center;
}

.cta-content {
  max-width: 800px;
  margin: 0 auto;
}

.cta-title {
  font-family: var(--font-serif);
  font-size: 2.5rem;
  color: var(--white);
  margin-bottom: 1rem;
}

.cta-text {
  font-size: 1.2rem;
  color: rgba(255,255,255,0.9);
  margin-bottom: 2.5rem;
  line-height: 1.6;
}

.cta-buttons {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn--large {
  padding: 1rem 2.5rem;
  font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .media-section {
    padding: 3rem 4%;
  }
  
  .videos-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .gallery-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
  }
  
  .gallery-category-title {
    font-size: 1.5rem;
  }
  
  .cta-title {
    font-size: 2rem;
  }
  
  .cta-text {
    font-size: 1.1rem;
  }
  
  .cta-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn--large {
    width: 100%;
    max-width: 300px;
  }
}
</style>

<script>
// Hero Gallery Slider
document.addEventListener('DOMContentLoaded', function() {
  const slides = document.querySelectorAll('.hero-gallery__slide');
  const totalSlides = slides.length;
  let currentSlide = 0;
  
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
  }
  
  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
  }
  
  // Change slide every 4 seconds
  setInterval(nextSlide, 4000);
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
