<?php
/**
 * Run Migration 058: Remove Unused Solution Sections
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running migration 058: Remove Unused Solution Sections\n";
echo "======================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/058_remove_unused_solution_sections.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        echo "Executing: " . substr($statement, 0, 80) . "...\n";
        $db->exec($statement);
    }
    
    echo "\n✅ Migration 058 completed successfully!\n";
    echo "\nRemoved columns from solutions table:\n";
    echo "- stats (JSON)\n";
    echo "- highlight_cards (JSON)\n";
    echo "- workflow_steps (JSON)\n";
    echo "- benefits (JSON)\n";
    echo "- use_cases (JSON)\n";
    echo "- integrations (JSON)\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}