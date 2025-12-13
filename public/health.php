<?php
/**
 * Health Check Endpoint
 * 
 * This file provides a simple health check for the application.
 * It verifies that the basic components are working correctly.
 * 
 * Access: /public/health.php or /health (with clean URLs)
 */

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'checks' => []
];

// Check 1: PHP Version
$phpVersion = phpversion();
$health['checks']['php'] = [
    'status' => version_compare($phpVersion, '8.0.0', '>=') ? 'ok' : 'warning',
    'version' => $phpVersion,
    'required' => '8.0.0'
];

// Check 2: Required Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$health['checks']['extensions'] = [
    'status' => empty($missingExtensions) ? 'ok' : 'error',
    'missing' => $missingExtensions
];

// Check 3: Writable Directories
$writableDirs = [
    'storage' => __DIR__ . '/../storage',
    'uploads' => __DIR__ . '/../uploads',
    'config' => __DIR__ . '/../config'
];
$nonWritable = [];
foreach ($writableDirs as $name => $path) {
    if (!is_writable($path)) {
        $nonWritable[] = $name;
    }
}
$health['checks']['writable_dirs'] = [
    'status' => empty($nonWritable) ? 'ok' : 'warning',
    'non_writable' => $nonWritable
];

// Check 4: Environment File
$envExists = file_exists(__DIR__ . '/../.env');
$health['checks']['env_file'] = [
    'status' => $envExists ? 'ok' : 'warning',
    'exists' => $envExists
];

// Check 5: Installation Status
$installed = file_exists(__DIR__ . '/../config/.installed');
$health['checks']['installation'] = [
    'status' => $installed ? 'ok' : 'info',
    'installed' => $installed
];

// Check 6: Database Connection (only if installed)
if ($installed && $envExists) {
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        
        // Load environment variables
        if (file_exists(__DIR__ . '/../.env')) {
            $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    if (preg_match('/^"(.*)"$/', $value, $matches)) {
                        $value = $matches[1];
                    }
                    if (!getenv($key)) {
                        putenv("$key=$value");
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
        
        $config = require __DIR__ . '/../config/database.php';
        
        // Build DSN
        if (!empty($config['unix_socket']) && file_exists($config['unix_socket'])) {
            $dsn = sprintf(
                'mysql:unix_socket=%s;dbname=%s;charset=%s',
                $config['unix_socket'],
                $config['database'],
                $config['charset']
            );
        } else {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
        }
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        $pdo->query('SELECT 1');
        
        $health['checks']['database'] = [
            'status' => 'ok',
            'connected' => true
        ];
    } catch (Exception $e) {
        $health['checks']['database'] = [
            'status' => 'error',
            'connected' => false,
            'error' => $e->getMessage()
        ];
        $health['status'] = 'degraded';
    }
}

// Check 7: Environment Detection
$serverName = $_SERVER['SERVER_NAME'] ?? 'unknown';
$serverAddr = $_SERVER['SERVER_ADDR'] ?? 'unknown';
$localhostNames = ['localhost', '127.0.0.1', '::1'];
$isLocalhost = in_array(strtolower($serverName), $localhostNames) || in_array($serverAddr, $localhostNames);

$health['checks']['environment'] = [
    'status' => 'ok',
    'detected' => $isLocalhost ? 'localhost' : 'production',
    'server_name' => $serverName
];

// Check 8: URL Detection
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$parts = array_filter(explode('/', trim($scriptName, '/')));
$knownAppDirs = ['public', 'admin', 'app', 'install', 'api'];
$firstPart = reset($parts) ?: '';
$isSubdirectory = !empty($firstPart) && !in_array($firstPart, $knownAppDirs);

$health['checks']['url_detection'] = [
    'status' => 'ok',
    'is_subdirectory' => $isSubdirectory,
    'base_path' => $isSubdirectory ? '/' . $firstPart : '',
    'script_name' => $scriptName
];

// Determine overall status
foreach ($health['checks'] as $check) {
    if ($check['status'] === 'error') {
        $health['status'] = 'error';
        break;
    }
}

// Output
echo json_encode($health, JSON_PRETTY_PRINT);
