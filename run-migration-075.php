<?php
/**
 * Run Migration 075: Add category field to solutions table
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 075: Add category field to solutions table\n";
echo "============================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM solutions LIKE 'category'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Column 'category' already exists in solutions table.\n";
    } else {
        // Add the category column
        $db->exec("ALTER TABLE solutions ADD COLUMN category VARCHAR(100) DEFAULT 'core' AFTER display_order");
        echo "✓ Added 'category' column to solutions table.\n";
        
        // Add index
        $db->exec("CREATE INDEX idx_solutions_category ON solutions(category)");
        echo "✓ Created index on category column.\n";
        
        // Update existing solutions
        $db->exec("UPDATE solutions SET category = 'core' WHERE category IS NULL OR category = ''");
        echo "✓ Updated existing solutions with default category.\n";
    }
    
    echo "\n✅ Migration 075 completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
