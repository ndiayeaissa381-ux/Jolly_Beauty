<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Contact — Jolly Beauty';
$jbBase    = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');
$heroImg   = $jbBase . '/assets/images/soins/soin-visage-1.jpg';
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
    $name    = trim((string) ($_POST['name'] ?? ''));
    $email   = trim((string) ($_POST['email'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        $_SESSION['contact_flash'] = 'Votre message a bien été enregistré. Nous vous répondrons sous peu.';
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

<div class="sp-page">
  <section class="sp-hero sp-contact-hero">
    <div class="sp-hero__bg" style="background-image:url('<?= htmlspecialchars($heroImg) ?>')"></div>
    <div class="sp-hero__overlay"></div>
    <div class="sp-hero__inner">
      <h1>Contact</h1>
      <p>Une question, un conseil ou un mot doux : nous sommes à votre écoute.</p>
    </div>
  </section>

  <section class="sp-info-grid" style="background:var(--sp-bg,#fef9f6);">
    <div class="sp-info-card">
      <div class="ic">📧</div>
      <h3>Email</h3>
      <p>contact@jollybeauty.fr</p>
    </div>
    <div class="sp-info-card">
      <div class="ic">📍</div>
      <h3>Boutique en ligne</h3>
      <p>Service client du lundi au vendredi, 9h–18h.</p>
    </div>
    <div class="sp-info-card">
      <div class="ic">🚚</div>
      <h3>Livraison</h3>
      <p>Livraison gratuite dès 60 € sur le site.</p>
    </div>
  </section>

  <div class="sp-form-wrap">
    <div class="sp-form-card">
      <h2>Écrivez-nous</h2>
      <p class="lead">Commande, produit ou partenariat : décrivez-nous votre demande.</p>

      <?php if ($error !== ''): ?>
        <div class="sp-alert sp-alert--err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success !== ''): ?>
        <div class="sp-alert sp-alert--ok"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= $jbBase ?>/contact.php">
        <input type="hidden" name="contact_form" value="1">
        <div class="sp-row">
          <div class="sp-field">
            <label for="c_name">Nom *</label>
            <input id="c_name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
          </div>
          <div class="sp-field">
            <label for="c_email">Email *</label>
            <input id="c_email" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="sp-field">
          <label for="c_subject">Sujet *</label>
          <select id="c_subject" name="subject" required>
            <option value="">Choisissez…</option>
            <option value="commande" <?= (($_POST['subject'] ?? $prefSubject) === 'commande') ? 'selected' : '' ?>>Commande</option>
            <option value="produit" <?= (($_POST['subject'] ?? $prefSubject) === 'produit') ? 'selected' : '' ?>>Produit</option>
            <option value="retour" <?= (($_POST['subject'] ?? $prefSubject) === 'retour') ? 'selected' : '' ?>>Retour / échange</option>
            <option value="autre" <?= (($_POST['subject'] ?? $prefSubject) === 'autre') ? 'selected' : '' ?>>Autre</option>
          </select>
        </div>
        <div class="sp-field">
          <label for="c_msg">Message *</label>
          <textarea id="c_msg" name="message" required rows="6" placeholder="Votre message…"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="sp-submit">Envoyer</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
