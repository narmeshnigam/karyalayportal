<?php
/**
 * Run Migration 076: Fix double-encoded HTML entities
 * 
 * This script fixes content that was previously encoded with htmlspecialchars() on save
 * and would be double-encoded when displayed with htmlspecialchars() again.
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 076: Fix double-encoded HTML entities\n";
echo "========================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sqlFile = __DIR__ . '/database/migrations/076_fix_double_encoded_entities.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        // Skip empty statements and comments
        if (empty($statement) || strpos(trim($statement), '--') === 0) {
            continue;
        }
        
        try {
            $affected = $db->exec($statement);
            if ($affected > 0) {
                echo "✓ Updated $affected row(s)\n";
            }
            $successCount++;
        } catch (PDOException $e) {
            // Some tables might not exist, that's okay
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                echo "⚠ Table doesn't exist, skipping: " . substr($statement, 0, 50) . "...\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }
    
    echo "\n========================================================\n";
    echo "Migration completed!\n";
    echo "Successful statements: $successCount\n";
    echo "Errors: $errorCount\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
