<?php
// Fichier de test pour diagnostiquer l'erreur Stripe
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Stripe Integration</h1>";

// Test 1: Vérifier les includes
echo "<h2>Test 1: Configuration</h2>";
try {
    require_once __DIR__ . '/../includes/config.php';
    echo "config.php: OK<br>";
} catch (Exception $e) {
    echo "config.php: ERREUR - " . $e->getMessage() . "<br>";
}

try {
    require_once __DIR__ . '/../includes/stripe-config.php';
    echo "stripe-config.php: OK<br>";
} catch (Exception $e) {
    echo "stripe-config.php: ERREUR - " . $e->getMessage() . "<br>";
}

// Test 2: Vérifier la clé Stripe
echo "<h2>Test 2: Clé Stripe</h2>";
$stripeKey = getStripeSecretKey();
echo "Clé secrète: " . substr($stripeKey, 0, 10) . "..." . "<br>";

// Test 3: Vérifier la base de données
echo "<h2>Test 3: Base de données</h2>";
try {
    $db = getDB();
    echo "Connexion BDD: OK<br>";
    
    // Vérifier la table orders
    $stmt = $db->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Colonnes orders: " . implode(", ", $columns) . "<br>";
    
    if (!in_array('stripe_session_id', $columns)) {
        echo "ATTENTION: La colonne 'stripe_session_id' n'existe pas dans la table orders<br>";
    }
    
} catch (Exception $e) {
    echo "Connexion BDD: ERREUR - " . $e->getMessage() . "<br>";
}

// Test 4: Vérifier le panier
echo "<h2>Test 4: Session panier</h2>";
session_start();
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    echo "Panier: " . count($_SESSION['cart']) . " articles<br>";
    foreach ($_SESSION['cart'] as $item) {
        echo "- " . ($item['name'] ?? 'Sans nom') . " (" . ($item['price'] ?? 0) . " x " . ($item['qty'] ?? 1) . ")<br>";
    }
} else {
    echo "Panier: VIDE<br>";
}

// Test 5: Appel API Stripe simple
echo "<h2>Test 5: Appel API Stripe</h2>";
try {
    $ch = curl_init('https://api.stripe.com/v1/account');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripeKey,
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "API Stripe: ERREUR cURL - " . $error . "<br>";
    } elseif ($httpCode === 200) {
        echo "API Stripe: OK (HTTP 200)<br>";
        $account = json_decode($response, true);
        echo "Compte: " . ($account['display_name'] ?? 'Inconnu') . "<br>";
    } else {
        echo "API Stripe: ERREUR HTTP " . $httpCode . "<br>";
        echo "Réponse: " . substr($response, 0, 200) . "...<br>";
    }
} catch (Exception $e) {
    echo "API Stripe: ERREUR - " . $e->getMessage() . "<br>";
}

// Test 6: Simulation de création de session
echo "<h2>Test 6: Simulation création session</h2>";
try {
    // Données de test
    $testOrderData = [
        'id' => 999,
        'order_ref' => '#TEST123',
        'discount' => 0,
        'promo_code' => ''
    ];
    
    $testCartItems = [
        [
            'name' => 'Produit test',
            'price' => 29.90,
            'qty' => 1,
            'description' => 'Description test'
        ]
    ];
    
    $testCustomerInfo = [
        'email' => 'test@example.com',
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '0123456789',
        'address' => '123 rue test',
        'city' => 'Paris',
        'zip' => '75001',
        'country' => 'France'
    ];
    
    echo "Données de test préparées<br>";
    
    // Tenter de créer la session
    $sessionData = createStripeCheckoutSession($testOrderData, $testCartItems, $testCustomerInfo);
    echo "Session Stripe: OK<br>";
    echo "Session ID: " . $sessionData['id'] . "<br>";
    echo "Checkout URL: " . $sessionData['url'] . "<br>";
    
} catch (Exception $e) {
    echo "Session Stripe: ERREUR - " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Conclusion</h2>";
echo "<p>Consultez les résultats ci-dessus pour identifier le problème.</p>";
echo "<p><a href='../checkout.php'>Retour au checkout</a></p>";
?>
