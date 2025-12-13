<?php
/**
 * Server Configuration Check
 * 
 * This script checks web server configuration that might cause 500 errors
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Server Configuration Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .check { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>Server Configuration Check</h1>";

// Check 1: PHP Settings
echo "<div class='section'>";
echo "<h2>1. PHP Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$phpSettings = [
    'memory_limit' => ['current' => ini_get('memory_limit'), 'recommended' => '256M'],
    'max_execution_time' => ['current' => ini_get('max_execution_time'), 'recommended' => '300'],
    'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'recommended' => '64M'],
    'post_max_size' => ['current' => ini_get('post_max_size'), 'recommended' => '64M'],
    'max_input_vars' => ['current' => ini_get('max_input_vars'), 'recommended' => '3000'],
    'display_errors' => ['current' => ini_get('display_errors'), 'recommended' => '0 (production)'],
    'log_errors' => ['current' => ini_get('log_errors'), 'recommended' => '1'],
];

foreach ($phpSettings as $setting => $info) {
    $status = 'check';
    $statusText = '✓ OK';
    
    // Check specific conditions
    if ($setting === 'memory_limit') {
        $current = ini_get('memory_limit');
        if ($current !== '-1' && intval($current) < 128) {
            $status = 'warning';
            $statusText = '⚠ Low';
        }
    } elseif ($setting === 'max_execution_time') {
        if (intval($info['current']) < 30 && $info['current'] !== '0') {
            $status = 'warning';
            $statusText = '⚠ Low';
        }
    } elseif ($setting === 'display_errors') {
        if ($info['current'] === '1') {
            $status = 'warning';
            $statusText = '⚠ Should be off in production';
        }
    }
    
    echo "<tr>";
    echo "<td>{$setting}</td>";
    echo "<td>" . htmlspecialchars($info['current']) . "</td>";
    echo "<td class='{$status}'>{$statusText}</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Check 2: Server Variables
echo "<div class='section'>";
echo "<h2>2. Server Environment</h2>";
echo "<table>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

$serverVars = [
    'SERVER_SOFTWARE',
    'SERVER_NAME',
    'SERVER_ADDR',
    'SERVER_PORT',
    'DOCUMENT_ROOT',
    'REQUEST_URI',
    'SCRIPT_NAME',
    'SCRIPT_FILENAME',
    'HTTP_HOST',
    'HTTPS',
    'REQUEST_METHOD'
];

foreach ($serverVars as $var) {
    $value = $_SERVER[$var] ?? 'Not set';
    echo "<tr>";
    echo "<td>{$var}</td>";
    echo "<td>" . htmlspecialchars($value) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Check 3: .htaccess files
echo "<div class='section'>";
echo "<h2>3. .htaccess Files</h2>";

$htaccessPaths = [
    'Root .htaccess' => __DIR__ . '/.htaccess',
    'Public .htaccess' => __DIR__ . '/public/.htaccess',
    'Admin .htaccess' => __DIR__ . '/admin/.htaccess',
    'Install .htaccess' => __DIR__ . '/install/.htaccess'
];

foreach ($htaccessPaths as $name => $path) {
    if (file_exists($path)) {
        echo "<p class='check'>✓ {$name} exists</p>";
        if (is_readable($path)) {
            $content = file_get_contents($path);
            $lines = explode("\n", $content);
            echo "<p><strong>{$name} content:</strong></p>";
            echo "<pre>" . htmlspecialchars(implode("\n", array_slice($lines, 0, 10))) . "</pre>";
            if (count($lines) > 10) {
                echo "<p><em>... (showing first 10 lines)</em></p>";
            }
        } else {
            echo "<p class='error'>✗ {$name} exists but not readable</p>";
        }
    } else {
        echo "<p class='warning'>⚠ {$name} not found</p>";
    }
}
echo "</div>";

// Check 4: URL Rewriting Test
echo "<div class='section'>";
echo "<h2>4. URL Rewriting Test</h2>";

// Check if mod_rewrite is available
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p class='check'>✓ mod_rewrite is loaded</p>";
    } else {
        echo "<p class='error'>✗ mod_rewrite is not loaded</p>";
    }
} else {
    echo "<p class='warning'>⚠ Cannot check Apache modules (not running under Apache or function not available)</p>";
}

// Test if URL rewriting works
$testUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
           '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/public/';
echo "<p>Test accessing: <a href='{$testUrl}' target='_blank'>{$testUrl}</a></p>";
echo "</div>";

// Check 5: File System Permissions
echo "<div class='section'>";
echo "<h2>5. Critical File Permissions</h2>";

$criticalFiles = [
    'index.php' => __DIR__ . '/index.php',
    'public/index.php' => __DIR__ . '/public/index.php',
    'config/app.php' => __DIR__ . '/config/app.php',
    'config/database.php' => __DIR__ . '/config/database.php',
    '.env' => __DIR__ . '/.env'
];

foreach ($criticalFiles as $name => $path) {
    if (file_exists($path)) {
        $perms = fileperms($path);
        $octal = substr(sprintf('%o', $perms), -4);
        $readable = is_readable($path) ? 'Yes' : 'No';
        $writable = is_writable($path) ? 'Yes' : 'No';
        
        $status = is_readable($path) ? 'check' : 'error';
        echo "<p class='{$status}'>{$name}: {$octal} (Readable: {$readable}, Writable: {$writable})</p>";
    } else {
        echo "<p class='error'>✗ {$name}: File not found</p>";
    }
}
echo "</div>";

// Check 6: Error Log Locations
echo "<div class='section'>";
echo "<h2>6. Error Log Locations</h2>";

$errorLogPaths = [
    'PHP Error Log' => ini_get('error_log'),
    'Application Error Log' => __DIR__ . '/storage/logs/errors-' . date('Y-m-d') . '.log',
    'Apache Error Log (common)' => '/var/log/apache2/error.log',
    'Nginx Error Log (common)' => '/var/log/nginx/error.log'
];

foreach ($errorLogPaths as $name => $path) {
    if (empty($path)) {
        echo "<p class='warning'>⚠ {$name}: Not configured</p>";
    } elseif (file_exists($path)) {
        if (is_readable($path)) {
            $size = filesize($path);
            echo "<p class='check'>✓ {$name}: {$path} ({$size} bytes)</p>";
            
            // Show last few lines if it's a reasonable size
            if ($size > 0 && $size < 1024 * 1024) { // Less than 1MB
                $content = file_get_contents($path);
                $lines = explode("\n", $content);
                $lastLines = array_slice($lines, -5);
                if (!empty(trim(implode('', $lastLines)))) {
                    echo "<p><strong>Last 5 lines:</strong></p>";
                    echo "<pre>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";
                }
            }
        } else {
            echo "<p class='error'>✗ {$name}: {$path} (exists but not readable)</p>";
        }
    } else {
        echo "<p class='warning'>⚠ {$name}: {$path} (not found)</p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li><strong>Upload test-homepage.php</strong> and run it to test the exact homepage code path</li>";
echo "<li><strong>Check web server error logs</strong> for specific error messages during the 500 error</li>";
echo "<li><strong>Temporarily enable display_errors</strong> in production to see the actual error (remember to disable after)</li>";
echo "<li><strong>Check .htaccess rules</strong> for any redirects or rewrites that might cause issues</li>";
echo "<li><strong>Verify file permissions</strong> allow the web server to read all necessary files</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>