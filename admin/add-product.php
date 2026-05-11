<?php
require_once __DIR__ . '/../includes/config.php';
$jbBase = htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8');

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name       = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $sub        = trim($_POST['sub'] ?? '');
    $short      = trim($_POST['short'] ?? '');
    $price      = (float)str_replace(',', '.', $_POST['price'] ?? 0);
    $oldPrice   = (trim($_POST['old_price'] ?? '') !== '') ? (float)str_replace(',', '.', $_POST['old_price']) : null;
    $badge      = trim($_POST['badge'] ?? '');
    $description= trim($_POST['description'] ?? '');
    $stock      = (int)($_POST['stock'] ?? 0);
    $featured   = isset($_POST['featured']) ? 1 : 0;
    $active     = isset($_POST['active']) ? 1 : 0;
    $galleryOnly = isset($_POST['gallery_only']) ? 1 : 0;

    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$name))), '-');

    if (!$name || $price <= 0 || $categoryId <= 0) {
        $msg = 'Le nom, la catégorie et un prix valide sont obligatoires.';
        $msgType = 'error';
    } else {
        try {
            $base = $slug;
            $i = 1;
            while ((int)$db->query("SELECT COUNT(*) FROM products WHERE slug='".addslashes($slug)."'")->fetchColumn() > 0) {
                $slug = $base . '-' . $i++;
            }

            $stmt = $db->prepare(
                'INSERT INTO products (name,slug,category_id,sub,short,description,price,old_price,badge,stock,featured,active,gallery_only)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([$name,$slug,$categoryId,$sub,$short,$description,$price,$oldPrice,$badge,$stock,$featured,$active,$galleryOnly]);
            $productId = (int)$db->lastInsertId();

            // Gestion du téléchargement de plusieurs images
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadDir = __DIR__ . '/../assets/images/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                foreach ($_FILES['images']['name'] as $key => $name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg','jpeg','png','webp','gif'], true)) {
                            $filename = $slug . '-' . time() . '-' . $key . '.' . $ext;
                            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $uploadDir . $filename)) {
                                $url = BASE_URL . '/assets/images/uploads/' . $filename;
                                $imgStmt = $db->prepare('INSERT INTO product_images (product_id,url,sort_order) VALUES (?,?,?)');
                                $imgStmt->execute([$productId, $url, $key]);
                            }
                        }
                    }
                }
            }

            $msg = 'Produit ajouté avec succès !';
            $msgType = 'success';
        } catch (PDOException $e) {
            $msg = 'Erreur : ' . $e->getMessage();
            $msgType = 'error';
        }
    }
}

$categories = $db->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Ajouter un produit — Admin Jolly Beauty</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Poppins:wght@300;400;500;600&display=swap">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --rose:#F2A7B0;--rp:#FDF4F6;--rd:#D4788A;--rk:#B85C6E;
      --bl:#F8D7DA;--dk:#2C1A1D;--tx:#5C3A3F;--mu:#A07880;
      --border:rgba(242,167,176,.25);--sf:'Playfair Display',serif;--ss:'Poppins',sans-serif;
      --sb:248px;--r:12px;--sh:0 2px 14px rgba(192,92,107,.09);
    }
    body{font-family:var(--ss);background:#F7EFF2;color:var(--tx);display:flex;min-height:100vh;font-size:.87rem}
    a{color:inherit;text-decoration:none}

    .sb{width:var(--sb);flex-shrink:0;background:var(--dk);display:flex;flex-direction:column;position:fixed;inset:0 auto 0 0;z-index:50;overflow-y:auto}
    .sb-logo{padding:26px 22px 20px;border-bottom:1px solid rgba(255,255,255,.07)}
    .sb-logo .wm{font-family:var(--sf);font-style:italic;font-size:1.45rem;color:#fff}
    .sb-logo .tg{font-size:.58rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:var(--rose);margin-top:2px}
    .sb-nav{padding:16px 10px;flex:1}
    .ns{font-size:.57rem;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.28);padding:14px 12px 5px}
    .ni{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:9px;font-size:.78rem;font-weight:500;color:rgba(255,255,255,.65);cursor:pointer;transition:all .18s;margin-bottom:1px}
    .ni:hover{background:rgba(255,255,255,.07);color:#fff}
    .ni.active{background:var(--rd);color:#fff}
    .ni .ic{font-size:.95rem;width:19px;text-align:center;flex-shrink:0;opacity:.95;display:flex;align-items:center;justify-content:center}
    .sb-bot{padding:14px 10px;border-top:1px solid rgba(255,255,255,.07)}
    .sb-user{display:flex;align-items:center;gap:10px;padding:10px 12px;color:rgba(255,255,255,.55);font-size:.74rem}
    .sb-av{width:32px;height:32px;background:var(--rd);border-radius:50%;display:grid;place-items:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0}

    .main{margin-left:var(--sb);flex:1;display:flex;flex-direction:column;min-height:100vh}
    .tb{background:#fff;padding:0 28px;height:62px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:40;box-shadow:var(--sh)}
    .tb-title{font-family:var(--sf);font-size:1.25rem;font-weight:500;color:var(--dk)}
    .tb-r{display:flex;align-items:center;gap:10px}
    .tbtn{display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:50px;font-size:.72rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;border:none;cursor:pointer;transition:all .2s}
    .t-ghost{background:var(--bl);color:var(--dk)}.t-ghost:hover{background:var(--rose)}
    .t-rose{background:var(--rd);color:#fff}.t-rose:hover{background:var(--rk)}
    .ct{padding:28px;flex:1}

    .page{max-width:1180px;margin:0 auto}
    .grid{display:grid;grid-template-columns:1.45fr .85fr;gap:18px;align-items:start}
    @media(max-width:1050px){.grid{grid-template-columns:1fr}}

    .panel{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(44,26,29,.06);border:1px solid rgba(242,167,176,.18);overflow:hidden}
    .panel-h{padding:16px 20px;border-bottom:1px solid var(--border);font-size:.62rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--mu);background:linear-gradient(135deg,#FDF4F6,#fff)}
    .panel-b{padding:18px 20px}

    .fg{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
    .fl{font-size:.62rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--mu)}
    .fi,.fa,select{
      width:100%;padding:12px 14px;border:1.5px solid #EDD5D9;border-radius:10px;
      font-family:var(--ss);font-size:.86rem;color:var(--dk);outline:none;background:#fff;
      transition:border .2s,box-shadow .2s
    }
    .fi:focus,.fa:focus,select:focus{border-color:var(--rd);box-shadow:0 0 0 3px rgba(212,120,138,.12)}
    .fa{min-height:110px;resize:vertical}
    .row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:520px){.row2{grid-template-columns:1fr}}

    .up{border:2px dashed #EDD5D9;border-radius:12px;padding:18px;text-align:center;cursor:pointer;transition:border .2s,background .2s;position:relative;background:#fff}
    .up:hover,.up.drag{border-color:var(--rd);background:#FDF4F6}
    .up input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
    .up-ico{width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#FDE8EC,#F8D7DA);display:grid;place-items:center;margin:0 auto 10px;font-size:1.25rem}
    .up-t{font-size:.82rem;color:var(--mu);line-height:1.6}
    .up-t strong{color:var(--rd)}
    .prev{margin-top:12px;display:none;gap:10px;align-items:center;justify-content:flex-start}
    .prev img{width:74px;height:74px;border-radius:12px;object-fit:cover;border:1px solid rgba(242,167,176,.25);background:var(--bl)}
    .prev .meta{display:flex;flex-direction:column;gap:2px;text-align:left}
    .prev .meta .n{font-weight:700;color:var(--dk);font-size:.84rem}
    .prev .meta .s{color:var(--mu);font-size:.74rem}

    .opt{display:flex;flex-direction:column;gap:10px}
    .ck{display:flex;align-items:center;gap:10px;font-size:.84rem;color:var(--dk);font-weight:500}
    .ck input{width:16px;height:16px;accent-color:var(--rd)}

    .alert{padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:.85rem;border:1px solid}
    .alert-success{background:#F0FBF4;color:#166534;border-color:#BBF7D0}
    .alert-error{background:#FEF2F2;color:#991B1B;border-color:#FECACA}
    .submit{margin-top:14px;display:flex;justify-content:flex-end}

    /* Category Selector Styles */
    .category-options {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 12px;
      margin-top: 8px;
    }
    .category-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 16px 12px;
      border: 2px solid #EDD5D9;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.2s ease;
      background: #fff;
      position: relative;
    }
    .category-card:hover {
      border-color: var(--rd);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(212,120,138,.15);
    }
    .category-card.selected {
      border-color: var(--rd);
      background: linear-gradient(135deg, #FDF4F6, #fff);
      box-shadow: 0 4px 16px rgba(212,120,138,.2);
    }
    .category-card.selected::after {
      content: '';
      position: absolute;
      top: -8px;
      right: -8px;
      width: 24px;
      height: 24px;
      background: var(--rd);
      color: #fff;
      border-radius: 50%;
      display: grid;
      place-items: center;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
      background-size: 14px;
      background-repeat: no-repeat;
      background-position: center;
    }
    .category-card input[type="radio"] {
      position: absolute;
      opacity: 0;
    }
    .cat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      font-size: 1.5rem;
    }
    .cat-name {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--dk);
      text-align: center;
    }
    .cat-slug {
      font-size: 0.65rem;
      color: var(--mu);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .subcategory-selector select {
      width: 100%;
    }
    .subcategory-selector select option:disabled {
      color: #999;
      font-style: italic;
    }

    /* RESPONSIVE */
    @media(max-width:768px){
      .sb{position:fixed;left:0;top:0;bottom:0;transform:translateX(-100%);transition:transform .3s;z-index:100}
      .sb.open{transform:translateX(0)}
      .mc{margin-left:0;padding:20px}
      .form-grid{grid-template-columns:1fr}
      .images-grid{grid-template-columns:repeat(2,1fr)}
      .prev-grid{grid-template-columns:repeat(2,1fr)}
    }
    @media(max-width:480px){
      .sb{width:100%}
      .card-hd{flex-direction:column;align-items:flex-start;gap:10px}
      .images-grid{grid-template-columns:1fr}
      .prev-grid{grid-template-columns:1fr}
      .form-actions{flex-direction:column}
      .form-actions button{width:100%}
    }
  </style>
</head>
<body>
  <aside class="sb">
    <div class="sb-logo"><div class="wm">Jolly Beauty</div><div class="tg">Administration</div></div>
    <nav class="sb-nav">
      <div class="ns">Principal</div>
      <a href="<?= $jbBase ?>/admin/index.php?page=dashboard" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span> Tableau de bord</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=orders" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 8h14M5 12h14M5 16h14"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg></span> Commandes</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=products" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12l4 6-10 13L2 9l4-6z"/></svg></span> Produits</a>
      <a href="<?= $jbBase ?>/admin/add-product.php" class="ni active"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg></span> Ajouter produit</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=users" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span> Clients</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=promo" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l9 4.9V17L12 22l-9-4.9V7z"/></svg></span> Codes promo</a>
      <div class="ns">Boutique</div>
      <a href="<?= $jbBase ?>/index.php" target="_blank" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span> Voir le site</a>
      <a href="<?= $jbBase ?>/category.php?c=all" target="_blank" class="ni"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></span> La boutique</a>
    </nav>
    <div class="sb-bot">
      <div class="sb-user"><div class="sb-av">A</div><div><div style="color:#fff;font-weight:600;font-size:.8rem">Admin</div><div>Jolly Beauty</div></div></div>
      <a href="<?= $jbBase ?>/admin/index.php?logout=1" class="ni" style="color:rgba(255,255,255,.45)"><span class="ic"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span> Déconnexion</a>
    </div>
  </aside>

  <main class="main">
    <div class="tb">
      <div class="tb-title">Ajouter un produit</div>
      <div class="tb-r">
        <a href="<?= $jbBase ?>/index.php" target="_blank" class="tbtn t-ghost"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>Voir le site</a>
        <a href="<?= $jbBase ?>/admin/index.php?page=products" class="tbtn t-rose">← Retour aux produits</a>
      </div>
    </div>

    <div class="ct">
      <div class="page">
        <?php if ($msg): ?>
          <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="grid">
          <div class="panel">
            <div class="panel-h">Informations principales</div>
            <div class="panel-b">
              <div class="fg">
                <label class="fl" for="name">Nom du produit *</label>
                <input class="fi" type="text" id="name" name="name" required placeholder="Ex: Bracelet Charms Éclat" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
              </div>

              <div class="fg category-selector">
                <label class="fl" for="category_id">Catégorie *</label>
                <div class="category-options">
                  <?php
                  $catIcons = [
                    'bijoux' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 13L2 9l4-6z"/><path d="M12 22L6 9h12l-6 13z"/><path d="M6 9l6-6 6 6"/></svg>',
                    'soins' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c4.97-4.97 4.97-13.03 0-18-4.97 4.97-4.97 13.03 0 18z"/><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg>',
                    'coffrets' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M12 8v13"/><path d="M19 12v-3a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0-3-3 3 3 0 0 0-3 3v3"/></svg>',
                    'produits' => '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>'
                  ];
                  $catColors = [
                    'bijoux' => '#FFD700',
                    'soins' => '#90EE90',
                    'coffrets' => '#FFB6C1',
                    'produits' => '#DDA0DD'
                  ];
                  $selectedCat = (int)($_POST['category_id'] ?? 0);
                  foreach ($categories as $c):
                    $icon = $catIcons[$c['slug']] ?? '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>';
                    $color = $catColors[$c['slug']] ?? '#DDA0DD';
                    $isSelected = ($selectedCat === (int)$c['id']);
                  ?>
                    <label class="category-card <?= $isSelected ? 'selected' : '' ?>" data-cat-id="<?= (int)$c['id'] ?>" data-cat-slug="<?= htmlspecialchars($c['slug']) ?>">
                      <input type="radio" name="category_id" value="<?= (int)$c['id'] ?>" <?= $isSelected ? 'checked' : '' ?> required>
                      <span class="cat-icon" style="background:<?= $color ?>20; color:<?= $color ?>"><?= $icon ?></span>
                      <span class="cat-name"><?= htmlspecialchars($c['name']) ?></span>
                      <span class="cat-slug"><?= htmlspecialchars($c['slug']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="fg subcategory-selector" id="subcategory-container">
                <label class="fl" for="sub">Sous-catégorie</label>
                <select class="fi" id="sub" name="sub">
                  <option value="">Choisir une sous-catégorie…</option>
                </select>
                <small class="sub-hint" style="color:var(--mu);font-size:.75rem;margin-top:4px;display:block;">
                  Sélectionnez d'abord une catégorie pour voir les sous-catégories disponibles
                </small>
              </div>

              <div class="fg">
                <label class="fl" for="short">Accroche courte</label>
                <input class="fi" type="text" id="short" name="short" placeholder="Une phrase courte pour résumer votre produit" value="<?= htmlspecialchars($_POST['short'] ?? '') ?>">
              </div>

              <div class="row2">
                <div class="fg">
                  <label class="fl" for="price">Prix (€) *</label>
                  <input class="fi" type="number" id="price" name="price" step="0.01" min="0.01" required placeholder="36.90" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                </div>
                <div class="fg">
                  <label class="fl" for="old_price">Ancien prix (€)</label>
                  <input class="fi" type="number" id="old_price" name="old_price" step="0.01" min="0" placeholder="(optionnel)" value="<?= htmlspecialchars($_POST['old_price'] ?? '') ?>">
                </div>
              </div>

              <div class="row2">
                <div class="fg">
                  <label class="fl" for="stock">Stock</label>
                  <input class="fi" type="number" id="stock" name="stock" min="0" placeholder="10" value="<?= htmlspecialchars($_POST['stock'] ?? '10') ?>">
                </div>
                <div class="fg">
                  <label class="fl" for="badge">Badge</label>
                  <input class="fi" type="text" id="badge" name="badge" placeholder="Best-seller, Nouveau…" value="<?= htmlspecialchars($_POST['badge'] ?? '') ?>">
                </div>
              </div>

              <div class="fg" style="margin-bottom:0">
                <label class="fl" for="description">Description complète</label>
                <textarea class="fa" id="description" name="description" placeholder="Description détaillée du produit…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
              </div>
            </div>
          </div>

          <div style="display:flex;flex-direction:column;gap:18px">
            <div class="panel">
              <div class="panel-h">Images du produit</div>
              <div class="panel-b">
                <div class="up" id="dropzone">
                  <input type="file" id="images" name="images[]" accept="image/*" multiple onchange="previewMultipleFiles(this.files)">
                  <div class="up-ico">🖼️</div>
                  <div class="up-t"><strong>Cliquer</strong> pour choisir<br><span style="font-size:.72rem">Plusieurs images possibles — JPG, PNG, WEBP, GIF — max 5 Mo par image</span></div>
                </div>
                <div id="previewContainer" style="margin-top:12px;display:none;">
                  <div style="font-size:.8rem;color:var(--muted);margin-bottom:8px;">Images sélectionnées :</div>
                  <div id="previewGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;"></div>
                </div>
              </div>
            </div>

            <div class="panel">
              <div class="panel-h">Options</div>
              <div class="panel-b">
                <div class="opt">
                  <label class="ck"><input type="checkbox" name="featured" <?= !empty($_POST['featured']) ? 'checked' : '' ?>> Mettre en avant (best-sellers)</label>
                  <label class="ck"><input type="checkbox" name="active" <?= isset($_POST['active']) ? 'checked' : 'checked' ?>> Visible sur le site</label>
                  <label class="ck"><input type="checkbox" name="gallery_only" <?= !empty($_POST['gallery_only']) ? 'checked' : '' ?>> Afficher uniquement dans la galerie</label>
                </div>
                <div class="submit">
                  <button type="submit" name="add_product" class="tbtn t-rose" style="padding:12px 22px">＋ Ajouter le produit</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script>
    // Sub-categories data by category
    const subcategoriesData = {
      'bijoux': [
        { value: '', label: '— Choisir une sous-catégorie —' },
        { value: 'bracelets', label: 'Bracelets' },
        { value: 'bagues', label: 'Bagues' },
        { value: 'colliers', label: 'Colliers' },
        { value: 'boucles', label: 'Boucles d\'oreilles' }
      ],
      'soins': [
        { value: '', label: '— Choisir une sous-catégorie —' },
        { value: 'visage', label: 'Soins visage' },
        { value: 'corps', label: 'Soins corps' },
        { value: 'cheveux', label: 'Soins cheveux' },
        { value: 'rituels', label: 'Rituels & accessoires' }
      ],
      'coffrets': [
        { value: '', label: '— Choisir une sous-catégorie —' },
        { value: 'bijoux', label: 'Coffrets bijoux' },
        { value: 'soins', label: 'Coffrets soins' },
        { value: 'mixtes', label: 'Coffrets mixtes' }
      ],
      'produits': [
        { value: '', label: '— Choisir une sous-catégorie —' },
        { value: 'nouveautes', label: 'Nouveautés' },
        { value: 'best-sellers', label: 'Best-sellers' },
        { value: 'promotions', label: 'Promotions' }
      ]
    };

    // Category selection handling
    const categoryCards = document.querySelectorAll('.category-card');
    const subSelect = document.getElementById('sub');
    const subHint = document.querySelector('.sub-hint');
    const subContainer = document.getElementById('subcategory-container');

    function updateSubcategories(catSlug) {
      const subs = subcategoriesData[catSlug] || [{ value: '', label: '— Aucune sous-catégorie —' }];

      subSelect.innerHTML = '';
      subs.forEach(sub => {
        const option = document.createElement('option');
        option.value = sub.value;
        option.textContent = sub.label;
        if (sub.value === '') option.disabled = true;
        subSelect.appendChild(option);
      });

      // Show subcategory selector
      subContainer.style.opacity = '1';
      subContainer.style.pointerEvents = 'auto';
      subHint.textContent = 'Sous-catégories disponibles pour ' + catSlug;
      subHint.style.color = 'var(--rd)';
    }

    categoryCards.forEach(card => {
      card.addEventListener('click', () => {
        // Remove selected from all
        categoryCards.forEach(c => c.classList.remove('selected'));
        // Add selected to clicked
        card.classList.add('selected');
        // Check the radio
        const radio = card.querySelector('input[type="radio"]');
        radio.checked = true;

        // Update subcategories
        const catSlug = card.dataset.catSlug;
        updateSubcategories(catSlug);
      });
    });

    // Restore subcategory if form was submitted with errors
    const selectedCat = document.querySelector('.category-card.selected');
    if (selectedCat) {
      updateSubcategories(selectedCat.dataset.catSlug);
      // Restore selected subcategory
      const savedSub = '<?= htmlspecialchars($_POST['sub'] ?? '') ?>';
      if (savedSub) {
        setTimeout(() => {
          subSelect.value = savedSub;
        }, 0);
      }
    }

    // File upload handling
    const dz = document.getElementById('dropzone');
    const previewContainer = document.getElementById('previewContainer');
    const previewGrid = document.getElementById('previewGrid');

    function previewMultipleFiles(files) {
      if (!files || files.length === 0) {
        previewContainer.style.display = 'none';
        previewGrid.innerHTML = '';
        return;
      }

      previewContainer.style.display = 'block';
      previewGrid.innerHTML = '';

      Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          const previewItem = document.createElement('div');
          previewItem.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1;';

          const img = document.createElement('img');
          img.src = e.target.result;
          img.style.cssText = 'width:100%;height:100%;object-fit:cover;';

          const info = document.createElement('div');
          info.style.cssText = 'position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.7);color:white;padding:4px;font-size:.7rem;text-align:center;';
          info.textContent = `${file.name} (${Math.round(file.size/1024)}Ko)`;

          previewItem.appendChild(img);
          previewItem.appendChild(info);
          previewGrid.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
      });
    }

    ['dragenter','dragover'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('drag'); }));
    ['dragleave','drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('drag'); }));
    dz.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files && files.length > 0) {
        const input = document.getElementById('images');
        input.files = files;
        previewMultipleFiles(files);
      }
    });
  </script>
</body>
</html>
