<?php
/**
 * Run Migration 059: Rename Payout Section to Key Benefits Section
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running migration 059: Rename Payout Section to Key Benefits Section\n";
echo "====================================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/059_rename_payout_to_key_benefits.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        echo "Executing: " . substr($statement, 0, 80) . "...\n";
        $db->exec($statement);
    }
    
    echo "\n✅ Migration 059 completed successfully!\n";
    echo "\nRenamed columns in solutions table:\n";
    echo "- payout_section_enabled → key_benefits_section_enabled\n";
    echo "- payout_section_bg_color → key_benefits_section_bg_color\n";
    echo "- payout_section_heading1 → key_benefits_section_heading1\n";
    echo "- payout_section_heading2 → key_benefits_section_heading2\n";
    echo "- payout_section_subheading → key_benefits_section_subheading\n";
    echo "- payout_section_heading_color → key_benefits_section_heading_color\n";
    echo "- payout_section_subheading_color → key_benefits_section_subheading_color\n";
    echo "- payout_section_card_bg_color → key_benefits_section_card_bg_color\n";
    echo "- payout_section_card_border_color → key_benefits_section_card_border_color\n";
    echo "- payout_section_card_hover_bg_color → key_benefits_section_card_hover_bg_color\n";
    echo "- payout_section_card_text_color → key_benefits_section_card_text_color\n";
    echo "- payout_section_card_icon_color → key_benefits_section_card_icon_color\n";
    echo "- payout_cards → key_benefits_cards\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}