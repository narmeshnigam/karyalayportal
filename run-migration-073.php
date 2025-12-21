<?php
/**
 * Run Migration 073: Add Use Cases Section to Features
 * 
 * This script adds the Use Cases section fields to the feature_styling table
 * to allow dynamic management from the admin panel.
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "=== Migration 073: Add Use Cases Section to Features ===\n\n";

try {
    $db = Connection::getInstance();
    
    $migrationFile = __DIR__ . '/database/migrations/073_add_use_cases_section_to_features.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $skipCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        if (preg_match('/^--/', $statement) || preg_match('/^=+$/', $statement)) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $successCount++;
            
            if (preg_match('/ADD COLUMN.*?(\w+)\s/', $statement, $matches)) {
                echo "✓ Added column: {$matches[1]}\n";
            }
        } catch (PDOException $e) {
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
    
    echo "\n=== Verifying Columns ===\n";
    $checkSql = "DESCRIBE feature_styling";
    $stmt = $db->query($checkSql);
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $expectedColumns = [
        'use_cases_enabled',
        'use_cases_badge',
        'use_cases_heading',
        'use_cases_subheading',
        'use_cases_bg_color',
        'use_cases_badge_bg_color',
        'use_cases_badge_text_color',
        'use_cases_heading_color',
        'use_cases_subheading_color',
        'use_cases_card_bg_color',
        'use_cases_card_border_color',
        'use_cases_card_hover_border_color',
        'use_cases_title_color',
        'use_cases_desc_color',
        'use_cases_overlay_color',
        'use_cases_cards'
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
        echo "\n✓ All Use Cases section columns are present!\n";
    } else {
        echo "\n⚠ Some columns are missing. Please check the migration.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
