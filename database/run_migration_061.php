<?php
/**
 * Run Migration 061: Add button hover text colors and remove glassy effect fields
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

try {
    $db = Connection::getInstance();
    
    echo "Running Migration 061: Add button hover text colors and remove glassy effect fields...\n";
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/061_add_button_hover_colors_remove_glassy.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $db->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Some statements might fail if columns don't exist, which is expected
                if (strpos($e->getMessage(), 'check that column/key exists') !== false) {
                    echo "ℹ Skipped (column doesn't exist): " . substr($statement, 0, 50) . "...\n";
                } else {
                    throw $e;
                }
            }
        }
    }
    
    echo "\n✅ Migration 061 completed successfully!\n";
    echo "\nChanges made:\n";
    echo "- Added hero_primary_btn_text_hover_color field\n";
    echo "- Added hero_secondary_btn_text_hover_color field\n";
    echo "- Removed glassy effect fields (if they existed)\n";
    echo "- Updated existing solutions with default hover colors\n";
    echo "\nButton hover text colors are now fully configurable from the admin panel.\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration 061 failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>