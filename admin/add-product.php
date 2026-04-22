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
                'INSERT INTO products (name,slug,category_id,sub,short,description,price,old_price,badge,stock,featured,active)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([$name,$slug,$categoryId,$sub,$short,$description,$price,$oldPrice,$badge,$stock,$featured,$active]);
            $productId = (int)$db->lastInsertId();

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp','gif'], true)) {
                    $filename = $slug . '-' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                        $url = BASE_URL . '/assets/images/uploads/' . $filename;
                        $imgStmt = $db->prepare('INSERT INTO product_images (product_id,url,sort_order) VALUES (?,?,0)');
                        $imgStmt->execute([$productId, $url]);
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    .ni .ic{font-size:.95rem;width:19px;text-align:center;flex-shrink:0;opacity:.95}
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
  </style>
</head>
<body>
  <aside class="sb">
    <div class="sb-logo"><div class="wm">Jolly Beauty</div><div class="tg">Administration</div></div>
    <nav class="sb-nav">
      <div class="ns">Principal</div>
      <a href="<?= $jbBase ?>/admin/index.php?page=dashboard" class="ni"><span class="ic">▣</span> Tableau de bord</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=orders" class="ni"><span class="ic">📦</span> Commandes</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=products" class="ni"><span class="ic">💎</span> Produits</a>
      <a href="<?= $jbBase ?>/admin/add-product.php" class="ni active"><span class="ic">＋</span> Ajouter produit</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=users" class="ni"><span class="ic">👥</span> Clients</a>
      <a href="<?= $jbBase ?>/admin/index.php?page=promo" class="ni"><span class="ic">🏷</span> Codes promo</a>
      <div class="ns">Boutique</div>
      <a href="<?= $jbBase ?>/index.php" target="_blank" class="ni"><span class="ic">🌐</span> Voir le site</a>
      <a href="<?= $jbBase ?>/category.php?c=all" target="_blank" class="ni"><span class="ic">🛍</span> La boutique</a>
    </nav>
    <div class="sb-bot">
      <div class="sb-user"><div class="sb-av">A</div><div><div style="color:#fff;font-weight:600;font-size:.8rem">Admin</div><div>Jolly Beauty</div></div></div>
      <a href="<?= $jbBase ?>/admin/index.php?logout=1" class="ni" style="color:rgba(255,255,255,.45)"><span class="ic">🚪</span> Déconnexion</a>
    </div>
  </aside>

  <main class="main">
    <div class="tb">
      <div class="tb-title">Ajouter un produit</div>
      <div class="tb-r">
        <a href="<?= $jbBase ?>/index.php" target="_blank" class="tbtn t-ghost">🌐 Voir le site</a>
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

              <div class="row2">
                <div class="fg">
                  <label class="fl" for="category_id">Catégorie *</label>
                  <select id="category_id" name="category_id" required class="fi">
                    <option value="">Choisir…</option>
                    <?php foreach ($categories as $c): ?>
                      <option value="<?= (int)$c['id'] ?>" <?= ((int)($_POST['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="fg">
                  <label class="fl" for="sub">Sous-catégorie</label>
                  <input class="fi" type="text" id="sub" name="sub" placeholder="Ex: Bracelets" value="<?= htmlspecialchars($_POST['sub'] ?? '') ?>">
                </div>
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
              <div class="panel-h">Image du produit</div>
              <div class="panel-b">
                <div class="up" id="dropzone">
                  <input type="file" id="image" name="image" accept="image/*" onchange="previewFile(this.files && this.files[0])">
                  <div class="up-ico">🖼️</div>
                  <div class="up-t"><strong>Cliquer</strong> pour choisir<br><span style="font-size:.72rem">JPG, PNG, WEBP, GIF — max 5 Mo</span></div>
                </div>
                <div class="prev" id="previewRow">
                  <img id="imgPreview" alt="Aperçu">
                  <div class="meta">
                    <div class="n" id="imgName">Image</div>
                    <div class="s" id="imgSize">—</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="panel">
              <div class="panel-h">Options</div>
              <div class="panel-b">
                <div class="opt">
                  <label class="ck"><input type="checkbox" name="featured" <?= !empty($_POST['featured']) ? 'checked' : '' ?>> Mettre en avant (best-sellers)</label>
                  <label class="ck"><input type="checkbox" name="active" <?= isset($_POST['active']) ? 'checked' : 'checked' ?>> Visible sur le site</label>
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
    const dz = document.getElementById('dropzone');
    const previewRow = document.getElementById('previewRow');
    const imgPreview = document.getElementById('imgPreview');
    const imgName = document.getElementById('imgName');
    const imgSize = document.getElementById('imgSize');

    function previewFile(file) {
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        imgPreview.src = e.target.result;
        previewRow.style.display = 'flex';
      };
      reader.readAsDataURL(file);
      imgName.textContent = file.name || 'Image';
      imgSize.textContent = Math.round((file.size || 0) / 1024) + ' Ko';
    }

    ['dragenter','dragover'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('drag'); }));
    ['dragleave','drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('drag'); }));
    dz.addEventListener('drop', (e) => {
      const f = e.dataTransfer.files && e.dataTransfer.files[0];
      if (f) {
        const input = document.getElementById('image');
        input.files = e.dataTransfer.files;
        previewFile(f);
      }
    });
  </script>
</body>
</html>
