<?php
/**
 * Test script to verify feature styling update functionality
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Models\Feature;
use Karyalay\Database\Connection;

echo "Testing Feature Styling Update\n";
echo "==============================\n\n";

try {
    $db = Connection::getInstance();
    $featureModel = new Feature();
    
    // Get a feature to test with
    $features = $featureModel->findAll(['status' => 'PUBLISHED'], 1);
    
    if (empty($features)) {
        echo "No published features found. Trying all features...\n";
        $features = $featureModel->findAll([], 1);
    }
    
    if (empty($features)) {
        echo "❌ No features found in database.\n";
        exit(1);
    }
    
    $feature = $features[0];
    echo "Testing with feature: {$feature['name']} (ID: {$feature['id']})\n\n";
    
    // Check if styling record exists
    $checkSql = "SELECT COUNT(*) FROM feature_styling WHERE feature_id = :feature_id";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute([':feature_id' => $feature['id']]);
    $stylingExists = $checkStmt->fetchColumn() > 0;
    
    echo "Styling record exists: " . ($stylingExists ? "Yes" : "No") . "\n";
    
    if (!$stylingExists) {
        echo "Creating styling record...\n";
        $featureModel->ensureStylingExists($feature['id']);
        echo "✅ Styling record created.\n";
    }
    
    // Test update
    echo "\nTesting styling update...\n";
    $testData = [
        'hero_bg_color' => '#f0f0f0',
        'hero_title_gradient_middle' => '#ff6600',
        'hero_cta_primary_text' => 'Test Button',
        'hero_stats_enabled' => false,
        'hero_stat1_value' => '100+',
    ];
    
    $result = $featureModel->update($feature['id'], $testData);
    
    if ($result) {
        echo "✅ Update successful!\n";
        
        // Verify the update
        $updatedFeature = $featureModel->findById($feature['id']);
        echo "\nVerifying updated values:\n";
        echo "  hero_bg_color: {$updatedFeature['hero_bg_color']} (expected: #f0f0f0)\n";
        echo "  hero_title_gradient_middle: {$updatedFeature['hero_title_gradient_middle']} (expected: #ff6600)\n";
        echo "  hero_cta_primary_text: {$updatedFeature['hero_cta_primary_text']} (expected: Test Button)\n";
        echo "  hero_stats_enabled: " . ($updatedFeature['hero_stats_enabled'] ? 'true' : 'false') . " (expected: false)\n";
        echo "  hero_stat1_value: {$updatedFeature['hero_stat1_value']} (expected: 100+)\n";
        
        // Restore original values
        echo "\nRestoring original values...\n";
        $restoreData = [
            'hero_bg_color' => '#fafafa',
            'hero_title_gradient_middle' => '#667eea',
            'hero_cta_primary_text' => 'Get Started',
            'hero_stats_enabled' => true,
            'hero_stat1_value' => '30+',
        ];
        $featureModel->update($feature['id'], $restoreData);
        echo "✅ Original values restored.\n";
    } else {
        echo "❌ Update failed!\n";
    }
    
    echo "\n✅ Test completed!\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
