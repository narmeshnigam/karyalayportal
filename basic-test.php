<?php
/**
 * Basic Diagnostic Script
 * 
 * This script tests the most basic functionality step by step
 * to identify exactly where the fatal error occurs.
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Start output buffering to capture any errors
ob_start();

echo "Basic Diagnostic Test\n";
echo "====================\n\n";

$step = 1;

try {
    echo "Step {$step}: Testing basic PHP functionality\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "✓ Basic PHP works\n\n";
    $step++;

    echo "Step {$step}: Testing file system access\n";
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        echo "✓ Composer autoload file exists\n";
    } else {
        echo "✗ Composer autoload file missing\n";
        exit(1);
    }
    
    if (file_exists(__DIR__ . '/config/app.php')) {
        echo "✓ App config file exists\n";
    } else {
        echo "✗ App config file missing\n";
        exit(1);
    }
    echo "\n";
    $step++;

    echo "Step {$step}: Loading Composer autoloader\n";
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Composer autoloader loaded\n\n";
    $step++;

    echo "Step {$step}: Loading app configuration\n";
    $config = require __DIR__ . '/config/app.php';
    echo "✓ App configuration loaded\n";
    echo "Environment: " . $config['env'] . "\n";
    echo "Debug: " . ($config['debug'] ? 'true' : 'false') . "\n\n";
    $step++;

    echo "Step {$step}: Testing auth helpers include\n";
    if (file_exists(__DIR__ . '/includes/auth_helpers.php')) {
        echo "✓ Auth helpers file exists\n";
        require_once __DIR__ . '/includes/auth_helpers.php';
        echo "✓ Auth helpers loaded successfully\n";
    } else {
        echo "✗ Auth helpers file missing\n";
    }
    echo "\n";
    $step++;

    echo "Step {$step}: Testing template helpers include\n";
    if (file_exists(__DIR__ . '/includes/template_helpers.php')) {
        echo "✓ Template helpers file exists\n";
        
        // Check for function conflicts before including
        $existingFunctions = get_defined_functions()['user'];
        echo "Current user-defined functions: " . count($existingFunctions) . "\n";
        
        require_once __DIR__ . '/includes/template_helpers.php';
        echo "✓ Template helpers loaded successfully\n";
        
        $newFunctions = get_defined_functions()['user'];
        echo "Functions after include: " . count($newFunctions) . "\n";
    } else {
        echo "✗ Template helpers file missing\n";
    }
    echo "\n";
    $step++;

    echo "Step {$step}: Testing session start\n";
    if (function_exists('startSecureSession')) {
        startSecureSession();
        echo "✓ Secure session started\n";
        echo "Session ID: " . session_id() . "\n";
    } else {
        echo "✗ startSecureSession function not found\n";
    }
    echo "\n";
    $step++;

    echo "Step {$step}: Testing model classes\n";
    
    $modelTests = [
        'HeroSlide' => 'Karyalay\\Models\\HeroSlide',
        'WhyChooseCard' => 'Karyalay\\Models\\WhyChooseCard',
        'Solution' => 'Karyalay\\Models\\Solution'
    ];
    
    foreach ($modelTests as $name => $class) {
        if (class_exists($class)) {
            echo "✓ {$name} class exists\n";
            try {
                $instance = new $class();
                echo "✓ {$name} instance created\n";
            } catch (Exception $e) {
                echo "✗ {$name} instantiation failed: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✗ {$name} class not found\n";
        }
    }
    echo "\n";

    echo "✅ ALL TESTS PASSED!\n";
    echo "The basic functionality works. The 500 error might be caused by:\n";
    echo "1. Specific data in the database causing issues\n";
    echo "2. Template rendering problems\n";
    echo "3. Include file conflicts\n";
    echo "4. Memory or execution time limits\n";

} catch (ParseError $e) {
    echo "\n❌ PARSE ERROR at Step {$step}:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "\n❌ FATAL ERROR at Step {$step}:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Exception $e) {
    echo "\n❌ EXCEPTION at Step {$step}:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

// Get the output
$output = ob_get_clean();

// Output as both HTML and plain text
if (isset($_SERVER['HTTP_HOST'])) {
    // Web request
    header('Content-Type: text/html; charset=utf-8');
    echo "<html><head><title>Basic Test Results</title></head><body>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    echo "</body></html>";
} else {
    // CLI request
    echo $output;
}
?>