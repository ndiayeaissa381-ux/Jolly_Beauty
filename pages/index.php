<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Jolly Beauty — La beauté des moments doux';
$products  = getFeaturedProducts();
include __DIR__ . '/../includes/header.php';
?>

<!-- HERO CAROUSEL -->
<section class="hero-carousel">
  <div class="carousel-container">
    <div class="carousel-slide active" style="background: linear-gradient(rgba(212, 120, 138, 0.4), rgba(212, 120, 138, 0.4)), url('<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/slider-1.jpg') center/cover;">
      <div class="carousel-content">
        <div class="carousel-badge">NOUVEAUTÉ</div>
        <h1>L'Éclat de la féminité</h1>
        <p>Découvrez nos bijoux en acier inoxydable qui subliment votre beauté au quotidien</p>
        <div class="carousel-buttons">
          <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bijoux.php" class="btn btn-primary">Explorer les Bijoux</a>
          <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/coffrets.php" class="btn btn-outline">Coffrets Cadeaux</a>
        </div>
      </div>
    </div>
    
    <div class="carousel-slide" style="background: linear-gradient(rgba(248, 215, 218, 0.4), rgba(248, 215, 218, 0.4)), url('<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/slider-2.jpg') center/cover;">
      <div class="carousel-content">
        <div class="carousel-badge">SOINS NATURELS</div>
        <h1>La douceur des rituels</h1>
        <p>Des soins sensoriels pour prendre soin de vous avec des ingrédients naturels</p>
        <div class="carousel-buttons">
          <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/soins-rituels.php" class="btn btn-primary">Découvrir les Soins</a>
          <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bijoux.php" class="btn btn-outline">Bijoux assortis</a>
        </div>
      </div>
    </div>
    
    <div class="carousel-slide" style="background: linear-gradient(rgba(253, 232, 236, 0.4), rgba(253, 232, 236, 0.4)), url('<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/slider/slider-3.jpg') center/cover;">
      <div class="carousel-content">
        <div class="carousel-badge">CADEAUX PARFAITS</div>
        <h1>Offrir la beauté</h1>
        <p>Les coffrets Jolly Beauty pour faire plaisir à celles que vous aimez</p>
        <div class="carousel-buttons">
          <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/coffrets.php" class="btn btn-primary">Voir les Coffrets</a>
          <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/notre-histoire.php" class="btn btn-outline">Notre histoire</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Navigation dots -->
  <div class="carousel-dots">
    <button class="dot active" onclick="currentSlide(1)"></button>
    <button class="dot" onclick="currentSlide(2)"></button>
    <button class="dot" onclick="currentSlide(3)"></button>
  </div>
  
  <!-- Arrow navigation -->
  <button class="carousel-arrow prev" onclick="changeSlide(-1)">❮</button>
  <button class="carousel-arrow next" onclick="changeSlide(1)">❯</button>
</section>

<!-- CATEGORY SHOWCASE -->
<section class="category-showcase">
  <div class="category-showcase-header">
    <h2>Explorez nos univers</h2>
    <p>Découvrez nos trois catégories conçues pour sublimer votre beauté au quotidien</p>
  </div>
  
  <div class="category-grid">
    <div class="category-card" onclick="window.location.href='<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bijoux.php'">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/bijoux/bijoux-bracelet-1.jpg" alt="Bijoux élégants" class="category-image">
      <div class="category-content">
        <h3>BIJOUX</h3>
        <p>Acier inoxydable et résistants à l'eau pour une beauté intemporelle</p>
        <div class="category-arrow">→</div>
      </div>
    </div>
    
    <div class="category-card" onclick="window.location.href='<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/soins-rituels.php'">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/produits/beurre-karite-fouette.jpeg" alt="Soins naturels" class="category-image">
      <div class="category-content">
        <h3>SOINS &amp; RITUELS</h3>
        <p>Formules naturelles pour chouchouter votre peau au quotidien</p>
        <div class="category-arrow">→</div>
      </div>
    </div>
    
    <div class="category-card" onclick="window.location.href='<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/coffrets.php'">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/coffrets/coffret-luxe.jpg" alt="Coffrets cadeaux" class="category-image">
      <div class="category-content">
        <h3>COFFRETS</h3>
        <p>Le cadeau parfait pour faire plaisir à celles que vous aimez</p>
        <div class="category-arrow">→</div>
      </div>
    </div>
  </div>
</section>

<!-- BEST-SELLERS -->
<section class="best-sellers">
  <h2 class="section-title">NOS BEST-SELLERS</h2>
  <div class="products-grid">
    <div class="product-card">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/bijoux/bracelet-charms-eclat.jpg" alt="Bracelet Charms Éclat" class="product-image">
      <h3>Bracelet Charms Éclat</h3>
      <p class="product-price">36,90 €</p>
      <button class="btn-add-cart">AJOUTER AU PANIER</button>
    </div>
    <div class="product-card">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/bijoux/bague-coeur-douceur.jpg" alt="Bague Cœur Douceur" class="product-image">
      <h3>Bague Cœur Douceur</h3>
      <p class="product-price">29,90 €</p>
      <button class="btn-add-cart">AJOUTER AU PANIER</button>
    </div>
    <div class="product-card">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/produits/beurre-karite-fouette.jpeg" alt="Beurre de Karité Finesse" class="product-image">
      <h3>Beurre de Karité Finesse</h3>
      <p class="product-price">24,90 €</p>
      <button class="btn-add-cart">AJOUTER AU PANIER</button>
    </div>
    <div class="product-card">
      <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/bijoux/bijoux-collier-1.jpg" alt="Collier Lumière" class="product-image">
      <h3>Collier Lumière</h3>
      <p class="product-price">42,90 €</p>
      <button class="btn-add-cart">AJOUTER AU PANIER</button>
    </div>
  </div>
  <div class="view-all-container">
    <button class="btn-view-all">VOIR TOUS LES PRODUITS</button>
  </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="testimonials-section">
  <div class="testimonials-header">
    <h2>Elles nous font confiance</h2>
    <p>Découvrez les expériences de nos clientes</p>
  </div>

  <div class="testimonials-grid">
    <div class="testimonial-card">
      <div class="testimonial-stars">★★★★★</div>
      <p>"Les bijoux sont absolument magnifiques ! Qualité incroyable pour le prix, je recommande à 100%. J'ai reçu plein de compliments."</p>
      <div class="testimonial-author">
        <div class="author-avatar">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d4788a" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="author-info">
          <h4>Amina D.</h4>
          <span>Mars 2026</span>
        </div>
      </div>
    </div>

    <div class="testimonial-card">
      <div class="testimonial-stars">★★★★★</div>
      <p>"Le beurre de karité fouetté est une merveille. Ma peau est tellement douce depuis que je l'utilise. Texture divine, parfum subtil."</p>
      <div class="testimonial-author">
        <div class="author-avatar">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d4788a" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="author-info">
          <h4>Léa M.</h4>
          <span>Février 2026</span>
        </div>
      </div>
    </div>

    <div class="testimonial-card">
      <div class="testimonial-stars">★★★★★</div>
      <p>"J'ai offert le coffret Rituel Douceur à ma meilleure amie pour son anniversaire. Elle était aux anges ! Emballage magnifique."</p>
      <div class="testimonial-author">
        <div class="author-avatar">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d4788a" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="author-info">
          <h4>Sarah K.</h4>
          <span>Janvier 2026</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LIFESTYLE SECTION -->
<section class="lifestyle-section">
  <div class="lifestyle-left">
    <h2>Prenez soin de vous, naturellement.</h2>
    <p>Des rituels sensoriels pour se sentir bien, chaque jour.</p>
    <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/soins-rituels.php" class="btn-primary">DÉCOUVRIR NOS RITUELS</a>
  </div>
  <div class="lifestyle-right">
    <img src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/soins/soin-visage-1.jpg" alt="Rituels de soin" class="lifestyle-image">
  </div>
</section>

<!-- FEATURE HIGHLIGHTS -->
<section class="feature-highlights">
  <div class="feature-item">
    <div class="feature-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M12 2L2 7L12 12L22 7L12 2Z"/>
        <path d="M2 17L12 22L22 17"/>
        <path d="M2 12L12 17L22 12"/>
      </svg>
    </div>
    <h3>INGRÉDIENTS NATURELS</h3>
    <p>Sélectionnés avec soin pour votre peau</p>
  </div>
  <div class="feature-item">
    <div class="feature-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
      </svg>
    </div>
    <h3>RÉSISTANTS À L'EAU</h3>
    <p>Vos bijoux au quotidien sans crainte</p>
  </div>
  <div class="feature-item">
    <div class="feature-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
        <line x1="9" y1="9" x2="15" y2="9"/>
        <line x1="9" y1="15" x2="15" y2="15"/>
      </svg>
    </div>
    <h3>EMBALLAGE LUXE</h3>
    <p>Une expérience d'ouverture magique</p>
  </div>
  <div class="feature-item">
    <div class="feature-icon">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="1" y="3" width="15" height="13"/>
        <polygon points="16,8 20,8 23,11 23,16 16,16"/>
        <circle cx="5.5" cy="18.5" r="2.5"/>
        <circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
    </div>
    <h3>LIVRAISON OFFERTE</h3>
    <p>Dès 50€ d'achat en France métropolitaine</p>
  </div>
</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
// Carousel functionality
let currentSlideIndex = 1;
let slideInterval;

function currentSlide(n) {
  showSlide(currentSlideIndex = n);
  resetInterval();
}

function changeSlide(n) {
  showSlide(currentSlideIndex += n);
  resetInterval();
}

function showSlide(n) {
  const slides = document.querySelectorAll('.carousel-slide');
  const dots = document.querySelectorAll('.dot');
  
  if (n > slides.length) currentSlideIndex = 1;
  if (n < 1) currentSlideIndex = slides.length;
  
  slides.forEach(slide => slide.classList.remove('active'));
  dots.forEach(dot => dot.classList.remove('active'));
  
  slides[currentSlideIndex - 1].classList.add('active');
  dots[currentSlideIndex - 1].classList.add('active');
}

function autoSlide() {
  currentSlideIndex++;
  showSlide(currentSlideIndex);
}

function resetInterval() {
  clearInterval(slideInterval);
  slideInterval = setInterval(autoSlide, 5000);
}

// Cart functionality for best-sellers
document.addEventListener('DOMContentLoaded', function() {
  // Start carousel
  slideInterval = setInterval(autoSlide, 5000);
  
  // Add to cart buttons
  const addToCartButtons = document.querySelectorAll('.btn-add-cart');
  addToCartButtons.forEach((button, index) => {
    button.addEventListener('click', function() {
      const products = [
        {id: 1, name: 'Bracelet Charms Éclat', price: 36.90, image: 'bracelet-charms-eclat.jpg', category: 'bijoux'},
        {id: 2, name: 'Bague Cœur Douceur', price: 29.90, image: 'bague-coeur-douceur.jpg', category: 'bijoux'},
        {id: 3, name: 'Beurre de Karité Finesse', price: 24.90, image: 'beurre-karite-fouette.jpeg', category: 'soins'},
        {id: 4, name: 'Collier Lumière', price: 42.90, image: 'bijoux-collier-1.jpg', category: 'bijoux'}
      ];
      
      const product = products[index];
      if (product && typeof addToCart === 'function') {
        addToCart(product);
      } else {
        console.log('Adding to cart:', product);
        alert('Produit ajouté au panier: ' + product.name);
      }
    });
  });

  // View all products button
  const viewAllButton = document.querySelector('.btn-view-all');
  if (viewAllButton) {
    viewAllButton.addEventListener('click', function() {
      window.location.href = '<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/bijoux.php';
    });
  }

  // Add smooth scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Observe sections for animation
  document.querySelectorAll('.category-card, .testimonial-card, .feature-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
});

// Newsletter
async function submitNewsletter(e, form) {
  e.preventDefault();
  const btn = form.querySelector('button');
  btn.textContent = '...'; btn.disabled = true;
  try {
    const fd = new FormData(form);
    const r = await fetch('<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/api/newsletter.php', {method:'POST', body:fd});
    const d = await r.json();
    if (typeof showToast === 'function') {
      showToast(d.message || 'Merci !');
    } else {
      alert(d.message || 'Merci !');
    }
    if (d.success) form.reset();
  } catch { 
    if (typeof showToast === 'function') {
      showToast('Une erreur est survenue.'); 
    } else {
      alert('Une erreur est survenue.');
    }
  }
  btn.textContent = "S'inscrire"; btn.disabled = false;
}
</script>