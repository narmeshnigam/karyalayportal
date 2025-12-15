<?php
/**
 * Run Migration 055: Enhance solutions table for Razorpay-style detail page
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 055: Enhance solutions table for Razorpay-style detail page\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sqlFile = __DIR__ . '/migrations/055_enhance_solutions_for_razorpay_style.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        // Skip comment-only lines
        $lines = explode("\n", $statement);
        $hasCode = false;
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (!empty($trimmed) && strpos($trimmed, '--') !== 0) {
                $hasCode = true;
                break;
            }
        }
        
        if (!$hasCode) {
            continue;
        }
        
        try {
            $db->exec($statement);
            echo "✓ Executed: " . substr(preg_replace('/\s+/', ' ', $statement), 0, 60) . "...\n";
        } catch (PDOException $e) {
            // Check if it's a "column already exists" error
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Skipped (already exists): " . substr(preg_replace('/\s+/', ' ', $statement), 0, 50) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✓ Migration 055 completed successfully!\n\n";
    
    echo "New columns added to solutions table:\n";
    echo "- subtitle: Extended description for hero section\n";
    echo "- hero_badge: Badge text (e.g., 'New', 'Popular')\n";
    echo "- hero_cta_primary_text/link: Primary CTA button\n";
    echo "- hero_cta_secondary_text/link: Secondary CTA button\n";
    echo "- demo_video_url: URL for demo video\n";
    echo "- highlight_cards: JSON array of highlight cards with metrics\n";
    echo "- integrations: JSON array of integration logos\n";
    echo "- workflow_steps: JSON array of how-it-works steps\n";
    echo "- testimonial_id: Link to testimonial\n";
    echo "- pricing_note: Custom CTA section text\n";
    echo "- meta_title/description/keywords: SEO fields\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
