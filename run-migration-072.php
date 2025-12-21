<?php
/**
 * Run Migration 072: Add Feature Highlights Section to Features
 * 
 * This script adds the Feature Highlights section fields to the feature_styling table
 * to allow dynamic management from the admin panel.
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "=== Migration 072: Add Feature Highlights Section to Features ===\n\n";

try {
    $db = Connection::getInstance();
    
    // Read the migration file
    $migrationFile = __DIR__ . '/database/migrations/072_add_feature_highlights_section_to_features.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $skipCount = 0;
    
    foreach ($statements as $statement) {
        // Skip empty statements and comments
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        // Skip comment blocks
        if (preg_match('/^--/', $statement) || preg_match('/^=+$/', $statement)) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $successCount++;
            
            // Extract column name for logging
            if (preg_match('/ADD COLUMN.*?(\w+)\s/', $statement, $matches)) {
                echo "✓ Added column: {$matches[1]}\n";
            }
        } catch (PDOException $e) {
            // Check if it's a "column already exists" error
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                $skipCount++;
                if (preg_match('/ADD COLUMN.*?(\w+)\s/', $statement, $matches)) {
                    echo "- Skipped (exists): {$matches[1]}\n";
                }
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n=== Migration Complete ===\n";
    echo "Columns added: $successCount\n";
    echo "Columns skipped (already exist): $skipCount\n";
    
    // Verify the columns exist
    echo "\n=== Verifying Columns ===\n";
    $checkSql = "DESCRIBE feature_styling";
    $stmt = $db->query($checkSql);
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $expectedColumns = [
        'highlights_enabled',
        'highlights_badge',
        'highlights_heading',
        'highlights_subheading',
        'highlights_bg_color',
        'highlights_badge_bg_color',
        'highlights_badge_text_color',
        'highlights_heading_color',
        'highlights_subheading_color',
        'highlights_card_bg_color',
        'highlights_card_border_color',
        'highlights_card_hover_border_color',
        'highlights_icon_bg_color',
        'highlights_icon_color',
        'highlights_title_color',
        'highlights_desc_color',
        'highlights_cards'
    ];
    
    $allFound = true;
    foreach ($expectedColumns as $col) {
        if (in_array($col, $columns)) {
            echo "✓ $col\n";
        } else {
            echo "✗ $col (MISSING)\n";
            $allFound = false;
        }
    }
    
    if ($allFound) {
        echo "\n✓ All Feature Highlights section columns are present!\n";
    } else {
        echo "\n⚠ Some columns are missing. Please check the migration.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
