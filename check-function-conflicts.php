<?php
/**
 * Function Conflict Checker
 * 
 * This script checks for function redeclaration issues
 * which seem to be causing the 500 errors.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Function Conflict Check</title>
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
<h1>Function Conflict Check</h1>";

echo "<div class='section'>";
echo "<h2>1. Initial Function State</h2>";
$initialFunctions = get_defined_functions()['user'];
echo "<p>Initial user-defined functions: " . count($initialFunctions) . "</p>";
if (!empty($initialFunctions)) {
    echo "<p>Functions: " . implode(', ', array_slice($initialFunctions, 0, 10)) . "</p>";
    if (count($initialFunctions) > 10) {
        echo "<p><em>... and " . (count($initialFunctions) - 10) . " more</em></p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>2. Check Include Files</h2>";

$includeFiles = [
    'auth_helpers.php' => __DIR__ . '/includes/auth_helpers.php',
    'template_helpers.php' => __DIR__ . '/includes/template_helpers.php',
    'admin_helpers.php' => __DIR__ . '/includes/admin_helpers.php'
];

foreach ($includeFiles as $name => $path) {
    echo "<h3>Checking {$name}</h3>";
    
    if (!file_exists($path)) {
        echo "<p class='error'>✗ File not found: {$path}</p>";
        continue;
    }
    
    echo "<p class='check'>✓ File exists</p>";
    
    // Get functions before include
    $beforeFunctions = get_defined_functions()['user'];
    
    try {
        // Check if file has already been included
        $includedFiles = get_included_files();
        $alreadyIncluded = in_array(realpath($path), array_map('realpath', $includedFiles));
        
        if ($alreadyIncluded) {
            echo "<p class='warning'>⚠ File already included</p>";
        } else {
            // Try to include the file
            require_once $path;
            echo "<p class='check'>✓ File included successfully</p>";
        }
        
        // Get functions after include
        $afterFunctions = get_defined_functions()['user'];
        $newFunctions = array_diff($afterFunctions, $beforeFunctions);
        
        if (!empty($newFunctions)) {
            echo "<p>New functions added: " . count($newFunctions) . "</p>";
            echo "<p>Functions: " . implode(', ', $newFunctions) . "</p>";
            
            // Check for specific problematic functions
            $problematicFunctions = ['has_role', 'is_logged_in', 'get_user_role'];
            foreach ($problematicFunctions as $func) {
                if (in_array($func, $newFunctions)) {
                    echo "<p class='warning'>⚠ Found potentially problematic function: {$func}</p>";
                }
            }
        } else {
            echo "<p>No new functions added (file may have been included already)</p>";
        }
        
    } catch (Error $e) {
        echo "<p class='error'>✗ Fatal error including file: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p class='error'>File: " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p class='error'>Line: " . $e->getLine() . "</p>";
        
        // If it's a redeclaration error, show which function
        if (strpos($e->getMessage(), 'Cannot redeclare') !== false) {
            echo "<p class='error'><strong>This is a function redeclaration error!</strong></p>";
            
            // Try to extract the function name
            if (preg_match('/Cannot redeclare (\w+)\(\)/', $e->getMessage(), $matches)) {
                $funcName = $matches[1];
                echo "<p class='error'>Conflicting function: <strong>{$funcName}</strong></p>";
                
                // Check where this function was first defined
                $reflection = new ReflectionFunction($funcName);
                echo "<p class='error'>First defined in: " . $reflection->getFileName() . " at line " . $reflection->getStartLine() . "</p>";
            }
        }
        break; // Stop processing if we hit an error
    } catch (Exception $e) {
        echo "<p class='error'>✗ Exception including file: " . htmlspecialchars($e->getMessage()) . "</p>";
        break;
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>3. Function Analysis</h2>";

// Check for duplicate function definitions in files
$functionsToCheck = ['has_role', 'is_logged_in', 'get_user_role', 'startSecureSession'];

foreach ($functionsToCheck as $funcName) {
    echo "<h3>Function: {$funcName}</h3>";
    
    if (function_exists($funcName)) {
        echo "<p class='check'>✓ Function exists</p>";
        
        try {
            $reflection = new ReflectionFunction($funcName);
            echo "<p>Defined in: " . $reflection->getFileName() . "</p>";
            echo "<p>Line: " . $reflection->getStartLine() . " - " . $reflection->getEndLine() . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>Could not get reflection info: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='warning'>⚠ Function does not exist</p>";
    }
    
    // Search for function definitions in files
    $searchPaths = [
        __DIR__ . '/includes/auth_helpers.php',
        __DIR__ . '/includes/template_helpers.php',
        __DIR__ . '/includes/admin_helpers.php'
    ];
    
    $foundIn = [];
    foreach ($searchPaths as $searchPath) {
        if (file_exists($searchPath)) {
            $content = file_get_contents($searchPath);
            if (preg_match('/function\s+' . preg_quote($funcName) . '\s*\(/i', $content)) {
                $foundIn[] = basename($searchPath);
            }
        }
    }
    
    if (count($foundIn) > 1) {
        echo "<p class='error'>✗ Function defined in multiple files: " . implode(', ', $foundIn) . "</p>";
    } elseif (count($foundIn) === 1) {
        echo "<p class='check'>✓ Function defined in: " . $foundIn[0] . "</p>";
    } else {
        echo "<p class='warning'>⚠ Function definition not found in checked files</p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>4. Recommendations</h2>";
echo "<p>Based on the analysis above:</p>";
echo "<ul>";
echo "<li>If you see 'Cannot redeclare' errors, there are duplicate function definitions</li>";
echo "<li>Check the files mentioned in the error for duplicate functions</li>";
echo "<li>Use function_exists() checks before defining functions</li>";
echo "<li>Consider using include_once instead of require_once for some files</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>