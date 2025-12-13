<?php
/**
 * Production 500 Error Diagnostic Script
 * 
 * This script helps diagnose common causes of 500 errors on production servers.
 * Upload this file to your production server and access it via browser.
 */

// Enable error reporting for diagnostics
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Production 500 Error Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .check { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>Production 500 Error Diagnostics</h1>";

// Check 1: .env file exists
echo "<div class='section'>";
echo "<h2>1. Environment File Check</h2>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "<p class='check'>✓ .env file exists</p>";
    
    // Check if .env is readable
    if (is_readable($envPath)) {
        echo "<p class='check'>✓ .env file is readable</p>";
        
        // Show some .env content (safely)
        $envContent = file_get_contents($envPath);
        $lines = explode("\n", $envContent);
        $safeLines = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                $safeLines[] = $line;
            } elseif (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                // Hide sensitive values
                if (strpos($key, 'PASS') !== false || strpos($key, 'SECRET') !== false || strpos($key, 'KEY') !== false) {
                    $safeLines[] = $key . '=***HIDDEN***';
                } else {
                    $safeLines[] = $line;
                }
            }
        }
        echo "<pre>" . htmlspecialchars(implode("\n", array_slice($safeLines, 0, 20))) . "</pre>";
        if (count($lines) > 20) {
            echo "<p><em>... (showing first 20 lines)</em></p>";
        }
    } else {
        echo "<p class='error'>✗ .env file exists but is not readable</p>";
    }
} else {
    echo "<p class='error'>✗ .env file is missing!</p>";
    echo "<p><strong>This is likely the cause of your 500 error.</strong></p>";
    echo "<p>Copy the .env.production-template file to .env and configure it with your production settings.</p>";
}
echo "</div>";

// Check 2: PHP Version and Extensions
echo "<div class='section'>";
echo "<h2>2. PHP Environment</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'curl', 'json'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='check'>✓ {$ext} extension loaded</p>";
    } else {
        echo "<p class='error'>✗ {$ext} extension missing</p>";
    }
}
echo "</div>";

// Check 3: File Permissions
echo "<div class='section'>";
echo "<h2>3. File Permissions</h2>";

$checkPaths = [
    'config' => __DIR__ . '/config',
    'storage' => __DIR__ . '/storage',
    'uploads' => __DIR__ . '/uploads',
    'vendor' => __DIR__ . '/vendor'
];

foreach ($checkPaths as $name => $path) {
    if (file_exists($path)) {
        $perms = fileperms($path);
        $octal = substr(sprintf('%o', $perms), -4);
        if (is_readable($path)) {
            echo "<p class='check'>✓ {$name} directory readable (permissions: {$octal})</p>";
        } else {
            echo "<p class='error'>✗ {$name} directory not readable (permissions: {$octal})</p>";
        }
    } else {
        echo "<p class='error'>✗ {$name} directory missing</p>";
    }
}
echo "</div>";

// Check 4: Database Connection (if .env exists)
if (file_exists($envPath)) {
    echo "<div class='section'>";
    echo "<h2>4. Database Connection Test</h2>";
    
    try {
        // Load .env manually
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value, '"\'');
            }
        }
        
        // Try to determine which credentials to use
        $isProduction = !in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);
        
        if ($isProduction && isset($env['DB_LIVE_HOST'])) {
            $host = $env['DB_LIVE_HOST'];
            $dbname = $env['DB_LIVE_NAME'];
            $username = $env['DB_LIVE_USER'];
            $password = $env['DB_LIVE_PASS'];
            echo "<p>Using production (DB_LIVE_*) credentials</p>";
        } else {
            $host = $env['DB_LOCAL_HOST'] ?? 'localhost';
            $dbname = $env['DB_LOCAL_NAME'] ?? '';
            $username = $env['DB_LOCAL_USER'] ?? '';
            $password = $env['DB_LOCAL_PASS'] ?? '';
            echo "<p>Using local (DB_LOCAL_*) credentials</p>";
        }
        
        if (empty($dbname)) {
            echo "<p class='error'>✗ Database name not configured</p>";
        } else {
            $dsn = "mysql:host={$host};dbname={$dbname}";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
            echo "<p class='check'>✓ Database connection successful</p>";
            echo "<p>Connected to: {$host}/{$dbname}</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "</div>";
}

// Check 5: Composer Dependencies
echo "<div class='section'>";
echo "<h2>5. Composer Dependencies</h2>";
$vendorAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    echo "<p class='check'>✓ Composer autoload file exists</p>";
    try {
        require_once $vendorAutoload;
        echo "<p class='check'>✓ Composer autoload loads successfully</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Composer autoload error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>✗ Composer autoload file missing</p>";
    echo "<p>Run 'composer install' on the server</p>";
}
echo "</div>";

// Check 6: Installation Status
echo "<div class='section'>";
echo "<h2>6. Installation Status</h2>";
$installedFile = __DIR__ . '/config/.installed';
if (file_exists($installedFile)) {
    echo "<p class='check'>✓ System is marked as installed</p>";
    $installData = json_decode(file_get_contents($installedFile), true);
    if ($installData) {
        echo "<p>Installed at: " . htmlspecialchars($installData['installed_at'] ?? 'Unknown') . "</p>";
        echo "<p>Version: " . htmlspecialchars($installData['version'] ?? 'Unknown') . "</p>";
    }
} else {
    echo "<p class='warning'>⚠ System not marked as installed</p>";
    echo "<p>You may need to run the installation wizard</p>";
}
echo "</div>";

// Check 7: Error Logs
echo "<div class='section'>";
echo "<h2>7. Recent Error Logs</h2>";
$errorLogPath = __DIR__ . '/storage/logs/errors-' . date('Y-m-d') . '.log';
if (file_exists($errorLogPath)) {
    echo "<p class='check'>✓ Error log file exists for today</p>";
    $errorContent = file_get_contents($errorLogPath);
    $lines = explode("\n", $errorContent);
    $recentLines = array_slice($lines, -10); // Last 10 lines
    if (!empty(trim(implode('', $recentLines)))) {
        echo "<p class='warning'>Recent errors found:</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $recentLines)) . "</pre>";
    } else {
        echo "<p class='check'>✓ No recent errors in log</p>";
    }
} else {
    echo "<p class='warning'>⚠ No error log file for today</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If .env file is missing, copy .env.production-template to .env and configure it</li>";
echo "<li>Ensure database credentials are correct for your production environment</li>";
echo "<li>Check that all required PHP extensions are installed</li>";
echo "<li>Verify file permissions allow PHP to read configuration files</li>";
echo "<li>Run 'composer install --no-dev --optimize-autoloader' if vendor directory is missing</li>";
echo "<li>Check your web server error logs for more specific error messages</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>