<?php
/**
 * Fix Media Assets Table
 * 
 * This script adds the missing 'file_path' column to the media_assets table.
 * Run this script once to fix the "Failed to save media asset to database" error.
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

// Only allow CLI or authenticated admin access
if (php_sapi_name() !== 'cli') {
    require_once __DIR__ . '/includes/auth_helpers.php';
    startSecureSession();
    if (!is_admin()) {
        http_response_code(403);
        die('Access denied. Admin authentication required.');
    }
}

echo "<pre>\n";
echo "=== Media Assets Table Fix ===\n\n";

try {
    $db = Connection::getInstance();
    
    // Check if column already exists
    echo "Checking if 'file_path' column exists...\n";
    $stmt = $db->query("SHOW COLUMNS FROM media_assets LIKE 'file_path'");
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Column 'file_path' already exists. No changes needed.\n";
    } else {
        echo "✗ Column 'file_path' is missing. Adding it now...\n";
        
        // Add the file_path column
        $db->exec("ALTER TABLE media_assets ADD COLUMN file_path VARCHAR(500) NULL AFTER url");
        echo "✓ Added 'file_path' column.\n";
        
        // Add index for file_path lookups
        try {
            $db->exec("CREATE INDEX idx_media_assets_file_path ON media_assets(file_path)");
            echo "✓ Added index on 'file_path' column.\n";
        } catch (PDOException $e) {
            // Index might already exist
            echo "Note: Index creation skipped (may already exist).\n";
        }
        
        // Update existing records to populate file_path from url
        echo "\nUpdating existing records with file_path...\n";
        
        $updateStmt = $db->prepare("
            UPDATE media_assets 
            SET file_path = CONCAT('uploads/', SUBSTRING_INDEX(url, '/uploads/', -1))
            WHERE file_path IS NULL 
            AND url LIKE '%/uploads/%'
        ");
        $updateStmt->execute();
        $updated = $updateStmt->rowCount();
        
        echo "✓ Updated {$updated} existing records with file_path.\n";
    }
    
    echo "\n=== Fix Complete ===\n";
    echo "You can now upload media files without errors.\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nPlease ensure:\n";
    echo "1. MySQL/MariaDB is running\n";
    echo "2. Database credentials in .env are correct\n";
    echo "3. The 'media_assets' table exists\n";
}

echo "</pre>\n";
