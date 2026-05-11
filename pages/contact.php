<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Contact — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$extraCss  = '<link rel="stylesheet" href="' . $jbBase . '/assets/css/static-pages.css">';

$error   = '';
$success = '';

$prefSubject = '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $raw = strtolower(trim((string)($_GET['subject'] ?? '')));
    $map = [
        'commande' => 'commande',
        'order'    => 'commande',
        'produit'  => 'produit',
        'product'  => 'produit',
        'retour'   => 'retour',
        'return'   => 'retour',
        'autre'    => 'autre',
    ];
    if (isset($map[$raw])) {
        $prefSubject = $map[$raw];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    $firstname = trim((string) ($_POST['firstname'] ?? ''));
    $lastname  = trim((string) ($_POST['lastname'] ?? ''));
    $email     = trim((string) ($_POST['email'] ?? ''));
    $subject   = trim((string) ($_POST['subject'] ?? ''));
    $message   = trim((string) ($_POST['message'] ?? ''));

    if ($firstname === '' || $lastname === '' || $email === '' || $subject === '' || $message === '') {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        $_SESSION['contact_flash'] = 'Votre message a bien été enregistré. Nous vous répondrons sous 24 à 48h ouvrées.';
        header('Location: ' . BASE_URL . '/contact.php');
        exit;
    }
}

if (!empty($_SESSION['contact_flash'])) {
    $success = (string) $_SESSION['contact_flash'];
    unset($_SESSION['contact_flash']);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="contact-page">
  <!-- Main Contact Section -->
  <section class="contact-main">
    <div class="contact-container">
      <!-- Left Column -->
      <div class="contact-left">
        <h1>Contactez-nous</h1>
        <h2>Nous sommes là pour vous.</h2>
        <p class="contact-description">
          Une question, un conseil ou une demande particulière ?<br>
          Notre équipe vous répond avec plaisir<br>
          dans les plus brefs délais.
        </p>
        <div class="contact-image">
          <img src="<?= $jbBase ?>/assets/images/bijoux/bijoux-collier-1.jpg" alt="Bijou Jolly Beauty">
        </div>
      </div>
      
      <!-- Right Column - Contact Form -->
      <div class="contact-right">
        <h2>Envoyez-nous un message</h2>
        
        <?php if ($error !== ''): ?>
          <div class="contact-alert contact-alert--error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
          <div class="contact-alert contact-alert--success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?= $jbBase ?>/contact.php" class="contact-form">
          <input type="hidden" name="contact_form" value="1">
          
          <div class="form-row">
            <div class="form-group">
              <input type="text" id="firstname" name="firstname" required 
                     value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>"
                     placeholder="Prénom">
            </div>
            <div class="form-group">
              <input type="text" id="lastname" name="lastname" required 
                     value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>"
                     placeholder="Nom">
            </div>
          </div>
          
          <div class="form-group">
            <input type="email" id="email" name="email" required 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="E-mail">
          </div>
          
          <div class="form-group">
            <input type="text" id="subject" name="subject" required 
                   value="<?= htmlspecialchars($_POST['subject'] ?? $prefSubject) ?>"
                   placeholder="Sujet">
          </div>
          
          <div class="form-group">
            <textarea id="message" name="message" required rows="6" 
                      placeholder="Votre message"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>
          
          <button type="submit" class="contact-submit">ENVOYER LE MESSAGE</button>
          
          <p class="contact-response-time">Nous vous répondrons sous 24 à 48h ouvrées.</p>
        </form>
      </div>
    </div>
  </section>
  
  <!-- Contact Info Cards Section -->
  <section class="contact-info-section">
    <div class="contact-info-container">
      <div class="contact-info-card">
        <span class="contact-icon">✉️</span>
        <h3>E-mail</h3>
        <p>contact@jolly-beauty.fr<br>Nous répondons sous 24 à 48h.</p>
      </div>
      
      <div class="contact-info-card">
        <span class="contact-icon">📞</span>
        <h3>Téléphone & WhatsApp</h3>
        <p><a href="tel:+330756957481" style="color:inherit;text-decoration:none;">+33 07 56 95 74 81</a><br><a href="https://wa.me/330756957481" target="_blank" rel="noopener" style="color:#d4788a;text-decoration:none;">💬 WhatsApp Business</a><br>Du lundi au vendredi<br>9h00 – 18h00</p>
      </div>
      
      <div class="contact-info-card">
        <span class="contact-icon">💬</span>
        <h3>Instagram</h3>
        <p><a href="https://www.instagram.com/jollyy_beauty" target="_blank" rel="noopener" style="color:inherit;text-decoration:none;">@jollyy_beauty</a><br>Envoyez-nous un message<br>en direct !</p>
      </div>
      
      <div class="contact-info-card">
        <span class="contact-icon">📍</span>
        <h3>Adresse</h3>
        <p>Jolly Beauty<br>Paris, France</p>
      </div>
    </div>
  </section>
  
  <!-- FAQ Section -->
  <section class="faq-section">
    <div class="faq-container">
      <div class="faq-header">
        <h2 class="faq-title">Questions fréquentes</h2>
        <div class="faq-cta-header">
          <p>Vous ne trouvez pas votre réponse ?</p>
          <a href="#contact-form">Contactez-nous →</a>
        </div>
      </div>
      
      <div class="faq-grid">
        <div class="faq-item">
          <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
            <h4>Quels sont les délais de livraison ?</h4>
            <span class="faq-toggle">+</span>
          </div>
          <div class="faq-answer">
            <p>Les délais de livraison sont de 2 à 5 jours ouvrés en France métropolitaine. Pour l'Europe, comptez 5 à 10 jours ouvrés.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
            <h4>Quels modes de paiement acceptez-vous ?</h4>
            <span class="faq-toggle">+</span>
          </div>
          <div class="faq-answer">
            <p>Nous acceptons les cartes Visa, Mastercard, American Express, ainsi que PayPal et Apple Pay.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
            <h4>Puis-je retourner un article ?</h4>
            <span class="faq-toggle">+</span>
          </div>
          <div class="faq-answer">
            <p>Oui, vous disposez de 14 jours après réception pour retourner un article non porté et dans son emballage d'origine.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
            <h4>Vos soins sont-ils adaptés aux peaux sensibles ?</h4>
            <span class="faq-toggle">+</span>
          </div>
          <div class="faq-answer">
            <p>Absolument ! Tous nos soins sont formulés sans parabènes, sulfates ni silicones et sont testés dermatologiquement.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Features Bar Section -->
  <section class="contact-features-bar">
    <div class="features-bar-container">
      <div class="feature-bar-item">
        <span class="feature-bar-icon">�</span>
        <div class="feature-bar-text">
          <h4>LIVRAISON OFFERTE</h4>
          <p>Dès 60€ d'achat</p>
        </div>
      </div>
      <div class="feature-bar-item">
        <span class="feature-bar-icon">🔒</span>
        <div class="feature-bar-text">
          <h4>PAIEMENT SÉCURISÉ</h4>
          <p>Transactions 100% sécurisées</p>
        </div>
      </div>
      <div class="feature-bar-item">
        <span class="feature-bar-icon">🔄</span>
        <div class="feature-bar-text">
          <h4>RETOURS FACILES</h4>
          <p>14 jours pour changer d'avis</p>
        </div>
      </div>
      <div class="feature-bar-item">
        <span class="feature-bar-icon">💝</span>
        <div class="feature-bar-text">
          <h4>SERVICE CLIENT</h4>
          <p>À votre écoute</p>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
