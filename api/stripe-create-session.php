<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/stripe-config.php';

header('Content-Type: application/json');

// Seulement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données du panier
$cartItems = [];
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItems = array_values(array_filter($_SESSION['cart'], 'is_array'));
    foreach ($cartItems as $i => $item) {
        if (!isset($item['qty']) && isset($item['quantity'])) {
            $cartItems[$i]['qty'] = (int)$item['quantity'];
        }
        $cartItems[$i]['qty'] = max(1, (int)($cartItems[$i]['qty'] ?? 1));
    }
}

if (empty($cartItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'Panier vide']);
    exit;
}

// Récupérer les données du formulaire
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

$firstName = sanitize($data['first_name'] ?? '');
$lastName = sanitize($data['last_name'] ?? '');
$email = sanitize($data['email'] ?? '');
$phone = sanitize($data['phone'] ?? '');
$address = sanitize($data['address'] ?? '');
$city = sanitize($data['city'] ?? '');
$zip = sanitize($data['zip'] ?? '');
$country = sanitize($data['country'] ?? 'France');
$promoCode = sanitize($data['promo_code'] ?? '');

// Validation
if (!$firstName || !$lastName || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$address || !$city || !$zip) {
    http_response_code(400);
    echo json_encode(['error' => 'Informations de livraison incomplètes']);
    exit;
}

// Calculer les totaux
$subtotal = 0;
foreach ($cartItems as $item) {
    $q = (int)($item['qty'] ?? 1);
    $subtotal += (float)($item['price'] ?? 0) * $q;
}

$discount = 0.0;
if ($promoCode) {
    $promo = validatePromoCode($promoCode, $subtotal);
    if ($promo) {
        $discount = $promo['discount_type'] === 'percent'
            ? $subtotal * ((float)$promo['discount_value'] / 100)
            : (float)$promo['discount_value'];
    }
}

$shipping = $subtotal >= 60 ? 0 : 5.90;
$total = max(0.0, $subtotal - $discount) + $shipping;

// Créer la commande en BDD (statut pending_payment)
try {
    $db = getDB();
    $orderRef = '#JB' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    $userId = isLoggedIn() ? (int)currentUser()['id'] : null;
    $guestName = trim($firstName . ' ' . $lastName);
    $addrLine = $address . ($country ? ' — ' . $country : '');

    $stmt = $db->prepare('
        INSERT INTO orders (order_ref, user_id, guest_email, guest_name, total, status,
            shipping_name, shipping_addr, shipping_city, shipping_zip, promo_code, discount, notes)
        VALUES (?, ?, ?, ?, ?, \'pending_payment\', ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $orderRef,
        $userId,
        $email,
        $guestName,
        round($total, 2),
        $guestName,
        $addrLine,
        $city,
        $zip,
        $promoCode !== '' ? $promoCode : null,
        round($discount, 2),
        $phone !== '' ? 'Tél: ' . $phone : null,
    ]);
    $orderId = (int)$db->lastInsertId();

    // Sauvegarder les items de commande
    foreach ($cartItems as $item) {
        $pid = isset($item['id']) && is_numeric($item['id']) ? (int)$item['id'] : null;
        $stmtItem = $db->prepare('
            INSERT INTO order_items (order_id, product_id, name, price, qty)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmtItem->execute([
            $orderId,
            $pid,
            (string)($item['name'] ?? ''),
            round((float)($item['price'] ?? 0), 2),
            (int)($item['qty'] ?? 1),
        ]);
    }

    // Préparer les données pour la session Stripe
    $orderData = [
        'id' => $orderId,
        'order_ref' => $orderRef,
        'discount' => $discount,
        'promo_code' => $promoCode
    ];
    
    $customerInfo = [
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'zip' => $zip,
        'country' => $country
    ];

    try {
        // Créer la session Stripe Checkout en utilisant la fonction sécurisée
        $sessionData = createStripeCheckoutSession($orderData, $cartItems, $customerInfo);
        
        // Mettre à jour la commande avec l'ID de session Stripe
        $stmt = $db->prepare('UPDATE orders SET stripe_session_id = ? WHERE id = ?');
        $stmt->execute([$sessionData['id'], $orderId]);

        echo json_encode([
            'success' => true,
            'session_id' => $sessionData['id'],
            'checkout_url' => $sessionData['url'],
            'order_id' => $orderId,
        ]);
        
    } catch (Exception $e) {
        error_log('Stripe session creation error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la création de la session de paiement: ' . $e->getMessage()]);
    }

} catch (Exception $e) {
    error_log('Stripe session creation error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
