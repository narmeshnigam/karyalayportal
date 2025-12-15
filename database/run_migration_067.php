<?php
/**
 * Migration Runner for 067: Seed Inventory Management and Project Management Solutions
 * 
 * This script inserts two complete solution records with all child table data:
 * - solutions (main table)
 * - solution_styling (styling/colors)
 * - solution_content (JSON content arrays)
 * 
 * Usage: php database/run_migration_067.php
 * Or via browser: http://localhost/karyalayportal/database/run_migration_067.php
 */

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain');
}

// Direct database connection for CLI compatibility
function getDirectConnection(): PDO {
    // Try XAMPP socket first (macOS)
    $socketPath = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
    
    if (file_exists($socketPath)) {
        $dsn = "mysql:unix_socket=$socketPath;dbname=karyalay_portal;charset=utf8mb4";
    } else {
        // Fallback to TCP connection
        $dsn = "mysql:host=localhost;port=3306;dbname=karyalay_portal;charset=utf8mb4";
    }
    
    return new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

echo "Running Migration 067: Seed Inventory & Project Management Solutions\n";
echo str_repeat("=", 70) . "\n\n";

try {
    $db = getDirectConnection();
    
    // Read the SQL file
    $sqlFile = __DIR__ . '/migrations/067_seed_inventory_project_solutions.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolons but be careful with JSON content
    // We'll execute the entire file as multi-query instead
    
    echo "Executing migration...\n\n";
    
    // Get PDO instance and execute statements one by one
    $pdo = $db;
    
    // Use a proper SQL parser that handles strings correctly
    $statements = [];
    $currentStatement = '';
    $inString = false;
    $stringChar = '';
    $escaped = false;
    
    for ($i = 0; $i < strlen($sql); $i++) {
        $char = $sql[$i];
        $currentStatement .= $char;
        
        if ($escaped) {
            $escaped = false;
            continue;
        }
        
        if ($char === '\\') {
            $escaped = true;
            continue;
        }
        
        if (!$inString && ($char === "'" || $char === '"')) {
            $inString = true;
            $stringChar = $char;
            continue;
        }
        
        if ($inString && $char === $stringChar) {
            $inString = false;
            continue;
        }
        
        if (!$inString && $char === ';') {
            $stmt = trim($currentStatement);
            // Skip comments-only statements
            $stmtWithoutComments = preg_replace('/--.*$/m', '', $stmt);
            $stmtWithoutComments = trim($stmtWithoutComments);
            if (!empty($stmtWithoutComments) && $stmtWithoutComments !== ';') {
                $statements[] = $stmt;
            }
            $currentStatement = '';
        }
    }
    
    // Add any remaining statement
    if (!empty(trim($currentStatement))) {
        $statements[] = trim($currentStatement);
    }
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        if (empty(trim($statement))) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Show progress for key operations
            if (stripos($statement, 'INSERT INTO solutions') !== false) {
                if (stripos($statement, 'inventory-management') !== false) {
                    echo "✓ Inserted: Inventory Management (solutions table)\n";
                } elseif (stripos($statement, 'project-management') !== false) {
                    echo "✓ Inserted: Project Management (solutions table)\n";
                }
            } elseif (stripos($statement, 'INSERT INTO solution_styling') !== false) {
                echo "✓ Inserted: solution_styling record\n";
            } elseif (stripos($statement, 'INSERT INTO solution_content') !== false) {
                echo "✓ Inserted: solution_content record\n";
            } elseif (stripos($statement, 'SET @') !== false) {
                // UUID generation - silent
            }
        } catch (PDOException $e) {
            $errorCount++;
            // Check for duplicate entry
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠ Skipped (already exists): Statement " . ($index + 1) . "\n";
            } else {
                echo "✗ Error in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
                echo "  Statement preview: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "Migration 067 completed!\n";
    echo "Successful statements: $successCount\n";
    echo "Errors/Skipped: $errorCount\n";
    
    // Verify the data was inserted
    echo "\n" . str_repeat("-", 70) . "\n";
    echo "Verification:\n\n";
    
    $checkSql = "SELECT s.id, s.name, s.slug, s.status, 
                        (SELECT COUNT(*) FROM solution_styling WHERE solution_id = s.id) as has_styling,
                        (SELECT COUNT(*) FROM solution_content WHERE solution_id = s.id) as has_content
                 FROM solutions s 
                 WHERE s.slug IN ('inventory-management', 'project-management')";
    
    $stmt = $pdo->query($checkSql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        foreach ($results as $row) {
            echo "✓ {$row['name']} (slug: {$row['slug']})\n";
            echo "  - Status: {$row['status']}\n";
            echo "  - Has styling: " . ($row['has_styling'] ? 'Yes' : 'No') . "\n";
            echo "  - Has content: " . ($row['has_content'] ? 'Yes' : 'No') . "\n\n";
        }
    } else {
        echo "⚠ No solutions found with the expected slugs.\n";
    }
    
    echo "\nView solutions at:\n";
    echo "- /solution/inventory-management\n";
    echo "- /solution/project-management\n";
    
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
