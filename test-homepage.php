<?php
/**
 * Homepage Specific Diagnostic Script
 * 
 * This script tests the exact same code path as the homepage
 * to identify what's causing the 500 error.
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Homepage Diagnostic Test</title>
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
<h1>Homepage Diagnostic Test</h1>";

// Test each step of the homepage loading process
$step = 1;

try {
    echo "<div class='section'>";
    echo "<h2>Step {$step}: Load Composer Autoloader</h2>";
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p class='check'>✓ Composer autoloader loaded successfully</p>";
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Load Configuration</h2>";
    $config = require __DIR__ . '/config/app.php';
    echo "<p class='check'>✓ App configuration loaded</p>";
    echo "<p>Environment: " . htmlspecialchars($config['env']) . "</p>";
    echo "<p>Debug: " . ($config['debug'] ? 'true' : 'false') . "</p>";
    echo "<p>URL: " . htmlspecialchars($config['url']) . "</p>";
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Set Error Reporting</h2>";
    if ($config['debug']) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        echo "<p class='check'>✓ Debug mode enabled - errors will be displayed</p>";
    } else {
        error_reporting(0);
        ini_set('display_errors', '0');
        echo "<p class='check'>✓ Production mode - errors hidden</p>";
    }
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Load Authentication Helpers</h2>";
    require_once __DIR__ . '/includes/auth_helpers.php';
    echo "<p class='check'>✓ Authentication helpers loaded</p>";
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Start Secure Session</h2>";
    startSecureSession();
    echo "<p class='check'>✓ Secure session started</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Load Template Helpers</h2>";
    require_once __DIR__ . '/includes/template_helpers.php';
    echo "<p class='check'>✓ Template helpers loaded</p>";
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Load Model Classes</h2>";
    
    // Test each model class individually
    $models = [
        'HeroSlide' => 'Karyalay\Models\HeroSlide',
        'WhyChooseCard' => 'Karyalay\Models\WhyChooseCard',
        'Solution' => 'Karyalay\Models\Solution',
        'Testimonial' => 'Karyalay\Models\Testimonial',
        'CaseStudy' => 'Karyalay\Models\CaseStudy',
        'BlogPost' => 'Karyalay\Models\BlogPost'
    ];
    
    foreach ($models as $name => $class) {
        try {
            if (class_exists($class)) {
                echo "<p class='check'>✓ {$name} class exists</p>";
                $instance = new $class();
                echo "<p class='check'>✓ {$name} instance created</p>";
            } else {
                echo "<p class='error'>✗ {$name} class not found</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ {$name} error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Test Database Queries</h2>";
    
    try {
        use Karyalay\Models\HeroSlide;
        $heroSlideModel = new HeroSlide();
        $heroSlides = $heroSlideModel->getPublishedSlides();
        echo "<p class='check'>✓ Hero slides query successful (" . count($heroSlides) . " slides)</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Hero slides query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        use Karyalay\Models\WhyChooseCard;
        $whyChooseModel = new WhyChooseCard();
        $whyChooseCards = $whyChooseModel->getPublishedCards(6);
        echo "<p class='check'>✓ Why choose cards query successful (" . count($whyChooseCards) . " cards)</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Why choose cards query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        use Karyalay\Models\Solution;
        $solutionModel = new Solution();
        $featuredSolutions = $solutionModel->getFeaturedSolutions(6);
        echo "<p class='check'>✓ Featured solutions query successful (" . count($featuredSolutions) . " solutions)</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Featured solutions query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        use Karyalay\Models\Testimonial;
        $testimonialModel = new Testimonial();
        $testimonials = $testimonialModel->getFeatured(6);
        echo "<p class='check'>✓ Testimonials query successful (" . count($testimonials) . " testimonials)</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Testimonials query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        use Karyalay\Models\CaseStudy;
        $caseStudyModel = new CaseStudy();
        $featuredCaseStudies = $caseStudyModel->getFeatured(3);
        echo "<p class='check'>✓ Case studies query successful (" . count($featuredCaseStudies) . " case studies)</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Case studies query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        use Karyalay\Models\BlogPost;
        $blogPostModel = new BlogPost();
        $featuredBlogPosts = $blogPostModel->getFeatured(3);
        echo "<p class='check'>✓ Blog posts query successful (" . count($featuredBlogPosts) . " posts)</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Blog posts query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Test Template Functions</h2>";
    
    try {
        $brandName = get_brand_name();
        echo "<p class='check'>✓ get_brand_name() works: " . htmlspecialchars($brandName) . "</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ get_brand_name() failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        $companyDesc = get_footer_company_description();
        echo "<p class='check'>✓ get_footer_company_description() works</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ get_footer_company_description() failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    try {
        $baseUrl = get_base_url();
        echo "<p class='check'>✓ get_base_url() works: " . htmlspecialchars($baseUrl) . "</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ get_base_url() failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</div>";
    $step++;

    echo "<div class='section'>";
    echo "<h2>Step {$step}: Test Header Include</h2>";
    
    try {
        // Capture output to prevent it from displaying
        ob_start();
        include_header('Test Page', 'Test Description');
        $headerOutput = ob_get_clean();
        
        if (strlen($headerOutput) > 0) {
            echo "<p class='check'>✓ Header include successful (" . strlen($headerOutput) . " bytes)</p>";
        } else {
            echo "<p class='warning'>⚠ Header include returned empty output</p>";
        }
    } catch (Exception $e) {
        ob_end_clean(); // Clean up in case of error
        echo "<p class='error'>✗ Header include failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</div>";

    echo "<div class='section'>";
    echo "<h2>✅ All Tests Completed Successfully!</h2>";
    echo "<p>If you're still getting a 500 error on the homepage, the issue might be:</p>";
    echo "<ul>";
    echo "<li>Web server configuration (check .htaccess files)</li>";
    echo "<li>PHP memory limit or execution time limits</li>";
    echo "<li>Missing or incorrect file permissions</li>";
    echo "<li>Web server error logs (check Apache/Nginx error logs)</li>";
    echo "</ul>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2 class='error'>❌ Error at Step {$step}</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p class='error'>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p class='error'>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
} catch (Error $e) {
    echo "<div class='section'>";
    echo "<h2 class='error'>❌ Fatal Error at Step {$step}</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p class='error'>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p class='error'>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
?>