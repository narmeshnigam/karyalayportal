<?php
/**
 * Run Migration 056: Solution Hero Enhancements
 * 
 * Adds support for:
 * - GIF/MP4 autoplaying media
 * - Glassy effects for buttons and media container
 * - Title and button styling controls
 * - Single-line title (max 24 characters)
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 056: Solution Hero Enhancements\n";
echo str_repeat("=", 50) . "\n\n";

try {
    $db = Connection::getInstance();
    
    // Read the migration file
    $migrationFile = __DIR__ . '/database/migrations/056_solution_hero_enhancements.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolons and filter out comments/empty statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            $stmt = trim($stmt);
            return !empty($stmt) && strpos($stmt, '--') !== 0;
        }
    );
    
    $successCount = 0;
    $skipCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $successCount++;
            
            // Extract column name for display
            if (preg_match('/ADD COLUMN IF NOT EXISTS (\w+)/', $statement, $matches)) {
                echo "✓ Added column: {$matches[1]}\n";
            } else {
                echo "✓ Executed statement\n";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                $skipCount++;
                if (preg_match('/ADD COLUMN IF NOT EXISTS (\w+)/', $statement, $matches)) {
                    echo "○ Column already exists: {$matches[1]}\n";
                }
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Migration completed!\n";
    echo "- Statements executed: $successCount\n";
    echo "- Columns skipped (already exist): $skipCount\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
