<?php
/**
 * Run Migration 063 - Add CTA Banner Section to Solutions
 * 
 * This migration adds fields for the CTA banner section on solution detail pages.
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 063: Add CTA Banner Section to Solutions\n";
echo "============================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/063_add_cta_banner_section_to_solutions.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            echo "Executing: " . substr($statement, 0, 80) . "...\n";
            $db->exec($statement);
        }
    }
    
    echo "\n✅ Migration 063 completed successfully!\n";
    echo "\nNew CTA Banner fields added to solutions table:\n";
    echo "- cta_banner_enabled (BOOLEAN)\n";
    echo "- cta_banner_image_url (VARCHAR 500)\n";
    echo "- cta_banner_overlay_color (VARCHAR 50)\n";
    echo "- cta_banner_overlay_intensity (DECIMAL)\n";
    echo "- cta_banner_heading1 (VARCHAR 100)\n";
    echo "- cta_banner_heading2 (VARCHAR 100)\n";
    echo "- cta_banner_heading_color (VARCHAR 20)\n";
    echo "- cta_banner_button_text (VARCHAR 50)\n";
    echo "- cta_banner_button_link (VARCHAR 255)\n";
    echo "- cta_banner_button_bg_color (VARCHAR 20)\n";
    echo "- cta_banner_button_text_color (VARCHAR 20)\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    
    // Check if columns already exist
    if (strpos($e->getMessage(), 'Duplicate column') !== false || 
        strpos($e->getMessage(), 'already exists') !== false) {
        echo "\nNote: Some columns may already exist. This is OK if you've run this migration before.\n";
    }
    
    exit(1);
}
