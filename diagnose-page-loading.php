<?php
/**
 * Diagnostic Script for Page Loading Issues
 * Tests database connection and template helper functions
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Page Loading Diagnostic</h1>";
echo "<pre>";

// Test 1: Bootstrap loading
echo "\n=== Test 1: Bootstrap Loading ===\n";
try {
    require_once __DIR__ . '/config/bootstrap.php';
    echo "✓ Bootstrap loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Bootstrap failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Database connection
echo "\n=== Test 2: Database Connection ===\n";
try {
    $db = \Karyalay\Database\Connection::getInstance();
    echo "✓ Database connection established\n";
    
    // Test query
    $stmt = $db->query("SELECT COUNT(*) as count FROM settings");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Settings table accessible (found {$result['count']} settings)\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 3: Template helpers
echo "\n=== Test 3: Template Helper Functions ===\n";
try {
    require_once __DIR__ . '/includes/template_helpers.php';
    echo "✓ Template helpers loaded\n";
} catch (Exception $e) {
    echo "✗ Template helpers failed: " . $e->getMessage() . "\n";
}

// Test 4: get_brand_name()
echo "\n=== Test 4: get_brand_name() ===\n";
try {
    $brandName = get_brand_name();
    echo "✓ Brand name: " . $brandName . "\n";
} catch (Exception $e) {
    echo "✗ get_brand_name() failed: " . $e->getMessage() . "\n";
}

// Test 5: get_base_url()
echo "\n=== Test 5: get_base_url() ===\n";
try {
    $baseUrl = get_base_url();
    echo "✓ Base URL: " . $baseUrl . "\n";
} catch (Exception $e) {
    echo "✗ get_base_url() failed: " . $e->getMessage() . "\n";
}

// Test 6: get_footer_company_description()
echo "\n=== Test 6: get_footer_company_description() ===\n";
try {
    $description = get_footer_company_description();
    echo "✓ Footer description: " . substr($description, 0, 50) . "...\n";
} catch (Exception $e) {
    echo "✗ get_footer_company_description() failed: " . $e->getMessage() . "\n";
}

// Test 7: get_logo_light_bg()
echo "\n=== Test 7: get_logo_light_bg() ===\n";
try {
    $logo = get_logo_light_bg();
    echo "✓ Logo light bg: " . ($logo ?? 'null') . "\n";
} catch (Exception $e) {
    echo "✗ get_logo_light_bg() failed: " . $e->getMessage() . "\n";
}

// Test 8: render_brand_logo()
echo "\n=== Test 8: render_brand_logo() ===\n";
try {
    $logoHtml = render_brand_logo('light_bg', 'test-logo', 40);
    echo "✓ Logo HTML generated: " . substr($logoHtml, 0, 100) . "...\n";
} catch (Exception $e) {
    echo "✗ render_brand_logo() failed: " . $e->getMessage() . "\n";
}

// Test 9: Feature model
echo "\n=== Test 9: Feature Model ===\n";
try {
    $featureModel = new \Karyalay\Models\Feature();
    $features = $featureModel->findAll(['status' => 'PUBLISHED']);
    echo "✓ Features loaded: " . count($features) . " features found\n";
} catch (Exception $e) {
    echo "✗ Feature model failed: " . $e->getMessage() . "\n";
}

// Test 10: Solution model
echo "\n=== Test 10: Solution Model ===\n";
try {
    $solutionModel = new \Karyalay\Models\Solution();
    $solutions = $solutionModel->findAll(['status' => 'PUBLISHED']);
    echo "✓ Solutions loaded: " . count($solutions) . " solutions found\n";
} catch (Exception $e) {
    echo "✗ Solution model failed: " . $e->getMessage() . "\n";
}

// Test 11: Check for PHP errors in error log
echo "\n=== Test 11: Recent PHP Errors ===\n";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $errors = file($errorLog);
    $recentErrors = array_slice($errors, -10);
    if (!empty($recentErrors)) {
        echo "Recent errors:\n";
        foreach ($recentErrors as $error) {
            echo $error;
        }
    } else {
        echo "✓ No recent errors in log\n";
    }
} else {
    echo "✓ No error log file found\n";
}

// Test 12: Output buffering test
echo "\n=== Test 12: Output Buffering ===\n";
$obLevel = ob_get_level();
echo "Output buffering level: " . $obLevel . "\n";
if ($obLevel > 0) {
    echo "⚠ Output buffering is active (level $obLevel)\n";
} else {
    echo "✓ No output buffering\n";
}

echo "\n=== Diagnostic Complete ===\n";
echo "</pre>";
