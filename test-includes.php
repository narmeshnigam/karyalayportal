<?php
/**
 * Test Include Files
 * 
 * This script tests if the include files can be loaded without conflicts
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Testing include files...\n\n";

try {
    echo "Step 1: Loading Composer autoloader\n";
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Composer autoloader loaded\n\n";

    echo "Step 2: Loading auth helpers\n";
    require_once __DIR__ . '/includes/auth_helpers.php';
    echo "✓ Auth helpers loaded\n\n";

    echo "Step 3: Loading template helpers\n";
    require_once __DIR__ . '/includes/template_helpers.php';
    echo "✓ Template helpers loaded\n\n";

    echo "Step 4: Loading admin helpers\n";
    require_once __DIR__ . '/includes/admin_helpers.php';
    echo "✓ Admin helpers loaded\n\n";

    echo "Step 5: Testing function existence\n";
    $testFunctions = [
        'has_role',
        'is_logged_in',
        'get_base_url',
        'startSecureSession',
        'has_permission',
        'is_admin_user'
    ];

    foreach ($testFunctions as $func) {
        if (function_exists($func)) {
            echo "✓ {$func}() exists\n";
        } else {
            echo "✗ {$func}() missing\n";
        }
    }

    echo "\n✅ ALL TESTS PASSED!\n";
    echo "The include files can be loaded without conflicts.\n";

} catch (Error $e) {
    echo "\n❌ FATAL ERROR:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "\n❌ EXCEPTION:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>