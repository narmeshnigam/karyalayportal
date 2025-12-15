<?php
/**
 * Run Migration 060: Add Hero Background Color Field to Solutions
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running migration 060: Add Hero Background Color Field to Solutions\n";
echo "===================================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/060_add_hero_bg_color_to_solutions.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        echo "Executing: " . substr($statement, 0, 80) . "...\n";
        $db->exec($statement);
    }
    
    echo "\n✅ Migration 060 completed successfully!\n";
    echo "\nNew column added to solutions table:\n";
    echo "- hero_bg_color (VARCHAR 50) - Base background color for hero section\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}