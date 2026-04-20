<?php
// Script de test rapide pour vérifier que toutes les pages fonctionnent
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test des pages Jolly Beauty</h1>";

$pages = [
    'index.php' => 'Accueil',
    'category.php' => 'Catégories',
    'product.php' => 'Produit',
    'checkout.php' => 'Panier',
    'contact.php' => 'Contact',
    'login.php' => 'Connexion',
    'notre-histoire.php' => 'Notre histoire',
    'media-gallery.php' => 'Galerie',
    'payment-success.php' => 'Paiement réussi',
    'payment-cancelled.php' => 'Paiement annulé'
];

$categoryPages = [
    'bijoux.php' => 'Bijoux',
    'coffrets.php' => 'Coffrets',
    'soins-rituels.php' => 'Soins & Rituels',
    'rituels.php' => 'Rituels'
];

echo "<h2>Pages principales</h2>";
foreach ($pages as $file => $name) {
    try {
        // Test d'inclusion pour vérifier les erreurs fatales
        ob_start();
        include_once $file;
        ob_end_clean();
        echo "<span style='color:green'>{$name}: OK</span><br>";
    } catch (ParseError $e) {
        echo "<span style='color:red'>{$name}: ERREUR SYNTAXE - {$e->getMessage()}</span><br>";
    } catch (Error $e) {
        echo "<span style='color:orange'>{$name}: ERREUR - {$e->getMessage()}</span><br>";
    } catch (Exception $e) {
        echo "<span style='color:orange'>{$name}: EXCEPTION - {$e->getMessage()}</span><br>";
    }
}

echo "<h2>Pages de catégories</h2>";
foreach ($categoryPages as $file => $name) {
    try {
        ob_start();
        include_once $file;
        ob_end_clean();
        echo "<span style='color:green'>{$name}: OK</span><br>";
    } catch (ParseError $e) {
        echo "<span style='color:red'>{$name}: ERREUR SYNTAXE - {$e->getMessage()}</span><br>";
    } catch (Error $e) {
        echo "<span style='color:orange'>{$name}: ERREUR - {$e->getMessage()}</span><br>";
    } catch (Exception $e) {
        echo "<span style='color:orange'>{$name}: EXCEPTION - {$e->getMessage()}</span><br>";
    }
}

echo "<h2>Test des chemins d'assets</h2>";
$assets = [
    'assets/css/style.css',
    'assets/js/script.js'
];

foreach ($assets as $asset) {
    if (file_exists($asset)) {
        echo "<span style='color:green'>{$asset}: OK</span><br>";
    } else {
        echo "<span style='color:red'>{$asset}: MANQUANT</span><br>";
    }
}

echo "<h2>Liens rapides</h2>";
echo "<p><a href='index.php'>Accueil</a></p>";
echo "<p><a href='category.php?c=all'>Toute la collection</a></p>";
echo "<p><a href='bijoux.php'>Bijoux</a></p>";
echo "<p><a href='checkout.php'>Panier</a></p>";
echo "<p><a href='test-integration.php'>Test d'intégration complet</a></p>";
?>
