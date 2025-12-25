<?php
/**
 * Run Migration 077 - Add featured_image to blog_posts table
 */

require_once __DIR__ . '/config/bootstrap.php';

echo "Running migration 077: Add featured_image to blog_posts...\n";

try {
    $db = \Karyalay\Database\Connection::getInstance();
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM blog_posts LIKE 'featured_image'");
    if ($stmt->rowCount() > 0) {
        echo "Column 'featured_image' already exists. Skipping.\n";
        exit(0);
    }
    
    // Read and execute migration
    $sql = file_get_contents(__DIR__ . '/database/migrations/077_add_featured_image_to_blog_posts.sql');
    
    // Remove comments and empty lines
    $lines = explode("\n", $sql);
    $cleanSql = '';
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, '--') !== 0) {
            $cleanSql .= $line . ' ';
        }
    }
    
    $db->exec(trim($cleanSql));
    
    echo "Migration 077 completed successfully!\n";
    echo "Column 'featured_image' added to blog_posts table.\n";
    
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
