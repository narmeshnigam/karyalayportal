<?php
/**
 * Run Migration 062: Add Feature Showcase Section to Solutions
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

try {
    $db = Connection::getInstance();
    
    echo "Running Migration 062: Add Feature Showcase Section to Solutions...\n";
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/062_add_feature_showcase_section_to_solutions.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $db->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 60) . "...\n";
            } catch (PDOException $e) {
                // Column might already exist
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "ℹ Skipped (column already exists): " . substr($statement, 0, 60) . "...\n";
                } else {
                    throw $e;
                }
            }
        }
    }
    
    echo "\n✅ Migration 062 completed successfully!\n";
    echo "\nChanges made:\n";
    echo "- Added feature_showcase_section_enabled field\n";
    echo "- Added feature_showcase_section_title field\n";
    echo "- Added feature_showcase_section_subtitle field\n";
    echo "- Added feature_showcase section color fields\n";
    echo "- Added feature_showcase card styling fields\n";
    echo "- Added feature_showcase_cards JSON field (max 6 cards)\n";
    echo "\nThe Feature Showcase section is now fully configurable from the admin panel.\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration 062 failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
