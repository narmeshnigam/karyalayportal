<?php
/**
 * Run Migration 057: Add Payout Cards Section to Solutions
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running migration 057: Add Payout Cards Section to Solutions\n";
echo "============================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/057_add_payout_cards_section_to_solutions.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        echo "Executing: " . substr($statement, 0, 80) . "...\n";
        $db->exec($statement);
    }
    
    echo "\n✅ Migration 057 completed successfully!\n";
    echo "\nNew columns added to solutions table:\n";
    echo "- payout_section_enabled (BOOLEAN)\n";
    echo "- payout_section_bg_color (VARCHAR)\n";
    echo "- payout_section_heading1 (VARCHAR 24)\n";
    echo "- payout_section_heading2 (VARCHAR 24)\n";
    echo "- payout_section_subheading (TEXT)\n";
    echo "- payout_section_heading_color (VARCHAR)\n";
    echo "- payout_section_subheading_color (VARCHAR)\n";
    echo "- payout_section_card_bg_color (VARCHAR)\n";
    echo "- payout_section_card_border_color (VARCHAR)\n";
    echo "- payout_section_card_hover_bg_color (VARCHAR)\n";
    echo "- payout_section_card_text_color (VARCHAR)\n";
    echo "- payout_section_card_icon_color (VARCHAR)\n";
    echo "- payout_cards (JSON)\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
