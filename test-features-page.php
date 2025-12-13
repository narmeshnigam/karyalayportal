<?php
/**
 * Test Features Page Loading
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Testing Features Page</h1>";
echo "<pre>";

// Test 1: Bootstrap
echo "\n=== Test 1: Bootstrap ===\n";
try {
    require_once __DIR__ . '/config/bootstrap.php';
    echo "✓ Bootstrap loaded\n";
} catch (Exception $e) {
    echo "✗ Bootstrap failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Template helpers
echo "\n=== Test 2: Template Helpers ===\n";
try {
    require_once __DIR__ . '/includes/template_helpers.php';
    echo "✓ Template helpers loaded\n";
} catch (Exception $e) {
    echo "✗ Template helpers failed: " . $e->getMessage() . "\n";
}

// Test 3: Feature Model
echo "\n=== Test 3: Feature Model ===\n";
try {
    $featureModel = new \Karyalay\Models\Feature();
    echo "✓ Feature model instantiated\n";
    
    $features = $featureModel->findAll(['status' => 'PUBLISHED']);
    echo "✓ Features loaded: " . count($features) . " features\n";
    
    if (!empty($features)) {
        echo "\nFirst feature:\n";
        $first = $features[0];
        echo "  - Name: " . ($first['name'] ?? 'N/A') . "\n";
        echo "  - Slug: " . ($first['slug'] ?? 'N/A') . "\n";
        echo "  - Description: " . substr($first['description'] ?? 'N/A', 0, 50) . "...\n";
        echo "  - Benefits: " . (is_array($first['benefits'] ?? null) ? count($first['benefits']) : 'N/A') . "\n";
    }
} catch (Exception $e) {
    echo "✗ Feature model failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Test 4: Simulate page rendering
echo "\n=== Test 4: Page Rendering Simulation ===\n";
try {
    ob_start();
    
    // Simulate what features.php does
    $page_title = 'Features';
    $page_description = 'Test';
    
    echo "✓ Variables set\n";
    
    // Test include_header
    include_header($page_title, $page_description);
    echo "✓ Header included\n";
    
    // Test CTA variables
    $cta_title = "Test CTA";
    $cta_subtitle = "Test subtitle";
    $cta_source = "test";
    
    echo "✓ CTA variables set\n";
    
    // Test footer
    include_footer();
    echo "✓ Footer included\n";
    
    $output = ob_get_clean();
    echo "✓ Page rendered successfully\n";
    echo "Output length: " . strlen($output) . " bytes\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "✗ Page rendering failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Throwable $e) {
    ob_end_clean();
    echo "✗ Fatal error in page rendering: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Test 5: Check for output buffering issues
echo "\n=== Test 5: Output Buffering ===\n";
$obLevel = ob_get_level();
echo "Current OB level: " . $obLevel . "\n";
if ($obLevel > 0) {
    echo "⚠ Warning: Output buffering is active\n";
}

echo "\n=== Tests Complete ===\n";
echo "</pre>";
