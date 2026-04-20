<?php
// Script de test pour vérifier l'intégration complète du site
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test d'intégration Jolly Beauty</h1>";

// Test 1: Configuration de base
echo "<h2>Test 1: Configuration</h2>";
try {
    require_once __DIR__ . '/includes/config.php';
    echo "config.php: OK<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
} catch (Exception $e) {
    echo "config.php: ERREUR - " . $e->getMessage() . "<br>";
}

// Test 2: Base de données
echo "<h2>Test 2: Base de données</h2>";
try {
    $db = getDB();
    echo "Connexion BDD: OK<br>";
    
    // Vérifier les tables
    $tables = ['products', 'categories', 'orders', 'order_items', 'users'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table $table: OK<br>";
        } else {
            echo "Table $table: MANQUANTE<br>";
        }
    }
    
    // Vérifier les colonnes Stripe
    $stmt = $db->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $stripeColumns = ['stripe_session_id', 'stripe_payment_intent_id', 'paid_at'];
    foreach ($stripeColumns as $col) {
        if (in_array($col, $columns)) {
            echo "Colonne $col: OK<br>";
        } else {
            echo "Colonne $col: MANQUANTE<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Base de données: ERREUR - " . $e->getMessage() . "<br>";
}

// Test 3: Fichiers de pages
echo "<h2>Test 3: Pages principales</h2>";
$pages = [
    'index.php' => 'pages/index.php',
    'category.php' => 'pages/category.php',
    'product.php' => 'pages/product.php',
    'checkout.php' => 'pages/checkout.php',
    'contact.php' => 'pages/contact.php',
    'login.php' => 'pages/login.php'
];

foreach ($pages as $public => $internal) {
    if (file_exists(__DIR__ . '/' . $public)) {
        echo "$public: OK<br>";
    } else {
        echo "$public: MANQUANT<br>";
    }
    
    if (file_exists(__DIR__ . '/' . $internal)) {
        echo "$internal: OK<br>";
    } else {
        echo "$internal: MANQUANT<br>";
    }
}

// Test 4: API Stripe
echo "<h2>Test 4: API Stripe</h2>";
try {
    require_once __DIR__ . '/includes/stripe-config.php';
    echo "stripe-config.php: OK<br>";
    
    $key = getStripeSecretKey();
    echo "Clé Stripe: " . substr($key, 0, 10) . "...<br>";
    
    if (file_exists(__DIR__ . '/api/stripe-create-session.php')) {
        echo "API Stripe: OK<br>";
    } else {
        echo "API Stripe: MANQUANTE<br>";
    }
    
} catch (Exception $e) {
    echo "Stripe: ERREUR - " . $e->getMessage() . "<br>";
}

// Test 5: Pages de paiement
echo "<h2>Test 5: Pages de paiement</h2>";
$paymentPages = [
    'payment-success.php' => 'payments/payment-success.php',
    'payment-cancelled.php' => 'payments/payment-cancelled.php'
];

foreach ($paymentPages as $public => $internal) {
    if (file_exists(__DIR__ . '/' . $public)) {
        echo "$public: OK<br>";
    } else {
        echo "$public: MANQUANT<br>";
    }
    
    if (file_exists(__DIR__ . '/' . $internal)) {
        echo "$internal: OK<br>";
    } else {
        echo "$internal: MANQUANTE<br>";
    }
}

// Test 6: Assets
echo "<h2>Test 6: Assets</h2>";
$assets = [
    'assets/css/style.css',
    'assets/js/script.js',
    'assets/images'
];

foreach ($assets as $asset) {
    if (file_exists(__DIR__ . '/' . $asset)) {
        echo "$asset: OK<br>";
    } else {
        echo "$asset: MANQUANT<br>";
    }
}

// Test 7: Pages de catégories
echo "<h2>Test 7: Pages de catégories</h2>";
$categoryPages = ['bijoux.php', 'coffrets.php', 'soins-rituels.php', 'rituels.php'];
foreach ($categoryPages as $page) {
    if (file_exists(__DIR__ . '/' . $page)) {
        echo "$page: OK<br>";
    } else {
        echo "$page: MANQUANT<br>";
    }
}

echo "<h2>Conclusion</h2>";
echo "<p>Test terminé. Vérifiez les résultats ci-dessus.</p>";
echo "<p><a href='pages/index.php'>Aller à l'accueil</a></p>";
echo "<p><a href='pages/checkout.php'>Aller au checkout</a></p>";
?>
