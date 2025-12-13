<?php
/**
 * Run Migration 053 - Create Business Hub Tables
 * 
 * This script creates the business_hub_categories and business_hub_nodes tables
 * and seeds them with default data matching the current static content.
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Running Migration 053: Create Business Hub Tables\n";
echo "=================================================\n\n";

try {
    $db = Connection::getInstance();
    
    // Read the migration SQL file
    $sqlFile = __DIR__ . '/migrations/053_create_business_hub_tables.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolons but be careful with the COMMENT syntax
    $statements = [];
    $current = '';
    $inComment = false;
    
    foreach (explode("\n", $sql) as $line) {
        $trimmed = trim($line);
        
        // Skip empty lines and comments
        if (empty($trimmed) || strpos($trimmed, '--') === 0) {
            continue;
        }
        
        $current .= $line . "\n";
        
        // Check if line ends with semicolon (end of statement)
        if (substr($trimmed, -1) === ';') {
            $statements[] = trim($current);
            $current = '';
        }
    }
    
    // Execute each statement
    $successCount = 0;
    $skipCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $successCount++;
            
            // Show what was executed
            $preview = substr($statement, 0, 60);
            $preview = preg_replace('/\s+/', ' ', $preview);
            echo "✓ Executed: {$preview}...\n";
            
        } catch (PDOException $e) {
            // Check if it's a "table already exists" or "duplicate" error
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), 'Duplicate') !== false) {
                $skipCount++;
                echo "⊘ Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n=================================================\n";
    echo "Migration completed!\n";
    echo "Statements executed: $successCount\n";
    echo "Statements skipped: $skipCount\n";
    
    // Verify tables exist
    echo "\nVerifying tables...\n";
    
    $tables = ['business_hub_categories', 'business_hub_nodes'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'")->fetch();
        if ($result) {
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "✓ Table '$table' exists with $count rows\n";
        } else {
            echo "✗ Table '$table' NOT FOUND\n";
        }
    }
    
    echo "\nDone!\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
