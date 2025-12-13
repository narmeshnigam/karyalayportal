<?php
/**
 * Root Index - Route to Public Directory
 * This file handles routing for the application entry point
 * Works for both local development (XAMPP subdirectory) and production (root domain)
 */

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Parse the request URI to get just the path
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Detect base path dynamically
$basePath = dirname($scriptName);
if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
    $basePath = '';
}

// Remove base path from request to get relative path
$relativePath = $requestPath;
if (!empty($basePath) && strpos($requestPath, $basePath) === 0) {
    $relativePath = substr($requestPath, strlen($basePath));
}
$relativePath = '/' . ltrim($relativePath, '/');

// If accessing assets, uploads, or storage directly, let Apache handle it
if (preg_match('#^/(assets|uploads|storage)/#', $relativePath)) {
    // Check if file exists
    $filePath = __DIR__ . $relativePath;
    if (file_exists($filePath) && is_file($filePath)) {
        // Serve the file with appropriate content type
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];
        
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
        }
        
        readfile($filePath);
        exit;
    }
    
    http_response_code(404);
    exit('File not found');
}

// If accessing admin, app, or install directories, let them handle their own routing
if (preg_match('#^/(admin|app|install)(/|$)#', $relativePath)) {
    // These directories have their own index files and routing
    // The .htaccess should handle this, but as fallback:
    $targetPath = __DIR__ . $relativePath;
    
    // If it's a directory, look for index.php
    if (is_dir($targetPath)) {
        $targetPath = rtrim($targetPath, '/') . '/index.php';
    }
    
    // If path doesn't end with .php, try adding it
    if (!str_ends_with($targetPath, '.php') && !file_exists($targetPath)) {
        $targetPath .= '.php';
    }
    
    if (file_exists($targetPath)) {
        // Change to the directory and include the file
        chdir(dirname($targetPath));
        include $targetPath;
        exit;
    }
}

// Default: redirect to public directory
$queryString = $_SERVER['QUERY_STRING'] ?? '';
$publicPath = $basePath . '/public' . $relativePath;

// Preserve query string if present
if (!empty($queryString)) {
    $publicPath .= '?' . $queryString;
}

// Use 302 redirect for flexibility
header('Location: ' . $publicPath, true, 302);
exit;
