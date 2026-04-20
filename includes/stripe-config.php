<?php
// Configuration sécurisée pour Stripe
// Ce fichier doit être protégé et ne doit jamais être exposé côté client

// Clés Stripe (à remplacer par vos vraies clés)
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_51PFgoYI099mQIcDd9VL1ObQ57chdKFGAG3zzpwU4sSTcd9mhCQALKuhsiAAYfrkLxUWq7T15CRu6hcFs4dt4G6Bk00hQVjoy1m');
define('STRIPE_SECRET_KEY', 'plyc-vsbe-xwxq-cthh-tepk');

// Configuration de l'environnement
define('STRIPE_MODE', 'live'); // 'live' pour production, 'test' pour développement

// URLs de redirection
define('STRIPE_SUCCESS_URL', BASE_URL . '/payments/payment-success.php');
define('STRIPE_CANCEL_URL', BASE_URL . '/payments/payment-cancelled.php');

// Pays autorisés pour la livraison
$stripe_allowed_countries = ['FR', 'BE', 'CH', 'CA', 'SN', 'MA'];

// Fonction pour obtenir la clé publique (sécurisée)
function getStripePublishableKey() {
    return STRIPE_PUBLISHABLE_KEY;
}

// Fonction pour obtenir la clé secrète (sécurisée)
function getStripeSecretKey() {
    return STRIPE_SECRET_KEY;
}

// Fonction pour vérifier la signature webhook (si nécessaire)
function verifyStripeWebhook($payload, $sig_header, $secret) {
    // Implémentation future pour les webhooks
    return true;
}

// Fonction pour créer une session Stripe Checkout
function createStripeCheckoutSession($orderData, $cartItems, $customerInfo) {
    $stripeSecretKey = getStripeSecretKey();
    
    // Préparer les line items pour Stripe
    $lineItems = [];
    $subtotal = 0;
    
    foreach ($cartItems as $item) {
        $qty = (int)($item['qty'] ?? 1);
        $price = (float)($item['price'] ?? 0);
        $subtotal += $price * $qty;
        
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => (string)($item['name'] ?? 'Produit'),
                    'description' => (string)($item['description'] ?? ''),
                ],
                'unit_amount' => (int)round($price * 100), // en centimes
            ],
            'quantity' => $qty,
        ];
    }
    
    // Calculer la livraison
    $shipping = $subtotal >= 60 ? 0 : 5.90;
    
    // Ajouter la livraison si payante
    if ($shipping > 0) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Livraison',
                    'description' => 'Livraison standard',
                ],
                'unit_amount' => (int)round($shipping * 100),
            ],
            'quantity' => 1,
        ];
    }
    
    // Appliquer la réduction si applicable
    if (!empty($orderData['discount']) && $orderData['discount'] > 0) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Réduction',
                    'description' => 'Code promo: ' . ($orderData['promo_code'] ?? ''),
                ],
                'unit_amount' => -(int)round($orderData['discount'] * 100),
            ],
            'quantity' => 1,
        ];
    }
    
    // Créer la session Stripe Checkout
    $checkoutSessionData = [
        'payment_method_types' => ['card', 'apple_pay', 'google_pay', 'paypal'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $orderData['id'],
        'cancel_url' => STRIPE_CANCEL_URL . '?order_id=' . $orderData['id'],
        'customer_email' => $customerInfo['email'],
        'metadata' => [
            'order_id' => $orderData['id'],
            'order_ref' => $orderData['order_ref'],
        ],
        'billing_address_collection' => 'required',
        'shipping_address_collection' => [
            'allowed_countries' => $GLOBALS['stripe_allowed_countries'] ?? ['FR', 'BE', 'CH', 'CA', 'SN', 'MA'],
        ],
    ];
    
    // Appel à l'API Stripe
    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($checkoutSessionData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeSecretKey,
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception('Erreur cURL: ' . $error);
    }
    
    if ($httpCode !== 200) {
        error_log('Stripe API Error (' . $httpCode . '): ' . $response);
        throw new Exception('Erreur API Stripe: ' . $response);
    }
    
    $sessionData = json_decode($response, true);
    
    if (!$sessionData || !isset($sessionData['id'])) {
        throw new Exception('Réponse invalide de l\'API Stripe');
    }
    
    return $sessionData;
}

// Fonction pour vérifier une session Stripe
function verifyStripeSession($sessionId) {
    $stripeSecretKey = getStripeSecretKey();
    
    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeSecretKey,
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception('Erreur cURL: ' . $error);
    }
    
    if ($httpCode !== 200) {
        error_log('Stripe API Error (' . $httpCode . '): ' . $response);
        throw new Exception('Erreur API Stripe: ' . $response);
    }
    
    return json_decode($response, true);
}
?>
