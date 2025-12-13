<?php
/**
 * Run migration 054 - Add mobile_image_url to hero_slides table
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration
    $sql = file_get_contents(__DIR__ . '/migrations/054_add_mobile_image_to_hero_slides.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            $db->exec($statement);
            echo "Executed: " . substr($statement, 0, 60) . "...\n";
        }
    }
    
    echo "\n✅ Migration 054 completed successfully!\n";
    echo "Added mobile_image_url column to hero_slides table.\n";
    
} catch (PDOException $e) {
    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "⚠️ Column mobile_image_url already exists. Migration skipped.\n";
    } else {
        echo "❌ Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
