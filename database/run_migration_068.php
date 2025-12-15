<?php
/**
 * Run Migration 068: Add FAQs Section fields to solution_styling table
 * 
 * This script adds faqs_section_theme, faqs_section_heading, and faqs_section_subheading
 * columns to the solution_styling table.
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 068: Add FAQs Section to Solutions\n";
echo str_repeat("=", 50) . "\n\n";

try {
    $db = Connection::getInstance();
    
    // Read the migration file
    $migrationFile = __DIR__ . '/migrations/068_add_faqs_section_to_solutions.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Check if columns already exist
    $checkSql = "SHOW COLUMNS FROM solution_styling LIKE 'faqs_section_theme'";
    $stmt = $db->query($checkSql);
    
    if ($stmt->rowCount() > 0) {
        echo "Migration already applied - FAQs section columns already exist.\n";
        exit(0);
    }
    
    // Execute the migration
    echo "Adding FAQs section columns to solution_styling table...\n";
    $db->exec($sql);
    
    echo "\n✓ Migration 068 completed successfully!\n";
    echo "\nNew columns added:\n";
    echo "  - faqs_section_theme (VARCHAR(10), default: 'light')\n";
    echo "  - faqs_section_heading (VARCHAR(48), default: 'Frequently Asked Questions')\n";
    echo "  - faqs_section_subheading (VARCHAR(120))\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
