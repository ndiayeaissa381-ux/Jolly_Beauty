<?php
// ============================================================
// JOLLY BEAUTY — Configuration & Connexion Base de Données
// ============================================================
// Modifiez uniquement les constantes DB_* selon votre XAMPP

define('DB_HOST', 'localhost');
define('DB_NAME', 'jollybeauty');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP : vide par défaut
define('DB_PORT', 3306);

// ── ADMIN ────────────────────────────────────────────────────
define('ADMIN_USER', 'admin');
// Mot de passe admin (changez en production !)
define('ADMIN_PASS_PLAIN', 'JollyBeauty2025!');

// ── APP ──────────────────────────────────────────────────────
define('APP_URL', 'http://localhost/Jolly_Beauty');
define('BASE_URL', '/Jolly_Beauty');;
define('APP_NAME', 'Jolly Beauty');

// ── SESSION ──────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── PDO CONNECTION ───────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST, DB_PORT, DB_NAME);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // En production, logguez l'erreur et affichez une page d'erreur
            die('<div style="font-family:sans-serif;padding:40px;text-align:center">
                <h2 style="color:#C97070">⚠ Connexion base de données impossible</h2>
                <p>Vérifiez que XAMPP est démarré et que la base <strong>jollybeauty</strong> existe.<br>
                Importez <code>database.sql</code> via phpMyAdmin.</p>
                <small style="color:#888">' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }
    return $pdo;
}

// ── HELPERS AUTH ─────────────────────────────────────────────
function isLoggedIn(): bool  { return !empty($_SESSION['jb_user']); }
function isAdmin(): bool     { return !empty($_SESSION['jb_admin']); }
function currentUser(): ?array { return $_SESSION['jb_user'] ?? null; }

// ── HELPERS FORMATAGE ────────────────────────────────────────
function formatPrice(float $p): string {
    return number_format($p, 2, ',', ' ') . ' €';
}

function activeClass(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return $current === $page ? 'active' : '';
}

function sanitize(string $s): string {
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

// ── FONCTIONS PRODUITS ───────────────────────────────────────
function getProducts(?string $cat = null, string $query = '', string $sort = 'default', int $limit = 100): array {
    $db  = getDB();
    $sql = "SELECT p.*, c.slug AS category,
                   GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list,
                   GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '||') AS mat_list,
                   GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '||') AS size_list
            FROM products p
            JOIN categories c ON c.id = p.category_id
            LEFT JOIN product_images pi ON pi.product_id = p.id
            LEFT JOIN product_materials pm ON pm.product_id = p.id
            LEFT JOIN product_sizes ps ON ps.product_id = p.id
            WHERE p.active = 1";
    $params = [];

    if ($cat && $cat !== 'all') {
        $sql    .= ' AND c.slug = ?';
        $params[] = $cat;
    }
    if ($query !== '') {
        $sql    .= ' AND (p.name LIKE ? OR p.short LIKE ?)';
        $like    = "%{$query}%";
        $params[] = $like;
        $params[] = $like;
    }
    $sql .= ' GROUP BY p.id';
    if ($sort === 'price_asc')  $sql .= ' ORDER BY p.price ASC';
    elseif ($sort === 'price_desc') $sql .= ' ORDER BY p.price DESC';
    elseif ($sort === 'newest')  $sql .= ' ORDER BY p.id DESC';
    else $sql .= ' ORDER BY p.featured DESC, p.id ASC';
    $sql .= " LIMIT $limit";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return array_map('_hydrateProduct', $rows);
}

function getProductBySlug(string $slug): ?array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.slug AS category,
                GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list,
                GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '||') AS mat_list,
                GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '||') AS size_list
         FROM products p
         JOIN categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id
         LEFT JOIN product_materials pm ON pm.product_id = p.id
         LEFT JOIN product_sizes ps ON ps.product_id = p.id
         WHERE p.slug = ? AND p.active = 1
         GROUP BY p.id LIMIT 1"
    );
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ? _hydrateProduct($row) : null;
}

function getFeaturedProducts(): array {
    $db   = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.slug AS category,
                GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '||') AS img_list,
                GROUP_CONCAT(DISTINCT pm.value ORDER BY pm.sort_order SEPARATOR '||') AS mat_list,
                GROUP_CONCAT(DISTINCT ps.value ORDER BY ps.sort_order SEPARATOR '||') AS size_list
         FROM products p
         JOIN categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id
         LEFT JOIN product_materials pm ON pm.product_id = p.id
         LEFT JOIN product_sizes ps ON ps.product_id = p.id
         WHERE p.featured = 1 AND p.active = 1
         GROUP BY p.id ORDER BY p.id ASC"
    );
    $stmt->execute();
    return array_map('_hydrateProduct', $stmt->fetchAll());
}

function _hydrateProduct(array $row): array {
    $row['images']    = $row['img_list']  ? explode('||', $row['img_list'])  : [];
    $row['materials'] = $row['mat_list']  ? explode('||', $row['mat_list'])  : [];
    $row['sizes']     = $row['size_list'] ? explode('||', $row['size_list']) : [];
    $row['price']     = (float) $row['price'];
    $row['old_price'] = $row['old_price'] ? (float) $row['old_price'] : null;
    unset($row['img_list'], $row['mat_list'], $row['size_list']);
    return $row;
}

// ── FONCTIONS UTILISATEURS ───────────────────────────────────
function getUserByEmail(string $email): ?array {
    $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    return $stmt->fetch() ?: null;
}

function createUser(string $name, string $email, string $password): int {
    $stmt = getDB()->prepare(
        'INSERT INTO users (name,email,password,promo_code) VALUES (?,?,?,?)'
    );
    $stmt->execute([
        trim($name),
        strtolower(trim($email)),
        password_hash($password, PASSWORD_BCRYPT),
        'BIENVENUE10',
    ]);
    return (int) getDB()->lastInsertId();
}

// ── COMMANDES ────────────────────────────────────────────────
function getOrders(int $limit = 20, int $offset = 0): array {
    $stmt = getDB()->prepare(
        "SELECT o.*, COALESCE(u.name, o.guest_name) AS client_name
         FROM orders o
         LEFT JOIN users u ON u.id = o.user_id
         ORDER BY o.created_at DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

function countOrders(): int {
    return (int) getDB()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
}

function getTotalRevenue(): float {
    return (float) getDB()->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
}

// ── PROMO ────────────────────────────────────────────────────
function validatePromoCode(string $code): ?array {
    $stmt = getDB()->prepare(
        "SELECT * FROM promo_codes
         WHERE code = ? AND active = 1
           AND (max_uses IS NULL OR used_count < max_uses)
           AND (expires_at IS NULL OR expires_at > NOW())
         LIMIT 1"
    );
    $stmt->execute([strtoupper($code)]);
    return $stmt->fetch() ?: null;
}

// ── NEWSLETTER ───────────────────────────────────────────────
function subscribeNewsletter(string $email): bool {
    try {
        $stmt = getDB()->prepare(
            'INSERT INTO newsletter (email) VALUES (?)
             ON DUPLICATE KEY UPDATE subscribed = 1'
        );
        return $stmt->execute([$email]);
    } catch (PDOException $e) {
        return false;
    }
}

// ── FALLBACK si DB non dispo (mode statique) ─────────────────
// Garde les données statiques comme fallback en cas d'erreur DB
function getProductsFallback(): array {
    return [
        ['id'=>'p1','slug'=>'bracelet-charms-eclat','category'=>'bijoux','sub'=>'Bracelets',
         'name'=>'Bracelet Charms Éclat','short'=>'Plaqué or 18k, charms symboliques, finition miroir.',
         'description'=>'Le Bracelet Charms Éclat est la pièce signature de Jolly Beauty.','price'=>36.90,'old_price'=>null,
         'badge'=>'Best-seller','rating'=>5,'reviews'=>142,'stock'=>24,'featured'=>true,
         'images'=>['https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=800&q=80'],
         'materials'=>['Acier inoxydable plaqué or 18k'],'sizes'=>['S — 16 cm','M — 18 cm','L — 20 cm']],
    ];
}