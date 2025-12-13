<?php
/**
 * Run Migration 052 - Create client_logos table
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

try {
    $db = Connection::getInstance();
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/migrations/052_create_client_logos_table.sql');
    
    $db->exec($sql);
    
    echo "Migration 052 completed successfully - client_logos table created.\n";
} catch (PDOException $e) {
    echo "Migration 052 failed: " . $e->getMessage() . "\n";
    exit(1);
}
