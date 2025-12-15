<?php
/**
 * Seed Solutions Database
 * Creates dummy solutions: Project Management and Inventory Management
 * 
 * Usage: php database/seed_solutions.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Karyalay\Database\Connection;

function generateUuid(): string {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

try {
    $db = Connection::getInstance();
    echo "Seeding solutions...\n\n";

    // Solution 1: Project Management
    $pm_id = generateUuid();
    $pm_styling_id = generateUuid();
    $pm_content_id = generateUuid();

    // Solution 2: Inventory Management
    $im_id = generateUuid();
    $im_styling_id = generateUuid();
    $im_content_id = generateUuid();

    $db->beginTransaction();

    // ========================================================================
    // INSERT INTO solutions TABLE
    // ========================================================================
    $sql = "INSERT INTO solutions (
        id, name, slug, description, tagline, subtitle, icon_image,
        video_url, demo_video_url, color_theme, testimonial_id, pricing_note,
        meta_title, meta_description, meta_keywords,
        display_order, status, featured_on_homepage
    ) VALUES (
        :id, :name, :slug, :description, :tagline, :subtitle, :icon_image,
        :video_url, :demo_video_url, :color_theme, :testimonial_id, :pricing_note,
        :meta_title, :meta_description, :meta_keywords,
        :display_order, :status, :featured_on_homepage
    )";
    $stmt = $db->prepare($sql);

    // Project Management
    $stmt->execute([
        ':id' => $pm_id,
        ':name' => 'Project Management',
        ':slug' => 'project-management',
        ':description' => 'Streamline your projects from planning to delivery with our comprehensive project management solution. Track tasks, manage resources, and collaborate seamlessly with your team.',
        ':tagline' => 'ENTERPRISE PROJECT SOLUTION',
        ':subtitle' => 'Plan, execute, and deliver projects on time and within budget with powerful tools designed for modern teams.',
        ':icon_image' => '/assets/images/solutions/project-management-icon.png',
        ':video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ':demo_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ':color_theme' => '#3b82f6',
        ':testimonial_id' => null,
        ':pricing_note' => 'Starting from $29/user/month. Enterprise plans available.',
        ':meta_title' => 'Project Management Software | Streamline Your Workflow',
        ':meta_description' => 'Powerful project management solution to plan, track, and deliver projects efficiently. Collaborate with your team in real-time.',
        ':meta_keywords' => 'project management, task tracking, team collaboration, gantt charts, agile, scrum',
        ':display_order' => 1,
        ':status' => 'PUBLISHED',
        ':featured_on_homepage' => true
    ]);
    echo "✓ Created solution: Project Management\n";

    // Inventory Management
    $stmt->execute([
        ':id' => $im_id,
        ':name' => 'Inventory Management',
        ':slug' => 'inventory-management',
        ':description' => 'Take control of your inventory with real-time tracking, automated reordering, and comprehensive analytics. Reduce stockouts and optimize your supply chain.',
        ':tagline' => 'SMART INVENTORY CONTROL',
        ':subtitle' => 'Real-time inventory tracking, automated alerts, and powerful analytics to optimize your stock levels and reduce costs.',
        ':icon_image' => '/assets/images/solutions/inventory-management-icon.png',
        ':video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ':demo_video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ':color_theme' => '#10b981',
        ':testimonial_id' => null,
        ':pricing_note' => 'Starting from $49/month. Volume discounts available.',
        ':meta_title' => 'Inventory Management System | Real-Time Stock Control',
        ':meta_description' => 'Comprehensive inventory management solution with real-time tracking, automated reordering, and detailed analytics.',
        ':meta_keywords' => 'inventory management, stock control, warehouse management, supply chain, barcode scanning',
        ':display_order' => 2,
        ':status' => 'PUBLISHED',
        ':featured_on_homepage' => true
    ]);
    echo "✓ Created solution: Inventory Management\n";

    // ========================================================================
    // INSERT INTO solution_styling TABLE
    // ========================================================================
    $sql = "INSERT INTO solution_styling (
        id, solution_id,
        hero_badge, hero_title_text, hero_title_color, hero_subtitle_color,
        hero_media_url, hero_media_type, hero_bg_color, hero_bg_gradient_color,
        hero_bg_gradient_opacity, hero_bg_pattern_opacity,
        hero_cta_primary_text, hero_cta_primary_link,
        hero_cta_secondary_text, hero_cta_secondary_link,
        hero_primary_btn_bg_color, hero_primary_btn_text_color, hero_primary_btn_text_hover_color, hero_primary_btn_border_color,
        hero_secondary_btn_bg_color, hero_secondary_btn_text_color, hero_secondary_btn_text_hover_color, hero_secondary_btn_border_color,
        key_benefits_section_enabled, key_benefits_section_bg_color,
        key_benefits_section_heading1, key_benefits_section_heading2, key_benefits_section_subheading,
        key_benefits_section_heading_color, key_benefits_section_subheading_color,
        key_benefits_section_card_bg_color, key_benefits_section_card_border_color,
        key_benefits_section_card_hover_bg_color, key_benefits_section_card_text_color, key_benefits_section_card_icon_color,
        feature_showcase_section_enabled, feature_showcase_section_title, feature_showcase_section_subtitle,
        feature_showcase_section_bg_color, feature_showcase_section_title_color, feature_showcase_section_subtitle_color,
        feature_showcase_card_bg_color, feature_showcase_card_border_color,
        feature_showcase_card_badge_bg_color, feature_showcase_card_badge_text_color,
        feature_showcase_card_heading_color, feature_showcase_card_text_color, feature_showcase_card_icon_color,
        cta_banner_enabled, cta_banner_image_url, cta_banner_overlay_color, cta_banner_overlay_intensity,
        cta_banner_heading1, cta_banner_heading2, cta_banner_heading_color,
        cta_banner_button_text, cta_banner_button_link, cta_banner_button_bg_color, cta_banner_button_text_color
    ) VALUES (
        :id, :solution_id,
        :hero_badge, :hero_title_text, :hero_title_color, :hero_subtitle_color,
        :hero_media_url, :hero_media_type, :hero_bg_color, :hero_bg_gradient_color,
        :hero_bg_gradient_opacity, :hero_bg_pattern_opacity,
        :hero_cta_primary_text, :hero_cta_primary_link,
        :hero_cta_secondary_text, :hero_cta_secondary_link,
        :hero_primary_btn_bg_color, :hero_primary_btn_text_color, :hero_primary_btn_text_hover_color, :hero_primary_btn_border_color,
        :hero_secondary_btn_bg_color, :hero_secondary_btn_text_color, :hero_secondary_btn_text_hover_color, :hero_secondary_btn_border_color,
        :key_benefits_section_enabled, :key_benefits_section_bg_color,
        :key_benefits_section_heading1, :key_benefits_section_heading2, :key_benefits_section_subheading,
        :key_benefits_section_heading_color, :key_benefits_section_subheading_color,
        :key_benefits_section_card_bg_color, :key_benefits_section_card_border_color,
        :key_benefits_section_card_hover_bg_color, :key_benefits_section_card_text_color, :key_benefits_section_card_icon_color,
        :feature_showcase_section_enabled, :feature_showcase_section_title, :feature_showcase_section_subtitle,
        :feature_showcase_section_bg_color, :feature_showcase_section_title_color, :feature_showcase_section_subtitle_color,
        :feature_showcase_card_bg_color, :feature_showcase_card_border_color,
        :feature_showcase_card_badge_bg_color, :feature_showcase_card_badge_text_color,
        :feature_showcase_card_heading_color, :feature_showcase_card_text_color, :feature_showcase_card_icon_color,
        :cta_banner_enabled, :cta_banner_image_url, :cta_banner_overlay_color, :cta_banner_overlay_intensity,
        :cta_banner_heading1, :cta_banner_heading2, :cta_banner_heading_color,
        :cta_banner_button_text, :cta_banner_button_link, :cta_banner_button_bg_color, :cta_banner_button_text_color
    )";
    $stmt = $db->prepare($sql);

    // Project Management Styling
    $stmt->execute([
        ':id' => $pm_styling_id,
        ':solution_id' => $pm_id,
        ':hero_badge' => 'NEW',
        ':hero_title_text' => 'Project Management',
        ':hero_title_color' => '#FFFFFF',
        ':hero_subtitle_color' => '#E0E7FF',
        ':hero_media_url' => '/assets/images/solutions/project-hero.gif',
        ':hero_media_type' => 'gif',
        ':hero_bg_color' => '#1e3a5f',
        ':hero_bg_gradient_color' => '#3b82f6',
        ':hero_bg_gradient_opacity' => 0.70,
        ':hero_bg_pattern_opacity' => 0.05,
        ':hero_cta_primary_text' => 'Start Free Trial',
        ':hero_cta_primary_link' => '/pricing',
        ':hero_cta_secondary_text' => 'Watch Demo',
        ':hero_cta_secondary_link' => '#demo-video',
        ':hero_primary_btn_bg_color' => '#3b82f6',
        ':hero_primary_btn_text_color' => '#FFFFFF',
        ':hero_primary_btn_text_hover_color' => '#FFFFFF',
        ':hero_primary_btn_border_color' => '#3b82f6',
        ':hero_secondary_btn_bg_color' => 'rgba(255,255,255,0.1)',
        ':hero_secondary_btn_text_color' => '#FFFFFF',
        ':hero_secondary_btn_text_hover_color' => '#3b82f6',
        ':hero_secondary_btn_border_color' => 'rgba(255,255,255,0.3)',
        ':key_benefits_section_enabled' => true,
        ':key_benefits_section_bg_color' => '#0f172a',
        ':key_benefits_section_heading1' => 'Why Choose Our',
        ':key_benefits_section_heading2' => 'Project Solution',
        ':key_benefits_section_subheading' => 'Powerful features designed to help teams deliver projects on time and within budget.',
        ':key_benefits_section_heading_color' => '#FFFFFF',
        ':key_benefits_section_subheading_color' => 'rgba(255,255,255,0.7)',
        ':key_benefits_section_card_bg_color' => 'rgba(59,130,246,0.1)',
        ':key_benefits_section_card_border_color' => 'rgba(59,130,246,0.2)',
        ':key_benefits_section_card_hover_bg_color' => '#3b82f6',
        ':key_benefits_section_card_text_color' => '#FFFFFF',
        ':key_benefits_section_card_icon_color' => '#60a5fa',
        ':feature_showcase_section_enabled' => true,
        ':feature_showcase_section_title' => 'Everything you need to manage projects',
        ':feature_showcase_section_subtitle' => 'From small teams to enterprise organizations, our tools scale with your needs',
        ':feature_showcase_section_bg_color' => '#ffffff',
        ':feature_showcase_section_title_color' => '#1e293b',
        ':feature_showcase_section_subtitle_color' => '#64748b',
        ':feature_showcase_card_bg_color' => '#ffffff',
        ':feature_showcase_card_border_color' => '#e2e8f0',
        ':feature_showcase_card_badge_bg_color' => '#dbeafe',
        ':feature_showcase_card_badge_text_color' => '#1d4ed8',
        ':feature_showcase_card_heading_color' => '#1e293b',
        ':feature_showcase_card_text_color' => '#475569',
        ':feature_showcase_card_icon_color' => '#3b82f6',
        ':cta_banner_enabled' => true,
        ':cta_banner_image_url' => '/assets/images/cta-bg-blue.jpg',
        ':cta_banner_overlay_color' => 'rgba(30,58,95,0.85)',
        ':cta_banner_overlay_intensity' => 0.85,
        ':cta_banner_heading1' => 'Ready to transform',
        ':cta_banner_heading2' => 'your project workflow?',
        ':cta_banner_heading_color' => '#FFFFFF',
        ':cta_banner_button_text' => 'Get Started Free',
        ':cta_banner_button_link' => '/register',
        ':cta_banner_button_bg_color' => '#3b82f6',
        ':cta_banner_button_text_color' => '#FFFFFF'
    ]);
    echo "✓ Created styling: Project Management\n";

    // Inventory Management Styling
    $stmt->execute([
        ':id' => $im_styling_id,
        ':solution_id' => $im_id,
        ':hero_badge' => 'POPULAR',
        ':hero_title_text' => 'Inventory Control',
        ':hero_title_color' => '#FFFFFF',
        ':hero_subtitle_color' => '#D1FAE5',
        ':hero_media_url' => '/assets/images/solutions/inventory-hero.gif',
        ':hero_media_type' => 'gif',
        ':hero_bg_color' => '#064e3b',
        ':hero_bg_gradient_color' => '#10b981',
        ':hero_bg_gradient_opacity' => 0.65,
        ':hero_bg_pattern_opacity' => 0.04,
        ':hero_cta_primary_text' => 'Request Demo',
        ':hero_cta_primary_link' => '/contact',
        ':hero_cta_secondary_text' => 'View Pricing',
        ':hero_cta_secondary_link' => '/pricing',
        ':hero_primary_btn_bg_color' => '#10b981',
        ':hero_primary_btn_text_color' => '#FFFFFF',
        ':hero_primary_btn_text_hover_color' => '#FFFFFF',
        ':hero_primary_btn_border_color' => '#10b981',
        ':hero_secondary_btn_bg_color' => 'rgba(255,255,255,0.1)',
        ':hero_secondary_btn_text_color' => '#FFFFFF',
        ':hero_secondary_btn_text_hover_color' => '#10b981',
        ':hero_secondary_btn_border_color' => 'rgba(255,255,255,0.3)',
        ':key_benefits_section_enabled' => true,
        ':key_benefits_section_bg_color' => '#022c22',
        ':key_benefits_section_heading1' => 'Smart Inventory',
        ':key_benefits_section_heading2' => 'Management',
        ':key_benefits_section_subheading' => 'Reduce stockouts, minimize overstock, and optimize your entire supply chain with intelligent automation.',
        ':key_benefits_section_heading_color' => '#FFFFFF',
        ':key_benefits_section_subheading_color' => 'rgba(255,255,255,0.7)',
        ':key_benefits_section_card_bg_color' => 'rgba(16,185,129,0.1)',
        ':key_benefits_section_card_border_color' => 'rgba(16,185,129,0.2)',
        ':key_benefits_section_card_hover_bg_color' => '#10b981',
        ':key_benefits_section_card_text_color' => '#FFFFFF',
        ':key_benefits_section_card_icon_color' => '#34d399',
        ':feature_showcase_section_enabled' => true,
        ':feature_showcase_section_title' => 'Complete inventory visibility',
        ':feature_showcase_section_subtitle' => 'Track every item across all locations in real-time with powerful analytics',
        ':feature_showcase_section_bg_color' => '#f0fdf4',
        ':feature_showcase_section_title_color' => '#14532d',
        ':feature_showcase_section_subtitle_color' => '#166534',
        ':feature_showcase_card_bg_color' => '#ffffff',
        ':feature_showcase_card_border_color' => '#bbf7d0',
        ':feature_showcase_card_badge_bg_color' => '#dcfce7',
        ':feature_showcase_card_badge_text_color' => '#166534',
        ':feature_showcase_card_heading_color' => '#14532d',
        ':feature_showcase_card_text_color' => '#166534',
        ':feature_showcase_card_icon_color' => '#10b981',
        ':cta_banner_enabled' => true,
        ':cta_banner_image_url' => '/assets/images/cta-bg-green.jpg',
        ':cta_banner_overlay_color' => 'rgba(6,78,59,0.9)',
        ':cta_banner_overlay_intensity' => 0.90,
        ':cta_banner_heading1' => 'Take control of',
        ':cta_banner_heading2' => 'your inventory today',
        ':cta_banner_heading_color' => '#FFFFFF',
        ':cta_banner_button_text' => 'Schedule a Demo',
        ':cta_banner_button_link' => '/contact',
        ':cta_banner_button_bg_color' => '#10b981',
        ':cta_banner_button_text_color' => '#FFFFFF'
    ]);
    echo "✓ Created styling: Inventory Management\n";

    // ========================================================================
    // INSERT INTO solution_content TABLE
    // ========================================================================
    $sql = "INSERT INTO solution_content (
        id, solution_id, features, screenshots, faqs, key_benefits_cards, feature_showcase_cards
    ) VALUES (
        :id, :solution_id, :features, :screenshots, :faqs, :key_benefits_cards, :feature_showcase_cards
    )";
    $stmt = $db->prepare($sql);

    // Project Management Content
    $pm_features = [
        'Gantt charts and timeline views',
        'Task dependencies and milestones',
        'Resource allocation and workload management',
        'Time tracking and timesheets',
        'Real-time collaboration and comments',
        'Custom workflows and automation',
        'Budget tracking and expense management',
        'Advanced reporting and analytics',
        'Integration with 100+ tools',
        'Mobile apps for iOS and Android'
    ];

    $pm_screenshots = [
        '/assets/images/solutions/pm-screenshot-1.png',
        '/assets/images/solutions/pm-screenshot-2.png',
        '/assets/images/solutions/pm-screenshot-3.png'
    ];

    $pm_faqs = [
        ['question' => 'How many projects can I manage?', 'answer' => 'Our platform supports unlimited projects across all plans. You can organize them into portfolios and programs for better visibility.'],
        ['question' => 'Can I import from other tools?', 'answer' => 'Yes! We support imports from Asana, Trello, Jira, Monday.com, and many other popular project management tools.'],
        ['question' => 'Is there a mobile app?', 'answer' => 'Absolutely. Our mobile apps for iOS and Android let you manage projects, update tasks, and collaborate with your team on the go.'],
        ['question' => 'What integrations are available?', 'answer' => 'We integrate with 100+ tools including Slack, Microsoft Teams, Google Workspace, Salesforce, and more.']
    ];

    $pm_key_benefits_cards = [
        ['icon' => 'clock', 'title' => 'Save 10+ Hours Weekly', 'description' => 'Automate repetitive tasks and streamline workflows to focus on what matters most.'],
        ['icon' => 'users', 'title' => 'Team Collaboration', 'description' => 'Keep everyone aligned with real-time updates, comments, and file sharing.'],
        ['icon' => 'chart', 'title' => 'Data-Driven Insights', 'description' => 'Make informed decisions with powerful analytics and customizable reports.'],
        ['icon' => 'shield', 'title' => 'Enterprise Security', 'description' => 'Bank-grade encryption, SSO, and compliance with SOC 2, GDPR, and HIPAA.']
    ];

    $pm_feature_showcase_cards = [
        ['nav_label' => 'Planning', 'badge' => 'Core Feature', 'heading' => 'Visual Project Planning', 'image_url' => '/assets/images/solutions/pm-planning.png', 'features' => ['Drag-and-drop Gantt charts', 'Task dependencies', 'Milestone tracking']],
        ['nav_label' => 'Tracking', 'badge' => 'Popular', 'heading' => 'Real-Time Progress Tracking', 'image_url' => '/assets/images/solutions/pm-tracking.png', 'features' => ['Live dashboards', 'Automated status updates', 'Custom KPIs']],
        ['nav_label' => 'Reports', 'badge' => 'Advanced', 'heading' => 'Comprehensive Reporting', 'image_url' => '/assets/images/solutions/pm-reports.png', 'features' => ['50+ report templates', 'Export to PDF/Excel', 'Scheduled reports']]
    ];

    $stmt->execute([
        ':id' => $pm_content_id,
        ':solution_id' => $pm_id,
        ':features' => json_encode($pm_features),
        ':screenshots' => json_encode($pm_screenshots),
        ':faqs' => json_encode($pm_faqs),
        ':key_benefits_cards' => json_encode($pm_key_benefits_cards),
        ':feature_showcase_cards' => json_encode($pm_feature_showcase_cards)
    ]);
    echo "✓ Created content: Project Management\n";

    // Inventory Management Content
    $im_features = [
        'Real-time stock level tracking',
        'Barcode and QR code scanning',
        'Multi-warehouse management',
        'Automated reorder points',
        'Batch and serial number tracking',
        'Inventory forecasting with AI',
        'Purchase order management',
        'Supplier management',
        'Stock transfer between locations',
        'Comprehensive audit trails'
    ];

    $im_screenshots = [
        '/assets/images/solutions/im-screenshot-1.png',
        '/assets/images/solutions/im-screenshot-2.png',
        '/assets/images/solutions/im-screenshot-3.png'
    ];

    $im_faqs = [
        ['question' => 'How does real-time tracking work?', 'answer' => 'Our system updates inventory levels instantly as transactions occur. Use barcode scanners, mobile apps, or manual entry to keep stock accurate.'],
        ['question' => 'Can I manage multiple warehouses?', 'answer' => 'Yes! Manage unlimited warehouses and locations. Track stock transfers, set location-specific reorder points, and view consolidated reports.'],
        ['question' => 'Does it integrate with my e-commerce platform?', 'answer' => 'We integrate with Shopify, WooCommerce, Amazon, eBay, and 50+ other e-commerce and marketplace platforms.'],
        ['question' => 'How accurate is the forecasting?', 'answer' => 'Our AI-powered forecasting analyzes historical data, seasonality, and trends to predict demand with up to 95% accuracy.']
    ];

    $im_key_benefits_cards = [
        ['icon' => 'package', 'title' => 'Reduce Stockouts by 80%', 'description' => 'Smart alerts and automated reordering ensure you never run out of critical items.'],
        ['icon' => 'trending-down', 'title' => 'Cut Carrying Costs', 'description' => 'Optimize stock levels to reduce excess inventory and free up working capital.'],
        ['icon' => 'zap', 'title' => 'Lightning Fast Operations', 'description' => 'Barcode scanning and mobile apps speed up receiving, picking, and shipping.'],
        ['icon' => 'eye', 'title' => 'Complete Visibility', 'description' => 'Track every item across all locations with real-time dashboards and reports.']
    ];

    $im_feature_showcase_cards = [
        ['nav_label' => 'Tracking', 'badge' => 'Core Feature', 'heading' => 'Real-Time Stock Tracking', 'image_url' => '/assets/images/solutions/im-tracking.png', 'features' => ['Live inventory counts', 'Multi-location sync', 'Barcode scanning']],
        ['nav_label' => 'Automation', 'badge' => 'Smart', 'heading' => 'Intelligent Automation', 'image_url' => '/assets/images/solutions/im-automation.png', 'features' => ['Auto reorder points', 'Low stock alerts', 'Purchase order generation']],
        ['nav_label' => 'Analytics', 'badge' => 'Insights', 'heading' => 'Powerful Analytics', 'image_url' => '/assets/images/solutions/im-analytics.png', 'features' => ['Demand forecasting', 'Inventory turnover', 'Cost analysis']],
        ['nav_label' => 'Integration', 'badge' => 'Connected', 'heading' => 'Seamless Integrations', 'image_url' => '/assets/images/solutions/im-integration.png', 'features' => ['E-commerce platforms', 'Accounting software', 'Shipping carriers']]
    ];

    $stmt->execute([
        ':id' => $im_content_id,
        ':solution_id' => $im_id,
        ':features' => json_encode($im_features),
        ':screenshots' => json_encode($im_screenshots),
        ':faqs' => json_encode($im_faqs),
        ':key_benefits_cards' => json_encode($im_key_benefits_cards),
        ':feature_showcase_cards' => json_encode($im_feature_showcase_cards)
    ]);
    echo "✓ Created content: Inventory Management\n";

    $db->commit();

    echo "\n✅ Successfully seeded 2 solutions with full styling and content!\n";
    echo "\nSolutions created:\n";
    echo "  1. Project Management (ID: $pm_id)\n";
    echo "  2. Inventory Management (ID: $im_id)\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n❌ Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
