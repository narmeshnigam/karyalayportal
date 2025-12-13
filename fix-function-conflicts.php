<?php
/**
 * Fix Function Conflicts Script
 * 
 * This script automatically adds function_exists() checks to all functions
 * in admin_helpers.php to prevent redeclaration errors.
 */

$adminHelpersPath = __DIR__ . '/includes/admin_helpers.php';

if (!file_exists($adminHelpersPath)) {
    die("Error: admin_helpers.php not found at {$adminHelpersPath}\n");
}

// Read the current content
$content = file_get_contents($adminHelpersPath);

// Create backup
$backupPath = $adminHelpersPath . '.backup.' . date('Y-m-d-H-i-s');
file_put_contents($backupPath, $content);
echo "Backup created: {$backupPath}\n";

// Pattern to match function definitions (not inside classes)
$pattern = '/^(\/\*\*[\s\S]*?\*\/\s*)?^function\s+(\w+)\s*\(/m';

$fixedContent = preg_replace_callback($pattern, function($matches) {
    $docComment = $matches[1] ?? '';
    $functionName = $matches[2];
    
    // Don't wrap functions that already have function_exists checks nearby
    $beforeFunction = substr($matches[0], 0, strpos($matches[0], 'function'));
    if (strpos($beforeFunction, 'function_exists') !== false) {
        return $matches[0]; // Already has check
    }
    
    // Build the replacement with function_exists check
    $replacement = $docComment;
    $replacement .= "if (!function_exists('{$functionName}')) {\n";
    $replacement .= "    function {$functionName}(";
    
    return $replacement;
}, $content);

// Now we need to add closing braces for each function
// This is more complex, so let's do a simpler approach
// We'll add function_exists checks only to the remaining functions that don't have them

// List of functions that need fixing (based on our earlier analysis)
$functionsToFix = [
    'has_permission',
    'has_any_permission', 
    'has_all_permissions',
    'has_any_role',
    'is_admin_user',
    'get_user_roles',
    'get_user_permissions',
    'render_admin_card',
    'format_number',
    'format_currency',
    'get_status_badge',
    'render_admin_pagination',
    'get_relative_time',
    'render_empty_state',
    'format_file_size',
    'get_role_badge',
    'get_role_badges'
];

$originalContent = file_get_contents($adminHelpersPath);

foreach ($functionsToFix as $functionName) {
    // Pattern to find the function definition
    $pattern = '/^(\/\*\*[\s\S]*?\*\/\s*)?^function\s+' . preg_quote($functionName) . '\s*\(/m';
    
    if (preg_match($pattern, $originalContent, $matches, PREG_OFFSET_CAPTURE)) {
        $fullMatch = $matches[0][0];
        $offset = $matches[0][1];
        
        // Check if it already has function_exists check
        $beforeFunction = substr($originalContent, max(0, $offset - 200), 200);
        if (strpos($beforeFunction, "function_exists('{$functionName}')") !== false) {
            echo "Function {$functionName} already has function_exists check, skipping\n";
            continue;
        }
        
        // Find the function body by counting braces
        $functionStart = $offset + strlen($fullMatch);
        $braceCount = 0;
        $inFunction = false;
        $functionEnd = $functionStart;
        
        for ($i = $functionStart; $i < strlen($originalContent); $i++) {
            $char = $originalContent[$i];
            
            if ($char === '{') {
                $braceCount++;
                $inFunction = true;
            } elseif ($char === '}') {
                $braceCount--;
                if ($inFunction && $braceCount === 0) {
                    $functionEnd = $i + 1;
                    break;
                }
            }
        }
        
        // Extract the function
        $functionCode = substr($originalContent, $offset, $functionEnd - $offset);
        
        // Create the wrapped version
        $docComment = $matches[1][0] ?? '';
        $functionDef = substr($functionCode, strlen($docComment));
        
        $wrappedFunction = $docComment;
        $wrappedFunction .= "if (!function_exists('{$functionName}')) {\n";
        $wrappedFunction .= "    " . str_replace("\n", "\n    ", trim($functionDef)) . "\n";
        $wrappedFunction .= "}";
        
        // Replace in content
        $originalContent = substr_replace($originalContent, $wrappedFunction, $offset, $functionEnd - $offset);
        
        echo "Fixed function: {$functionName}\n";
    } else {
        echo "Function {$functionName} not found\n";
    }
}

// Write the fixed content
file_put_contents($adminHelpersPath, $originalContent);
echo "Fixed admin_helpers.php\n";
echo "Original backed up to: {$backupPath}\n";

// Test the fixed file
echo "\nTesting the fixed file...\n";
$testResult = shell_exec("php -l {$adminHelpersPath} 2>&1");
if (strpos($testResult, 'No syntax errors') !== false) {
    echo "✓ Syntax check passed\n";
} else {
    echo "✗ Syntax error found:\n{$testResult}\n";
    echo "Restoring backup...\n";
    file_put_contents($adminHelpersPath, file_get_contents($backupPath));
}
?>