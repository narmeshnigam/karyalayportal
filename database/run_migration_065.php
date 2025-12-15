<?php
/**
 * Run Migration 065: Add Industries Section to Solutions
 * 
 * This migration adds the industries gallery section fields to solutions.
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 065: Add Industries Section to Solutions\n";
echo "============================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Check if migration already applied
    $checkSql = "SHOW COLUMNS FROM solution_styling LIKE 'industries_section_enabled'";
    $stmt = $db->query($checkSql);
    if ($stmt->rowCount() > 0) {
        echo "Migration 065 already applied. Skipping.\n";
        exit(0);
    }
    
    // Read and execute migration SQL
    $migrationFile = __DIR__ . '/migrations/065_add_industries_section_to_solutions.sql';
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        // Skip comments and empty statements
        if (empty($statement) || strpos(trim($statement), '--') === 0) {
            continue;
        }
        
        echo "Executing: " . substr($statement, 0, 80) . "...\n";
        $db->exec($statement);
    }
    
    echo "\n✅ Migration 065 completed successfully!\n";
    echo "Industries section fields added to solution_styling and solution_content tables.\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
