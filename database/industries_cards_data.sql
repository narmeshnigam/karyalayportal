-- Industries Cards Data for Solutions
-- This script adds comprehensive industries cards to existing solutions
-- Run after migration 065 has been applied

-- First, let's add the migration if not already applied
-- (Run migration 065 first: php database/run_migration_065.php)

-- Sample industries cards data for different solution types

-- Technology & Software Solutions (Inventory, POS, E-commerce, etc.)
SET @tech_industries = JSON_ARRAY(
    JSON_OBJECT(
        'title', 'Technology & Software',
        'description', 'Empowering tech companies with scalable solutions for rapid growth and innovation in the digital landscape.',
        'image_url', 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop',
        'link_url', '/industries/technology',
        'link_text', 'Explore Tech Solutions'
    ),
    JSON_OBJECT(
        'title', 'Healthcare & Medical',
        'description', 'Supporting healthcare providers with secure, compliant systems that enhance patient care and operational efficiency.',
        'image_url', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop',
        'link_url', '/industries/healthcare',
        'link_text', 'Healthcare Solutions'
    ),
    JSON_OBJECT(
        'title', 'Financial Services',
        'description', 'Delivering robust financial solutions with enterprise-grade security and real-time transaction processing.',
        'image_url', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
        'link_url', '/industries/finance',
        'link_text', 'Finance Solutions'
    ),
    JSON_OBJECT(
        'title', 'E-commerce & Retail',
        'description', 'Transforming retail experiences with seamless omnichannel solutions and intelligent inventory management.',
        'image_url', 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop',
        'link_url', '/industries/retail',
        'link_text', 'Retail Solutions'
    ),
    JSON_OBJECT(
        'title', 'Education & Training',
        'description', 'Enabling educational institutions with modern learning platforms and comprehensive student management tools.',
        'image_url', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&h=600&fit=crop',
        'link_url', '/industries/education',
        'link_text', 'Education Solutions'
    ),
    JSON_OBJECT(
        'title', 'Manufacturing',
        'description', 'Optimizing production workflows with IoT-enabled systems and predictive maintenance for maximum efficiency.',
        'image_url', 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop',
        'link_url', '/industries/manufacturing',
        'link_text', 'Manufacturing Solutions'
    ),
    JSON_OBJECT(
        'title', 'Logistics & Supply Chain',
        'description', 'Streamlining supply chain operations with real-time tracking and automated route optimization solutions.',
        'image_url', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=600&fit=crop',
        'link_url', '/industries/logistics',
        'link_text', 'Logistics Solutions'
    ),
    JSON_OBJECT(
        'title', 'Hospitality & Tourism',
        'description', 'Enhancing guest experiences with integrated booking systems and personalized service management platforms.',
        'image_url', 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=800&h=600&fit=crop',
        'link_url', '/industries/hospitality',
        'link_text', 'Hospitality Solutions'
    ),
    JSON_OBJECT(
        'title', 'Professional Services',
        'description', 'Streamlining operations for consulting firms, agencies, and service providers with project-based workflows.',
        'image_url', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
        'link_url', '/industries/professional-services',
        'link_text', 'Professional Services'
    )
);

-- Manufacturing & Production Solutions
SET @manufacturing_industries = JSON_ARRAY(
    JSON_OBJECT(
        'title', 'Automotive Industry',
        'description', 'Comprehensive solutions for automotive manufacturers with quality control and supply chain integration.',
        'image_url', 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=600&fit=crop',
        'link_url', '/industries/automotive',
        'link_text', 'Automotive Solutions'
    ),
    JSON_OBJECT(
        'title', 'Electronics & Semiconductors',
        'description', 'Precision manufacturing solutions for electronics with component tracking and quality assurance.',
        'image_url', 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
        'link_url', '/industries/electronics',
        'link_text', 'Electronics Solutions'
    ),
    JSON_OBJECT(
        'title', 'Pharmaceuticals',
        'description', 'FDA-compliant manufacturing systems with batch tracking and regulatory reporting capabilities.',
        'image_url', 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&h=600&fit=crop',
        'link_url', '/industries/pharmaceuticals',
        'link_text', 'Pharma Solutions'
    ),
    JSON_OBJECT(
        'title', 'Food & Beverage',
        'description', 'Food-safe manufacturing solutions with traceability, quality control, and regulatory compliance.',
        'image_url', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
        'link_url', '/industries/food-beverage',
        'link_text', 'Food & Beverage'
    ),
    JSON_OBJECT(
        'title', 'Textiles & Apparel',
        'description', 'End-to-end solutions for textile manufacturers with design integration and production planning.',
        'image_url', 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800&h=600&fit=crop',
        'link_url', '/industries/textiles',
        'link_text', 'Textile Solutions'
    ),
    JSON_OBJECT(
        'title', 'Chemical Processing',
        'description', 'Process manufacturing solutions with safety compliance and environmental monitoring.',
        'image_url', 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?w=800&h=600&fit=crop',
        'link_url', '/industries/chemicals',
        'link_text', 'Chemical Solutions'
    ),
    JSON_OBJECT(
        'title', 'Aerospace & Defense',
        'description', 'High-precision manufacturing for aerospace with strict quality standards and compliance tracking.',
        'image_url', 'https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=800&h=600&fit=crop',
        'link_url', '/industries/aerospace',
        'link_text', 'Aerospace Solutions'
    ),
    JSON_OBJECT(
        'title', 'Metal & Mining',
        'description', 'Heavy industry solutions for metal processing with equipment monitoring and safety management.',
        'image_url', 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=800&h=600&fit=crop',
        'link_url', '/industries/metals-mining',
        'link_text', 'Metals & Mining'
    )
);

-- Service-based Solutions (CRM, HR, Accounting, etc.)
SET @services_industries = JSON_ARRAY(
    JSON_OBJECT(
        'title', 'Healthcare Services',
        'description', 'Comprehensive healthcare management with patient records, scheduling, and billing integration.',
        'image_url', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=600&fit=crop',
        'link_url', '/industries/healthcare',
        'link_text', 'Healthcare Solutions'
    ),
    JSON_OBJECT(
        'title', 'Financial Services',
        'description', 'Banking and financial institutions with secure transaction processing and compliance management.',
        'image_url', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
        'link_url', '/industries/finance',
        'link_text', 'Financial Solutions'
    ),
    JSON_OBJECT(
        'title', 'Real Estate',
        'description', 'Property management solutions with tenant tracking, maintenance scheduling, and financial reporting.',
        'image_url', 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop',
        'link_url', '/industries/real-estate',
        'link_text', 'Real Estate Solutions'
    ),
    JSON_OBJECT(
        'title', 'Legal Services',
        'description', 'Law firm management with case tracking, document management, and billing automation.',
        'image_url', 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=800&h=600&fit=crop',
        'link_url', '/industries/legal',
        'link_text', 'Legal Solutions'
    ),
    JSON_OBJECT(
        'title', 'Consulting Services',
        'description', 'Project-based consulting with time tracking, resource allocation, and client management.',
        'image_url', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
        'link_url', '/industries/consulting',
        'link_text', 'Consulting Solutions'
    ),
    JSON_OBJECT(
        'title', 'Marketing Agencies',
        'description', 'Creative agency management with campaign tracking, client collaboration, and performance analytics.',
        'image_url', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
        'link_url', '/industries/marketing',
        'link_text', 'Marketing Solutions'
    ),
    JSON_OBJECT(
        'title', 'IT Services',
        'description', 'Technology service providers with project management, resource planning, and client support.',
        'image_url', 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=800&h=600&fit=crop',
        'link_url', '/industries/it-services',
        'link_text', 'IT Service Solutions'
    ),
    JSON_OBJECT(
        'title', 'Non-Profit Organizations',
        'description', 'Mission-driven organizations with donor management, volunteer coordination, and impact tracking.',
        'image_url', 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=800&h=600&fit=crop',
        'link_url', '/industries/non-profit',
        'link_text', 'Non-Profit Solutions'
    )
);

-- Update solutions with appropriate industry cards based on their type

-- Technology & Software Solutions
UPDATE solution_content sc
JOIN solutions s ON sc.solution_id = s.id
SET sc.industries_cards = @tech_industries
WHERE s.slug IN (
    'inventory-management',
    'purchase-management', 
    'point-of-sale',
    'ecommerce-integration',
    'warehouse-management',
    'supply-chain-management'
);

-- Manufacturing & Production Solutions  
UPDATE solution_content sc
JOIN solutions s ON sc.solution_id = s.id
SET sc.industries_cards = @manufacturing_industries
WHERE s.slug IN (
    'manufacturing-production',
    'quality-management'
);

-- Service-based Solutions
UPDATE solution_content sc
JOIN solutions s ON sc.solution_id = s.id
SET sc.industries_cards = @services_industries
WHERE s.slug IN (
    'sales-crm',
    'accounting-finance',
    'human-resources',
    'project-management',
    'asset-management'
);

-- For any remaining solutions, use the technology set as default
UPDATE solution_content sc
JOIN solutions s ON sc.solution_id = s.id
SET sc.industries_cards = @tech_industries
WHERE sc.industries_cards IS NULL OR JSON_LENGTH(sc.industries_cards) = 0;

-- Verify the updates
SELECT 
    s.name,
    s.slug,
    JSON_LENGTH(sc.industries_cards) as card_count,
    JSON_UNQUOTE(JSON_EXTRACT(sc.industries_cards, '$[0].title')) as first_industry
FROM solutions s
JOIN solution_content sc ON s.id = sc.solution_id
WHERE s.status = 'PUBLISHED'
ORDER BY s.name;