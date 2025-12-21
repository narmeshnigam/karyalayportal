<?php
/**
 * Migration Runner for 070_add_key_benefits_section_to_features.sql
 * Adds key benefits section styling columns to feature_styling table
 * 
 * Run this script from the command line:
 * php run-migration-070.php
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "=== Migration 070: Add Key Benefits Section to Features ===\n\n";

try {
    $db = Connection::getInstance();
    
    // Read the migration file
    $migrationFile = __DIR__ . '/database/migrations/070_add_key_benefits_section_to_features.sql';
    
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
        if (empty($statement) || strpos(trim($statement), '--') === 0) {
            continue;
        }
        
        // Skip pure comment blocks
        if (preg_match('/^--.*$/m', $statement) && !preg_match('/ALTER|CREATE|INSERT|UPDATE|DELETE/i', $statement)) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $successCount++;
            
            // Extract column name for logging
            if (preg_match('/ADD COLUMN.*?(\w+)\s+/i', $statement, $matches)) {
                echo "✓ Added column: {$matches[1]}\n";
            } else {
                echo "✓ Executed statement\n";
            }
        } catch (PDOException $e) {
            // Check if it's a "column already exists" error
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                $skipCount++;
                if (preg_match('/ADD COLUMN.*?(\w+)\s+/i', $statement, $matches)) {
                    echo "⊘ Column already exists: {$matches[1]}\n";
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
        'benefits_section_enabled',
        'benefits_section_heading1',
        'benefits_section_heading2',
        'benefits_section_subheading',
        'benefits_section_bg_color',
        'benefits_section_heading_color',
        'benefits_section_subheading_color',
        'benefits_card_bg_color',
        'benefits_card_border_color',
        'benefits_card_hover_bg_color',
        'benefits_card_title_color',
        'benefits_card_text_color',
        'benefits_card_icon_color',
        'benefits_card_hover_text_color',
        'benefits_cards'
    ];
    
    $allPresent = true;
    foreach ($expectedColumns as $col) {
        if (in_array($col, $columns)) {
            echo "✓ $col\n";
        } else {
            echo "✗ $col (MISSING)\n";
            $allPresent = false;
        }
    }
    
    if ($allPresent) {
        echo "\n✓ All key benefits section columns are present!\n";
    } else {
        echo "\n⚠ Some columns are missing. Please check the migration.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
