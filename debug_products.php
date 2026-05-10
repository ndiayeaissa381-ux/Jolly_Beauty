<?php
require_once 'includes/config.php';

echo "<h2>Diagnostic des produits</h2>";

try {
    $db = getDB();
    
    // Vérifier si la table products existe
    $stmt = $db->prepare("SHOW TABLES LIKE 'products'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p style='color: red;'>❌ La table 'products' n'existe pas!</p>";
        echo "<p>Veuillez importer le fichier database.sql via phpMyAdmin.</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ La table 'products' existe.</p>";
    
    // Compter les produits
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM products");
    $stmt->execute();
    $count = $stmt->fetch()['count'];
    
    echo "<p>Nombre de produits dans la base: <strong>" . $count . "</strong></p>";
    
    if ($count == 0) {
        echo "<p style='color: orange;'>⚠️ Aucun produit dans la base de données.</p>";
        echo "<p>C'est pourquoi les liens produits ne fonctionnent pas.</p>";
        echo "<p>Solution: Ajoutez des produits via phpMyAdmin ou utilisez les données mock.</p>";
    } else {
        // Afficher quelques produits
        $stmt = $db->prepare("SELECT id, slug, name, active FROM products LIMIT 5");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Exemples de produits:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Slug</th><th>Nom</th><th>Actif</th></tr>";
        
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . ($product['slug'] ?: 'NULL') . "</td>";
            echo "<td>" . $product['name'] . "</td>";
            echo "<td>" . ($product['active'] ? 'Oui' : 'Non') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Tester la fonction getProductBySlug
    echo "<h3>Test de la fonction getProductBySlug:</h3>";
    $testSlug = 'bracelet-charms-eclat';
    $product = getProductBySlug($testSlug);
    
    if ($product) {
        echo "<p style='color: green;'>✅ Produit trouvé pour le slug '$testSlug': " . $product['name'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Aucun produit trouvé pour le slug '$testSlug'</p>";
        
        // Tester avec un slug qui pourrait exister
        $stmt = $db->prepare("SELECT slug FROM products WHERE slug IS NOT NULL AND slug != '' LIMIT 1");
        $stmt->execute();
        $existingSlug = $stmt->fetchColumn();
        
        if ($existingSlug) {
            echo "<p>Test avec un slug existant: '$existingSlug'</p>";
            $product = getProductBySlug($existingSlug);
            if ($product) {
                echo "<p style='color: green;'>✅ Produit trouvé: " . $product['name'] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Même avec un slug existant, la fonction échoue.</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}

?>
