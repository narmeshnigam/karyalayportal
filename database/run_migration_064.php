<?php
/**
 * Run Migration 064: Create normalized solutions tables
 * 
 * Creates:
 * - solutions (main table)
 * - solution_styling (styling/colors)
 * - solution_content (JSON arrays)
 * 
 * Usage: php database/run_migration_064.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Karyalay\Database\Connection;

try {
    $db = Connection::getInstance();
    
    echo "Starting Migration 064: Create normalized solutions tables...\n\n";
    
    $migrationFile = __DIR__ . '/migrations/064_split_solutions_table.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolons, filtering out comments
    $statements = array_filter(
        preg_split('/;\s*\n/', $sql),
        fn($s) => !empty(trim($s)) && !preg_match('/^\s*--/', trim($s))
    );
    
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement) || preg_match('/^--/', $statement)) {
            continue;
        }
        
        // Extract a short description
        $shortDesc = substr(preg_replace('/\s+/', ' ', $statement), 0, 60) . '...';
        echo "Executing: $shortDesc\n";
        
        try {
            $db->exec($statement);
            echo "  ✓ Success\n";
        } catch (PDOException $e) {
            // Handle "already exists" errors gracefully
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "  ⚠ Skipped (already exists)\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Migration 064 completed successfully!\n";
    echo "\nCreated tables:\n";
    echo "  - solutions: Core solution data (name, slug, meta, status)\n";
    echo "  - solution_styling: All styling/color fields (FK → solutions.id)\n";
    echo "  - solution_content: JSON content arrays (FK → solutions.id)\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
