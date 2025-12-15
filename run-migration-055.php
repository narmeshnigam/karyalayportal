<?php
/**
 * Web-accessible Migration 055 Runner
 * Access via browser: http://localhost/karyalayportal/run-migration-055.php
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

header('Content-Type: text/plain');

echo "Running Migration 055: Enhance solutions table for Razorpay-style detail page\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $db = Connection::getInstance();
    
    // Add columns one by one to handle "already exists" gracefully
    $columns = [
        "ALTER TABLE solutions ADD COLUMN subtitle VARCHAR(500) AFTER tagline",
        "ALTER TABLE solutions ADD COLUMN hero_badge VARCHAR(100) AFTER subtitle",
        "ALTER TABLE solutions ADD COLUMN hero_cta_primary_text VARCHAR(100) DEFAULT 'Get Started' AFTER hero_badge",
        "ALTER TABLE solutions ADD COLUMN hero_cta_primary_link VARCHAR(500) AFTER hero_cta_primary_text",
        "ALTER TABLE solutions ADD COLUMN hero_cta_secondary_text VARCHAR(100) DEFAULT 'Watch Demo' AFTER hero_cta_primary_link",
        "ALTER TABLE solutions ADD COLUMN hero_cta_secondary_link VARCHAR(500) AFTER hero_cta_secondary_text",
        "ALTER TABLE solutions ADD COLUMN demo_video_url VARCHAR(500) AFTER hero_cta_secondary_link",
        "ALTER TABLE solutions ADD COLUMN highlight_cards JSON AFTER stats",
        "ALTER TABLE solutions ADD COLUMN integrations JSON AFTER highlight_cards",
        "ALTER TABLE solutions ADD COLUMN workflow_steps JSON AFTER integrations",
        "ALTER TABLE solutions ADD COLUMN testimonial_id CHAR(36) AFTER workflow_steps",
        "ALTER TABLE solutions ADD COLUMN pricing_note TEXT AFTER testimonial_id",
        "ALTER TABLE solutions ADD COLUMN meta_title VARCHAR(255) AFTER pricing_note",
        "ALTER TABLE solutions ADD COLUMN meta_description TEXT AFTER meta_title",
        "ALTER TABLE solutions ADD COLUMN meta_keywords VARCHAR(500) AFTER meta_description"
    ];
    
    foreach ($columns as $sql) {
        try {
            $db->exec($sql);
            echo "✓ " . substr($sql, 0, 60) . "...\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⚠ Skipped (exists): " . substr($sql, 30, 40) . "...\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✓ Migration 055 completed!\n\n";
    echo "Next step: Run the seed script at /seed-solutions.php\n";
    
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
}
