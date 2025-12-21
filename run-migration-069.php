<?php
/**
 * Run Migration 069: Create feature_styling table for hero section management
 * 
 * This script creates the feature_styling table and inserts default styling
 * for existing features.
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 069: Create feature_styling table\n";
echo "====================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Check if table already exists
    $checkSql = "SHOW TABLES LIKE 'feature_styling'";
    $stmt = $db->query($checkSql);
    
    if ($stmt->rowCount() > 0) {
        echo "⚠️  Table 'feature_styling' already exists.\n";
        echo "Checking for missing records...\n\n";
        
        // Insert default styling for any features that don't have styling records
        $insertSql = "INSERT INTO feature_styling (id, feature_id)
            SELECT UUID(), id FROM features 
            WHERE id NOT IN (SELECT feature_id FROM feature_styling)";
        $result = $db->exec($insertSql);
        
        echo "✅ Added styling records for $result features.\n";
    } else {
        // Read and execute the migration file
        $migrationFile = __DIR__ . '/database/migrations/069_add_hero_section_to_features.sql';
        
        if (!file_exists($migrationFile)) {
            throw new Exception("Migration file not found: $migrationFile");
        }
        
        $sql = file_get_contents($migrationFile);
        
        // Split by semicolons and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            echo "Executing: " . substr($statement, 0, 60) . "...\n";
            $db->exec($statement);
        }
        
        echo "\n✅ Migration 069 completed successfully!\n";
        echo "Created table: feature_styling\n";
    }
    
    // Verify the table structure
    echo "\nVerifying table structure:\n";
    $columns = $db->query("DESCRIBE feature_styling")->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in feature_styling: " . count($columns) . "\n";
    
    // Count records
    $count = $db->query("SELECT COUNT(*) FROM feature_styling")->fetchColumn();
    echo "Records in feature_styling: $count\n";
    
    // Count features
    $featureCount = $db->query("SELECT COUNT(*) FROM features")->fetchColumn();
    echo "Total features: $featureCount\n";
    
    if ($count < $featureCount) {
        echo "\n⚠️  Some features are missing styling records. Adding them now...\n";
        $insertSql = "INSERT INTO feature_styling (id, feature_id)
            SELECT UUID(), id FROM features 
            WHERE id NOT IN (SELECT feature_id FROM feature_styling)";
        $result = $db->exec($insertSql);
        echo "✅ Added $result styling records.\n";
    }
    
    echo "\n✅ Migration complete!\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
