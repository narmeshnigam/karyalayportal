<?php
/**
 * Seed Industries Cards for Solutions
 * 
 * Adds comprehensive industries cards data to all existing solutions.
 * Each solution gets 8+ industry cards with relevant content.
 * 
 * Usage: php database/seed_industries_cards.php
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Seeding Industries Cards for Solutions...\n";
echo str_repeat("=", 60) . "\n\n";

try {
    $db = Connection::getInstance();
    
    // Get all existing solutions
    $stmt = $db->query("SELECT id, name, slug FROM solutions WHERE status = 'PUBLISHED' ORDER BY name");
    $solutions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($solutions)) {
        echo "No published solutions found. Please run solution seeding first.\n";
        exit(1);
    }
    
    echo "Found " . count($solutions) . " solutions to update with industries cards.\n\n";
    
    // Define comprehensive industries cards for different solution types
    $industriesCardSets = [
        // Technology & Software Solutions
        'technology' => [
            [
                'title' => 'Technology & Software',
                'description' => 'Empowering tech companies with scalable solutions for rapid growth and innovation in the digital landscape.',
                'image_url' => 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop',
                'link_url' => '/industries/technology',
                'link_text' => 'Explore Tech Solutions'
            ],
            [
                'title' => 'Healthcare & Medical',
                'description' => 'Supporting healthcare providers with secure, compliant systems that enhance patient care and operational efficiency.',
                'image_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop',
                'link_url' => '/industries/healthcare',
                'link_text' => 'Healthcare Solutions'
            ],
            [
                'title' => 'Financial Services',
                'description' => 'Delivering robust financial solutions with enterprise-grade security and real-time transaction processing.',
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
                'link_url' => '/industries/finance',
                'link_text' => 'Finance Solutions'
            ],
            [
                'title' => 'E-commerce & Retail',
                'description' => 'Transforming retail experiences with seamless omnichannel solutions and intelligent inventory management.',
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop',
                'link_url' => '/industries/retail',
                'link_text' => 'Retail Solutions'
            ],
            [
                'title' => 'Education & Training',
                'description' => 'Enabling educational institutions with modern learning platforms and comprehensive student management tools.',
                'image_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop',
                'link_url' => '/industries/education',
                'link_text' => 'Education Solutions'
            ],
            [
                'title' => 'Manufacturing',
                'description' => 'Optimizing production workflows with IoT-enabled systems and predictive maintenance for maximum efficiency.',
                'image_url' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop',
                'link_url' => '/industries/manufacturing',
                'link_text' => 'Manufacturing Solutions'
            ],
            [
                'title' => 'Logistics & Supply Chain',
                'description' => 'Streamlining supply chain operations with real-time tracking and automated route optimization solutions.',
                'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=600&fit=crop',
                'link_url' => '/industries/logistics',
                'link_text' => 'Logistics Solutions'
            ],
            [
                'title' => 'Hospitality & Tourism',
                'description' => 'Enhancing guest experiences with integrated booking systems and personalized service management platforms.',
                'image_url' => 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=800&h=600&fit=crop',
                'link_url' => '/industries/hospitality',
                'link_text' => 'Hospitality Solutions'
            ],
            [
                'title' => 'Professional Services',
                'description' => 'Streamlining operations for consulting firms, agencies, and service providers with project-based workflows.',
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
                'link_url' => '/industries/professional-services',
                'link_text' => 'Professional Services'
            ]
        ],
        
        // Manufacturing & Production Solutions
        'manufacturing' => [
            [
                'title' => 'Automotive Industry',
                'description' => 'Comprehensive solutions for automotive manufacturers with quality control and supply chain integration.',
                'image_url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=600&fit=crop',
                'link_url' => '/industries/automotive',
                'link_text' => 'Automotive Solutions'
            ],
            [
                'title' => 'Electronics & Semiconductors',
                'description' => 'Precision manufacturing solutions for electronics with component tracking and quality assurance.',
                'image_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
                'link_url' => '/industries/electronics',
                'link_text' => 'Electronics Solutions'
            ],
            [
                'title' => 'Pharmaceuticals',
                'description' => 'FDA-compliant manufacturing systems with batch tracking and regulatory reporting capabilities.',
                'image_url' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&h=600&fit=crop',
                'link_url' => '/industries/pharmaceuticals',
                'link_text' => 'Pharma Solutions'
            ],
            [
                'title' => 'Food & Beverage',
                'description' => 'Food-safe manufacturing solutions with traceability, quality control, and regulatory compliance.',
                'image_url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
                'link_url' => '/industries/food-beverage',
                'link_text' => 'Food & Beverage'
            ],
            [
                'title' => 'Textiles & Apparel',
                'description' => 'End-to-end solutions for textile manufacturers with design integration and production planning.',
                'image_url' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800&h=600&fit=crop',
                'link_url' => '/industries/textiles',
                'link_text' => 'Textile Solutions'
            ],
            [
                'title' => 'Chemical Processing',
                'description' => 'Process manufacturing solutions with safety compliance and environmental monitoring.',
                'image_url' => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?w=800&h=600&fit=crop',
                'link_url' => '/industries/chemicals',
                'link_text' => 'Chemical Solutions'
            ],
            [
                'title' => 'Aerospace & Defense',
                'description' => 'High-precision manufacturing for aerospace with strict quality standards and compliance tracking.',
                'image_url' => 'https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=800&h=600&fit=crop',
                'link_url' => '/industries/aerospace',
                'link_text' => 'Aerospace Solutions'
            ],
            [
                'title' => 'Metal & Mining',
                'description' => 'Heavy industry solutions for metal processing with equipment monitoring and safety management.',
                'image_url' => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=800&h=600&fit=crop',
                'link_url' => '/industries/metals-mining',
                'link_text' => 'Metals & Mining'
            ]
        ],
        
        // Service-based Solutions
        'services' => [
            [
                'title' => 'Healthcare Services',
                'description' => 'Comprehensive healthcare management with patient records, scheduling, and billing integration.',
                'image_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop',
                'link_url' => '/industries/healthcare',
                'link_text' => 'Healthcare Solutions'
            ],
            [
                'title' => 'Financial Services',
                'description' => 'Banking and financial institutions with secure transaction processing and compliance management.',
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
                'link_url' => '/industries/finance',
                'link_text' => 'Financial Solutions'
            ],
            [
                'title' => 'Real Estate',
                'description' => 'Property management solutions with tenant tracking, maintenance scheduling, and financial reporting.',
                'image_url' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop',
                'link_url' => '/industries/real-estate',
                'link_text' => 'Real Estate Solutions'
            ],
            [
                'title' => 'Legal Services',
                'description' => 'Law firm management with case tracking, document management, and billing automation.',
                'image_url' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=800&h=600&fit=crop',
                'link_url' => '/industries/legal',
                'link_text' => 'Legal Solutions'
            ],
            [
                'title' => 'Consulting Services',
                'description' => 'Project-based consulting with time tracking, resource allocation, and client management.',
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
                'link_url' => '/industries/consulting',
                'link_text' => 'Consulting Solutions'
            ],
            [
                'title' => 'Marketing Agencies',
                'description' => 'Creative agency management with campaign tracking, client collaboration, and performance analytics.',
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
                'link_url' => '/industries/marketing',
                'link_text' => 'Marketing Solutions'
            ],
            [
                'title' => 'IT Services',
                'description' => 'Technology service providers with project management, resource planning, and client support.',
                'image_url' => 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop',
                'link_url' => '/industries/it-services',
                'link_text' => 'IT Service Solutions'
            ],
            [
                'title' => 'Non-Profit Organizations',
                'description' => 'Mission-driven organizations with donor management, volunteer coordination, and impact tracking.',
                'image_url' => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=800&h=600&fit=crop',
                'link_url' => '/industries/non-profit',
                'link_text' => 'Non-Profit Solutions'
            ]
        ]
    ];
    
    // Solution-specific industry mappings
    $solutionIndustryMapping = [
        'inventory-management' => 'technology',
        'sales-crm' => 'services',
        'accounting-finance' => 'services',
        'human-resources' => 'services',
        'purchase-management' => 'technology',
        'project-management' => 'services',
        'manufacturing-production' => 'manufacturing',
        'point-of-sale' => 'technology',
        'ecommerce-integration' => 'technology',
        'warehouse-management' => 'technology',
        'asset-management' => 'services',
        'quality-management' => 'manufacturing',
        'supply-chain-management' => 'technology'
    ];
    
    $db->beginTransaction();
    
    $updateCount = 0;
    
    foreach ($solutions as $solution) {
        echo "Processing: {$solution['name']} ({$solution['slug']})\n";
        
        // Determine which industry set to use
        $industrySet = 'technology'; // default
        if (isset($solutionIndustryMapping[$solution['slug']])) {
            $industrySet = $solutionIndustryMapping[$solution['slug']];
        } elseif (strpos($solution['slug'], 'manufacturing') !== false || 
                  strpos($solution['slug'], 'production') !== false ||
                  strpos($solution['slug'], 'quality') !== false) {
            $industrySet = 'manufacturing';
        } elseif (strpos($solution['slug'], 'crm') !== false || 
                  strpos($solution['slug'], 'hr') !== false ||
                  strpos($solution['slug'], 'accounting') !== false ||
                  strpos($solution['slug'], 'finance') !== false) {
            $industrySet = 'services';
        }
        
        $industriesCards = $industriesCardSets[$industrySet];
        
        // Check if solution_content record exists
        $checkStmt = $db->prepare("SELECT id FROM solution_content WHERE solution_id = ?");
        $checkStmt->execute([$solution['id']]);
        $contentExists = $checkStmt->fetch();
        
        if ($contentExists) {
            // Update existing record
            $updateStmt = $db->prepare("
                UPDATE solution_content 
                SET industries_cards = ? 
                WHERE solution_id = ?
            ");
            $updateStmt->execute([
                json_encode($industriesCards),
                $solution['id']
            ]);
        } else {
            // Create new record (shouldn't happen if solutions were seeded properly)
            $insertStmt = $db->prepare("
                INSERT INTO solution_content (id, solution_id, industries_cards) 
                VALUES (?, ?, ?)
            ");
            $uuid = bin2hex(random_bytes(16));
            $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20);
            
            $insertStmt->execute([
                $uuid,
                $solution['id'],
                json_encode($industriesCards)
            ]);
        }
        
        $updateCount++;
        echo "  ✓ Added " . count($industriesCards) . " industry cards\n";
    }
    
    $db->commit();
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ Successfully updated {$updateCount} solutions with industries cards!\n";
    echo "\nIndustry card sets used:\n";
    echo "  • Technology & Software: " . count($industriesCardSets['technology']) . " cards\n";
    echo "  • Manufacturing & Production: " . count($industriesCardSets['manufacturing']) . " cards\n";
    echo "  • Service-based: " . count($industriesCardSets['services']) . " cards\n";
    echo "\nEach solution now has 8+ industry cards with:\n";
    echo "  • Relevant industry titles and descriptions\n";
    echo "  • High-quality Unsplash images\n";
    echo "  • Industry-specific landing page links\n";
    echo "  • Customized call-to-action text\n";
    
} catch (PDOException $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollBack();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}