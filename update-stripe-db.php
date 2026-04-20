<?php
// Script pour ajouter les colonnes Stripe à la table orders
require_once __DIR__ . '/includes/config.php';

echo "<h1>Mise à jour de la base de données pour Stripe</h1>";

try {
    $db = getDB();
    
    // Vérifier si les colonnes existent déjà
    $stmt = $db->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $columnsToAdd = [
        'stripe_session_id' => "VARCHAR(255) DEFAULT NULL COMMENT 'ID de session Stripe Checkout'",
        'stripe_payment_intent_id' => "VARCHAR(255) DEFAULT NULL COMMENT 'ID du Payment Intent Stripe'",
        'paid_at' => "DATETIME DEFAULT NULL COMMENT 'Date de paiement'"
    ];
    
    $addedColumns = [];
    
    foreach ($columnsToAdd as $column => $definition) {
        if (!in_array($column, $columns)) {
            $sql = "ALTER TABLE orders ADD COLUMN `{$column}` {$definition}";
            echo "<p>Ajout de la colonne {$column}...</p>";
            $db->exec($sql);
            $addedColumns[] = $column;
            echo "<p>Column {$column} ajoutée avec succès.</p>";
        } else {
            echo "<p>La colonne {$column} existe déjà.</p>";
        }
    }
    
    // Mettre à jour le statut ENUM pour inclure 'pending_payment' et 'paid'
    $stmt = $db->query("SHOW COLUMNS FROM orders LIKE 'status'");
    $statusColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($statusColumn) {
        $currentEnum = $statusColumn['Type'];
        echo "<p>Statut actuel: {$currentEnum}</p>";
        
        // Vérifier si pending_payment et paid sont déjà dans l'ENUM
        if (strpos($currentEnum, 'pending_payment') === false || strpos($currentEnum, 'paid') === false) {
            echo "<p>Mise à jour de l'ENUM status...</p>";
            
            // Extraire les valeurs actuelles
            preg_match("/ENUM\((.*)\)/", $currentEnum, $matches);
            $currentValues = str_getcsv($matches[1], ",", "'");
            
            // Ajouter les nouvelles valeurs si elles n'existent pas
            if (!in_array('pending_payment', $currentValues)) {
                $currentValues[] = 'pending_payment';
            }
            if (!in_array('paid', $currentValues)) {
                $currentValues[] = 'paid';
            }
            
            $newEnum = "ENUM('" . implode("','", $currentValues) . "')";
            $sql = "ALTER TABLE orders MODIFY COLUMN status {$newEnum} DEFAULT 'pending'";
            $db->exec($sql);
            echo "<p>ENUM status mis à jour: {$newEnum}</p>";
        } else {
            echo "<p>L'ENUM status contient déjà les valeurs nécessaires.</p>";
        }
    }
    
    echo "<h2>Mise à jour terminée !</h2>";
    echo "<p>Colonnes ajoutées: " . implode(", ", $addedColumns) . "</p>";
    echo "<p><a href='api/stripe-test.php'>Tester l'intégration Stripe</a></p>";
    echo "<p><a href='checkout.php'>Aller au checkout</a></p>";
    
} catch (Exception $e) {
    echo "<h2>Erreur</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
