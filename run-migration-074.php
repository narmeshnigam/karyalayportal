<?php
/**
 * Run Migration 074: Add FAQs Section fields to feature_styling table
 * 
 * This script adds faqs_section_theme, faqs_section_heading, faqs_section_subheading,
 * and faqs_cards columns to the feature_styling table.
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 074: Add FAQs Section to Features\n";
echo str_repeat("=", 50) . "\n\n";

try {
    $db = Connection::getInstance();
    
    // Read the migration SQL
    $sql = file_get_contents(__DIR__ . '/database/migrations/074_add_faqs_section_to_features.sql');
    
    if (!$sql) {
        throw new Exception("Could not read migration file");
    }
    
    // Check if columns already exist
    $checkSql = "SHOW COLUMNS FROM feature_styling LIKE 'faqs_section_theme'";
    $stmt = $db->query($checkSql);
    
    if ($stmt->rowCount() > 0) {
        echo "Migration already applied - FAQs section columns already exist.\n";
        exit(0);
    }
    
    // Execute the migration
    echo "Adding FAQs section columns to feature_styling table...\n";
    $db->exec($sql);
    
    echo "\n✓ Migration 074 completed successfully!\n";
    echo "\nNew columns added:\n";
    echo "  - faqs_section_theme (VARCHAR(10), default: 'light')\n";
    echo "  - faqs_section_heading (VARCHAR(100), default: 'Frequently Asked Questions')\n";
    echo "  - faqs_section_subheading (VARCHAR(200))\n";
    echo "  - faqs_cards (JSON)\n";
    
} catch (PDOException $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
