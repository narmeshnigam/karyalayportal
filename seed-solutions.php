<?php
/**
 * Web-accessible Solution Seeder
 * Access via browser: http://localhost/karyalayportal/seed-solutions.php
 * Creates 20 fully-populated ERP solutions for testing
 */

require_once __DIR__ . '/config/bootstrap.php';

use Karyalay\Database\Connection;

header('Content-Type: text/plain');

echo "Seeding 20 ERP Solutions with Razorpay-style data...\n";
echo str_repeat("=", 60) . "\n\n";

$db = Connection::getInstance();

function generateUuid(): string {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$solutions = [
    [
        'name' => 'Inventory Management',
        'slug' => 'inventory-management',
        'tagline' => 'SMART INVENTORY CONTROL',
        'hero_badge' => 'Most Popular',
        'description' => 'Complete inventory tracking and management solution for modern businesses.',
        'subtitle' => 'Track stock levels in real-time, automate reordering, and eliminate stockouts with our AI-powered inventory management system trusted by 10,000+ businesses.',
        'stats' => [['value' => '10M+', 'label' => 'Items Tracked'], ['value' => '99.9%', 'label' => 'Accuracy'], ['value' => '45%', 'label' => 'Cost Reduction'], ['value' => '24/7', 'label' => 'Real-time']],
        'highlight_cards' => [['icon' => 'speed', 'title' => 'Real-time Tracking', 'description' => 'Track every item instantly', 'value' => 'Live'], ['icon' => 'chart', 'title' => 'Smart Analytics', 'description' => 'AI-powered forecasting', 'value' => '95%'], ['icon' => 'security', 'title' => 'Multi-warehouse', 'description' => 'Unlimited locations', 'value' => '∞']],
        'workflow_steps' => [['step' => 1, 'title' => 'Add Products', 'description' => 'Import catalog or add manually', 'icon' => 'upload'], ['step' => 2, 'title' => 'Set Thresholds', 'description' => 'Configure reorder points', 'icon' => 'settings'], ['step' => 3, 'title' => 'Track & Manage', 'description' => 'Monitor in real-time', 'icon' => 'check']],
        'benefits' => [['title' => 'Eliminate Stockouts', 'description' => 'Never lose a sale with predictive alerts'], ['title' => 'Reduce Costs', 'description' => 'Optimize inventory levels'], ['title' => 'Improve Accuracy', 'description' => '99.9% accuracy with barcode scanning']],
        'use_cases' => [['title' => 'Retail Stores', 'description' => 'Multi-location sync', 'icon' => 'shopping'], ['title' => 'Warehouses', 'description' => 'Large-scale management', 'icon' => 'building'], ['title' => 'E-commerce', 'description' => 'Marketplace sync', 'icon' => 'truck']],
        'integrations' => [['name' => 'Shopify', 'logo' => '/assets/integrations/shopify.png', 'description' => 'Store sync'], ['name' => 'Amazon', 'logo' => '/assets/integrations/amazon.png', 'description' => 'FBA sync'], ['name' => 'Tally', 'logo' => '/assets/integrations/tally.png', 'description' => 'Accounting']],
        'faqs' => [['question' => 'Can I track across multiple warehouses?', 'answer' => 'Yes, unlimited warehouse locations with real-time sync.'], ['question' => 'Does it support barcode scanning?', 'answer' => 'Yes, all major barcode formats including QR codes.'], ['question' => 'How do reorder alerts work?', 'answer' => 'Set thresholds and receive automatic email/SMS alerts.']],
        'pricing_note' => 'Free tier for up to 100 SKUs. Enterprise plans available.',
        'color_theme' => '#667eea'
    ],
    [
        'name' => 'Sales & CRM',
        'slug' => 'sales-crm',
        'tagline' => 'CLOSE MORE DEALS',
        'hero_badge' => 'New',
        'description' => 'Powerful CRM to manage leads, track deals, and boost sales productivity.',
        'subtitle' => 'Convert more leads into customers with intelligent pipeline management and automated follow-ups.',
        'stats' => [['value' => '35%', 'label' => 'More Conversions'], ['value' => '2.5x', 'label' => 'Faster Closure'], ['value' => '50K+', 'label' => 'Sales Teams'], ['value' => '₹500Cr+', 'label' => 'Deals Closed']],
        'highlight_cards' => [['icon' => 'users', 'title' => 'Lead Scoring', 'description' => 'AI-powered prioritization', 'value' => 'Smart'], ['icon' => 'chart', 'title' => 'Pipeline View', 'description' => 'Visual deal tracking', 'value' => 'Kanban'], ['icon' => 'clock', 'title' => 'Auto Follow-up', 'description' => 'Never miss a follow-up', 'value' => '100%']],
        'workflow_steps' => [['step' => 1, 'title' => 'Capture Leads', 'description' => 'Import from multiple sources', 'icon' => 'users'], ['step' => 2, 'title' => 'Nurture & Score', 'description' => 'Automated nurturing', 'icon' => 'send'], ['step' => 3, 'title' => 'Close Deals', 'description' => 'Track and close efficiently', 'icon' => 'check']],
        'benefits' => [['title' => 'Unified Customer View', 'description' => '360-degree view of all interactions'], ['title' => 'Automated Workflows', 'description' => 'Email sequences and task assignments'], ['title' => 'Sales Forecasting', 'description' => 'AI-powered revenue predictions']],
        'use_cases' => [['title' => 'B2B Sales', 'description' => 'Enterprise sales cycles', 'icon' => 'building'], ['title' => 'Real Estate', 'description' => 'Property lead management', 'icon' => 'briefcase'], ['title' => 'Services', 'description' => 'Service-based sales', 'icon' => 'users']],
        'integrations' => [['name' => 'Gmail', 'logo' => '/assets/integrations/gmail.png', 'description' => 'Email sync'], ['name' => 'WhatsApp', 'logo' => '/assets/integrations/whatsapp.png', 'description' => 'WhatsApp Business'], ['name' => 'Zoom', 'logo' => '/assets/integrations/zoom.png', 'description' => 'Video meetings']],
        'faqs' => [['question' => 'Can I import existing contacts?', 'answer' => 'Yes, import from Excel, CSV, or other CRMs.'], ['question' => 'Is there a mobile app?', 'answer' => 'Yes, iOS and Android with full functionality.'], ['question' => 'How does lead scoring work?', 'answer' => 'AI analyzes engagement and behavior to score leads.']],
        'pricing_note' => 'Free for teams up to 3 users.',
        'color_theme' => '#10b981'
    ],
    [
        'name' => 'Accounting & Finance',
        'slug' => 'accounting-finance',
        'tagline' => 'FINANCIAL CLARITY',
        'hero_badge' => 'GST Ready',
        'description' => 'Complete accounting with GST compliance, invoicing, and financial reporting.',
        'subtitle' => 'Simplify finances with automated bookkeeping, GST-compliant invoicing, and real-time insights.',
        'stats' => [['value' => '₹10Cr+', 'label' => 'Invoices Generated'], ['value' => '100%', 'label' => 'GST Compliant'], ['value' => '80%', 'label' => 'Time Saved'], ['value' => '25K+', 'label' => 'Businesses']],
        'highlight_cards' => [['icon' => 'security', 'title' => 'GST Ready', 'description' => 'Auto-calculate & file', 'value' => 'GSTIN'], ['icon' => 'speed', 'title' => 'Auto Reconciliation', 'description' => 'Bank statement matching', 'value' => '< 1 min'], ['icon' => 'chart', 'title' => 'Financial Reports', 'description' => 'P&L, Balance Sheet', 'value' => '50+']],
        'workflow_steps' => [['step' => 1, 'title' => 'Connect Bank', 'description' => 'Link accounts securely', 'icon' => 'link'], ['step' => 2, 'title' => 'Record Transactions', 'description' => 'Auto-categorize entries', 'icon' => 'document'], ['step' => 3, 'title' => 'Generate Reports', 'description' => 'Instant financial insights', 'icon' => 'check']],
        'benefits' => [['title' => 'GST Compliance', 'description' => 'Auto-generate GSTR-1, GSTR-3B'], ['title' => 'Multi-currency', 'description' => 'International transactions'], ['title' => 'Audit Trail', 'description' => 'Complete transaction history']],
        'use_cases' => [['title' => 'SMEs', 'description' => 'Small business accounting', 'icon' => 'briefcase'], ['title' => 'Freelancers', 'description' => 'Invoice and expense tracking', 'icon' => 'users'], ['title' => 'Enterprises', 'description' => 'Multi-entity consolidation', 'icon' => 'building']],
        'integrations' => [['name' => 'Tally', 'logo' => '/assets/integrations/tally.png', 'description' => 'Two-way sync'], ['name' => 'ICICI Bank', 'logo' => '/assets/integrations/icici.png', 'description' => 'Bank feed'], ['name' => 'Razorpay', 'logo' => '/assets/integrations/razorpay.png', 'description' => 'Payment reconciliation']],
        'faqs' => [['question' => 'Is it GST compliant?', 'answer' => 'Yes, fully compliant with e-invoices and e-way bills.'], ['question' => 'Can I connect my bank?', 'answer' => 'Yes, direct feeds from all major Indian banks.'], ['question' => 'Does it support TDS?', 'answer' => 'Yes, automatic TDS calculation and Form 26Q.']],
        'pricing_note' => 'Starts at ₹499/month. 14-day free trial.',
        'color_theme' => '#f59e0b'
    ],
    [
        'name' => 'Human Resources',
        'slug' => 'human-resources',
        'tagline' => 'PEOPLE FIRST',
        'hero_badge' => 'All-in-One',
        'description' => 'Complete HR management from recruitment to retirement with payroll.',
        'subtitle' => 'Streamline HR operations - hiring, payroll, attendance, and performance reviews.',
        'stats' => [['value' => '500K+', 'label' => 'Employees Managed'], ['value' => '99%', 'label' => 'Payroll Accuracy'], ['value' => '60%', 'label' => 'HR Time Saved'], ['value' => '15K+', 'label' => 'Companies']],
        'highlight_cards' => [['icon' => 'users', 'title' => 'Employee Self-Service', 'description' => 'ESS portal access', 'value' => '24/7'], ['icon' => 'clock', 'title' => 'Attendance', 'description' => 'Biometric & GPS', 'value' => 'GPS'], ['icon' => 'money', 'title' => 'Payroll', 'description' => 'One-click disbursement', 'value' => '< 5 min']],
        'workflow_steps' => [['step' => 1, 'title' => 'Onboard', 'description' => 'Digital onboarding', 'icon' => 'users'], ['step' => 2, 'title' => 'Track Attendance', 'description' => 'Multiple capture methods', 'icon' => 'clock'], ['step' => 3, 'title' => 'Process Payroll', 'description' => 'Automated calculation', 'icon' => 'send']],
        'benefits' => [['title' => 'Paperless Onboarding', 'description' => 'Digital documents and e-signatures'], ['title' => 'Statutory Compliance', 'description' => 'Auto PF, ESI, PT calculations'], ['title' => 'Performance Management', 'description' => 'Goals and 360-degree feedback']],
        'use_cases' => [['title' => 'IT Companies', 'description' => 'Tech workforce management', 'icon' => 'building'], ['title' => 'Manufacturing', 'description' => 'Shift management', 'icon' => 'truck'], ['title' => 'Retail', 'description' => 'Multi-location HR', 'icon' => 'shopping']],
        'integrations' => [['name' => 'Slack', 'logo' => '/assets/integrations/slack.png', 'description' => 'Notifications'], ['name' => 'Zoho', 'logo' => '/assets/integrations/zoho.png', 'description' => 'Zoho People'], ['name' => 'HDFC Bank', 'logo' => '/assets/integrations/hdfc.png', 'description' => 'Salary disbursement']],
        'faqs' => [['question' => 'Does it handle statutory compliance?', 'answer' => 'Yes, automatic PF, ESI, PT with challan generation.'], ['question' => 'Can employees apply leave online?', 'answer' => 'Yes, complete leave management with approvals.'], ['question' => 'Is biometric supported?', 'answer' => 'Yes, all major devices plus mobile GPS.']],
        'pricing_note' => '₹49 per employee per month.',
        'color_theme' => '#8b5cf6'
    ],
    [
        'name' => 'Purchase Management',
        'slug' => 'purchase-management',
        'tagline' => 'SMART PROCUREMENT',
        'hero_badge' => 'Cost Saver',
        'description' => 'Streamline procurement with vendor management and cost optimization.',
        'subtitle' => 'Control procurement with automated PO generation and spend analytics.',
        'stats' => [['value' => '25%', 'label' => 'Cost Reduction'], ['value' => '₹100Cr+', 'label' => 'Purchases'], ['value' => '3x', 'label' => 'Faster Approvals'], ['value' => '10K+', 'label' => 'Vendors']],
        'highlight_cards' => [['icon' => 'chart', 'title' => 'Spend Analytics', 'description' => 'Track spending patterns', 'value' => 'Real-time'], ['icon' => 'users', 'title' => 'Vendor Portal', 'description' => 'Self-service management', 'value' => 'Portal'], ['icon' => 'check', 'title' => 'Auto PO', 'description' => 'Generate from requisitions', 'value' => '1-Click']],
        'workflow_steps' => [['step' => 1, 'title' => 'Create Requisition', 'description' => 'Raise purchase requests', 'icon' => 'document'], ['step' => 2, 'title' => 'Compare Vendors', 'description' => 'Get and compare quotes', 'icon' => 'users'], ['step' => 3, 'title' => 'Generate PO', 'description' => 'Create purchase orders', 'icon' => 'send']],
        'benefits' => [['title' => 'Vendor Comparison', 'description' => 'Compare quotes side-by-side'], ['title' => 'Approval Workflows', 'description' => 'Multi-level approvals'], ['title' => 'Contract Management', 'description' => 'Track contracts and renewals']],
        'use_cases' => [['title' => 'Manufacturing', 'description' => 'Raw material procurement', 'icon' => 'truck'], ['title' => 'Retail', 'description' => 'Merchandise buying', 'icon' => 'shopping'], ['title' => 'Construction', 'description' => 'Material procurement', 'icon' => 'building']],
        'integrations' => [['name' => 'SAP', 'logo' => '/assets/integrations/sap.png', 'description' => 'SAP integration'], ['name' => 'IndiaMART', 'logo' => '/assets/integrations/indiamart.png', 'description' => 'Vendor discovery'], ['name' => 'Tally', 'logo' => '/assets/integrations/tally.png', 'description' => 'Accounting sync']],
        'faqs' => [['question' => 'Can vendors submit quotes online?', 'answer' => 'Yes, self-service portal for vendors.'], ['question' => 'How does approval work?', 'answer' => 'Multi-level approvals based on amount.'], ['question' => 'Can I track delivery?', 'answer' => 'Yes, GRN and three-way matching.']],
        'pricing_note' => 'Enterprise pricing based on volume.',
        'color_theme' => '#06b6d4'
    ],
    [
        'name' => 'Project Management',
        'slug' => 'project-management',
        'tagline' => 'DELIVER ON TIME',
        'hero_badge' => 'Agile Ready',
        'description' => 'Plan, track, and deliver projects with Gantt charts and collaboration.',
        'subtitle' => 'Keep projects on track with visual planning and real-time progress tracking.',
        'stats' => [['value' => '40%', 'label' => 'Faster Delivery'], ['value' => '100K+', 'label' => 'Projects'], ['value' => '95%', 'label' => 'On-time'], ['value' => '50K+', 'label' => 'Teams']],
        'highlight_cards' => [['icon' => 'chart', 'title' => 'Gantt Charts', 'description' => 'Visual timelines', 'value' => 'Interactive'], ['icon' => 'users', 'title' => 'Resource Planning', 'description' => 'Team allocation', 'value' => 'Smart'], ['icon' => 'clock', 'title' => 'Time Tracking', 'description' => 'Hours per task', 'value' => 'Automatic']],
        'workflow_steps' => [['step' => 1, 'title' => 'Plan Project', 'description' => 'Create tasks and milestones', 'icon' => 'document'], ['step' => 2, 'title' => 'Assign Resources', 'description' => 'Allocate team members', 'icon' => 'users'], ['step' => 3, 'title' => 'Track Progress', 'description' => 'Monitor and report', 'icon' => 'check']],
        'benefits' => [['title' => 'Visual Planning', 'description' => 'Gantt, Kanban, and calendar views'], ['title' => 'Resource Optimization', 'description' => 'Prevent over-allocation'], ['title' => 'Budget Tracking', 'description' => 'Track costs vs budgets']],
        'use_cases' => [['title' => 'IT Projects', 'description' => 'Software development', 'icon' => 'building'], ['title' => 'Construction', 'description' => 'Construction tracking', 'icon' => 'truck'], ['title' => 'Marketing', 'description' => 'Campaign management', 'icon' => 'users']],
        'integrations' => [['name' => 'Jira', 'logo' => '/assets/integrations/jira.png', 'description' => 'Jira sync'], ['name' => 'GitHub', 'logo' => '/assets/integrations/github.png', 'description' => 'Code commits'], ['name' => 'Slack', 'logo' => '/assets/integrations/slack.png', 'description' => 'Team updates']],
        'faqs' => [['question' => 'Does it support Agile?', 'answer' => 'Yes, Scrum and Kanban with sprints.'], ['question' => 'Can I track billable hours?', 'answer' => 'Yes, with client invoicing.'], ['question' => 'Is there a mobile app?', 'answer' => 'Yes, iOS and Android apps.']],
        'pricing_note' => 'Free for up to 5 users.',
        'color_theme' => '#ec4899'
    ],
    [
        'name' => 'Manufacturing & Production',
        'slug' => 'manufacturing-production',
        'tagline' => 'INDUSTRY 4.0',
        'hero_badge' => 'Enterprise',
        'description' => 'End-to-end manufacturing with BOM, production planning, and quality control.',
        'subtitle' => 'Digitize your factory with production planning, BOM management, and shop floor tracking.',
        'stats' => [['value' => '30%', 'label' => 'Efficiency Gain'], ['value' => '1M+', 'label' => 'Units Produced'], ['value' => '99.5%', 'label' => 'Quality Rate'], ['value' => '5K+', 'label' => 'Factories']],
        'highlight_cards' => [['icon' => 'chart', 'title' => 'Production Planning', 'description' => 'MRP and capacity', 'value' => 'MRP II'], ['icon' => 'check', 'title' => 'Quality Control', 'description' => 'Inspection tracking', 'value' => 'ISO'], ['icon' => 'clock', 'title' => 'Shop Floor', 'description' => 'Real-time tracking', 'value' => 'Live']],
        'workflow_steps' => [['step' => 1, 'title' => 'Create BOM', 'description' => 'Define materials and routing', 'icon' => 'document'], ['step' => 2, 'title' => 'Plan Production', 'description' => 'Schedule work orders', 'icon' => 'settings'], ['step' => 3, 'title' => 'Track & Control', 'description' => 'Monitor production', 'icon' => 'check']],
        'benefits' => [['title' => 'BOM Management', 'description' => 'Multi-level BOMs with costing'], ['title' => 'Capacity Planning', 'description' => 'Optimize utilization'], ['title' => 'Traceability', 'description' => 'Batch and serial tracking']],
        'use_cases' => [['title' => 'Discrete Manufacturing', 'description' => 'Assembly and fabrication', 'icon' => 'truck'], ['title' => 'Process Manufacturing', 'description' => 'Chemical and food', 'icon' => 'building'], ['title' => 'Job Shops', 'description' => 'Custom manufacturing', 'icon' => 'briefcase']],
        'integrations' => [['name' => 'IoT Sensors', 'logo' => '/assets/integrations/iot.png', 'description' => 'Machine data'], ['name' => 'SAP', 'logo' => '/assets/integrations/sap.png', 'description' => 'ERP integration'], ['name' => 'AutoCAD', 'logo' => '/assets/integrations/autocad.png', 'description' => 'Design import']],
        'faqs' => [['question' => 'Does it support multi-level BOMs?', 'answer' => 'Yes, unlimited levels with sub-assemblies.'], ['question' => 'Can I track machine downtime?', 'answer' => 'Yes, OEE tracking with maintenance.'], ['question' => 'Is batch tracking supported?', 'answer' => 'Yes, full batch and lot tracking.']],
        'pricing_note' => 'Custom pricing based on factory size.',
        'color_theme' => '#14b8a6'
    ],
    [
        'name' => 'Point of Sale',
        'slug' => 'point-of-sale',
        'tagline' => 'RETAIL SIMPLIFIED',
        'hero_badge' => 'Offline Ready',
        'description' => 'Modern POS for retail with billing, inventory sync, and loyalty.',
        'subtitle' => 'Fast, reliable POS that works online and offline with all payment modes.',
        'stats' => [['value' => '₹500Cr+', 'label' => 'Daily Transactions'], ['value' => '< 3 sec', 'label' => 'Checkout Time'], ['value' => '50K+', 'label' => 'Retail Stores'], ['value' => '99.99%', 'label' => 'Uptime']],
        'highlight_cards' => [['icon' => 'speed', 'title' => 'Quick Billing', 'description' => 'Scan and bill fast', 'value' => '< 3s'], ['icon' => 'globe', 'title' => 'Offline Mode', 'description' => 'Works without internet', 'value' => '100%'], ['icon' => 'money', 'title' => 'All Payments', 'description' => 'Cash, card, UPI', 'value' => '15+']],
        'workflow_steps' => [['step' => 1, 'title' => 'Scan Products', 'description' => 'Barcode or search', 'icon' => 'document'], ['step' => 2, 'title' => 'Apply Discounts', 'description' => 'Promotions and loyalty', 'icon' => 'check'], ['step' => 3, 'title' => 'Collect Payment', 'description' => 'Multiple modes', 'icon' => 'send']],
        'benefits' => [['title' => 'Offline Capability', 'description' => 'Continue without internet'], ['title' => 'Multi-store', 'description' => 'Centralized management'], ['title' => 'Customer Loyalty', 'description' => 'Points and rewards']],
        'use_cases' => [['title' => 'Retail Stores', 'description' => 'Fashion, electronics', 'icon' => 'shopping'], ['title' => 'Restaurants', 'description' => 'Table management', 'icon' => 'heart'], ['title' => 'Pharmacies', 'description' => 'Drug license tracking', 'icon' => 'briefcase']],
        'integrations' => [['name' => 'Paytm', 'logo' => '/assets/integrations/paytm.png', 'description' => 'UPI payments'], ['name' => 'Pine Labs', 'logo' => '/assets/integrations/pinelabs.png', 'description' => 'Card terminals'], ['name' => 'Swiggy', 'logo' => '/assets/integrations/swiggy.png', 'description' => 'Online orders']],
        'faqs' => [['question' => 'Does it work offline?', 'answer' => 'Yes, full offline with auto-sync.'], ['question' => 'What hardware is supported?', 'answer' => 'Tablets, printers, scanners, cash drawers.'], ['question' => 'Can I manage multiple stores?', 'answer' => 'Yes, centralized dashboard.']],
        'pricing_note' => '₹999/month per terminal.',
        'color_theme' => '#ef4444'
    ],
    [
        'name' => 'E-commerce Integration',
        'slug' => 'ecommerce-integration',
        'tagline' => 'OMNICHANNEL SELLING',
        'hero_badge' => 'Multi-channel',
        'description' => 'Sync ERP with online marketplaces for omnichannel operations.',
        'subtitle' => 'Sell everywhere from one platform - Amazon, Flipkart, Shopify, and more.',
        'stats' => [['value' => '15+', 'label' => 'Marketplaces'], ['value' => '₹200Cr+', 'label' => 'GMV Processed'], ['value' => '99.9%', 'label' => 'Sync Accuracy'], ['value' => '10K+', 'label' => 'Sellers']],
        'highlight_cards' => [['icon' => 'globe', 'title' => 'Multi-channel', 'description' => 'Sell on 15+ platforms', 'value' => '15+'], ['icon' => 'speed', 'title' => 'Real-time Sync', 'description' => 'Inventory in seconds', 'value' => '< 30s'], ['icon' => 'chart', 'title' => 'Unified Analytics', 'description' => 'Cross-channel insights', 'value' => 'Dashboard']],
        'workflow_steps' => [['step' => 1, 'title' => 'Connect Channels', 'description' => 'Link marketplace accounts', 'icon' => 'link'], ['step' => 2, 'title' => 'Sync Catalog', 'description' => 'Push products everywhere', 'icon' => 'upload'], ['step' => 3, 'title' => 'Manage Orders', 'description' => 'Centralized processing', 'icon' => 'check']],
        'benefits' => [['title' => 'Inventory Sync', 'description' => 'Real-time stock updates'], ['title' => 'Order Management', 'description' => 'Process from one place'], ['title' => 'Pricing Control', 'description' => 'Channel-specific pricing']],
        'use_cases' => [['title' => 'D2C Brands', 'description' => 'Direct to consumer', 'icon' => 'shopping'], ['title' => 'Distributors', 'description' => 'B2B and B2C', 'icon' => 'truck'], ['title' => 'Retailers', 'description' => 'Online + offline', 'icon' => 'building']],
        'integrations' => [['name' => 'Amazon', 'logo' => '/assets/integrations/amazon.png', 'description' => 'Seller Central'], ['name' => 'Flipkart', 'logo' => '/assets/integrations/flipkart.png', 'description' => 'Seller Hub'], ['name' => 'Shopify', 'logo' => '/assets/integrations/shopify.png', 'description' => 'Shopify stores']],
        'faqs' => [['question' => 'Which marketplaces are supported?', 'answer' => 'Amazon, Flipkart, Myntra, Shopify, and 10+ more.'], ['question' => 'How fast is inventory sync?', 'answer' => 'Near real-time within 30 seconds.'], ['question' => 'Can I manage returns?', 'answer' => 'Yes, centralized returns management.']],
        'pricing_note' => 'Based on order volume. Starts at ₹2,999/month.',
        'color_theme' => '#667eea'
    ],
    [
        'name' => 'Warehouse Management',
        'slug' => 'warehouse-management',
        'tagline' => 'OPTIMIZE SPACE',
        'hero_badge' => 'WMS',
        'description' => 'Advanced WMS with bin locations, pick-pack-ship, and space optimization.',
        'subtitle' => 'Maximize warehouse efficiency with intelligent putaway and wave picking.',
        'stats' => [['value' => '50%', 'label' => 'Faster Picking'], ['value' => '99.8%', 'label' => 'Order Accuracy'], ['value' => '30%', 'label' => 'Space Saved'], ['value' => '2K+', 'label' => 'Warehouses']],
        'highlight_cards' => [['icon' => 'chart', 'title' => 'Bin Management', 'description' => 'Location tracking', 'value' => '3D Map'], ['icon' => 'speed', 'title' => 'Wave Picking', 'description' => 'Optimized routes', 'value' => '50%↑'], ['icon' => 'check', 'title' => 'Cycle Counting', 'description' => 'Continuous audits', 'value' => 'Auto']],
        'workflow_steps' => [['step' => 1, 'title' => 'Receive Goods', 'description' => 'GRN with inspection', 'icon' => 'document'], ['step' => 2, 'title' => 'Putaway', 'description' => 'System-suggested bins', 'icon' => 'upload'], ['step' => 3, 'title' => 'Pick-Pack-Ship', 'description' => 'Optimized fulfillment', 'icon' => 'send']],
        'benefits' => [['title' => 'Space Optimization', 'description' => 'AI-powered slotting'], ['title' => 'Pick Efficiency', 'description' => 'Batch and zone picking'], ['title' => 'Real-time Visibility', 'description' => 'Know where everything is']],
        'use_cases' => [['title' => '3PL Providers', 'description' => 'Multi-client WMS', 'icon' => 'building'], ['title' => 'E-commerce', 'description' => 'Fulfillment centers', 'icon' => 'truck'], ['title' => 'Distribution', 'description' => 'Cross-docking', 'icon' => 'shopping']],
        'integrations' => [['name' => 'Delhivery', 'logo' => '/assets/integrations/delhivery.png', 'description' => 'Shipping'], ['name' => 'Zebra', 'logo' => '/assets/integrations/zebra.png', 'description' => 'Barcode hardware'], ['name' => 'SAP', 'logo' => '/assets/integrations/sap.png', 'description' => 'ERP integration']],
        'faqs' => [['question' => 'Does it support multiple warehouses?', 'answer' => 'Yes, unlimited with inter-warehouse transfers.'], ['question' => 'Can I use RF scanners?', 'answer' => 'Yes, full RF/handheld support.'], ['question' => 'How does putaway work?', 'answer' => 'System suggests optimal bins based on velocity.']],
        'pricing_note' => 'Per-warehouse pricing. Contact sales.',
        'color_theme' => '#10b981'
    ],
    [
        'name' => 'Asset Management',
        'slug' => 'asset-management',
        'tagline' => 'TRACK EVERYTHING',
        'hero_badge' => 'RFID Ready',
        'description' => 'Track fixed assets with depreciation, maintenance, and audits.',
        'subtitle' => 'Complete asset visibility from procurement to disposal.',
        'stats' => [['value' => '₹1000Cr+', 'label' => 'Assets Tracked'], ['value' => '40%', 'label' => 'Maintenance Cost↓'], ['value' => '100%', 'label' => 'Audit Ready'], ['value' => '8K+', 'label' => 'Companies']],
        'highlight_cards' => [['icon' => 'globe', 'title' => 'GPS Tracking', 'description' => 'Real-time location', 'value' => 'Live'], ['icon' => 'clock', 'title' => 'Maintenance', 'description' => 'Preventive alerts', 'value' => 'Auto'], ['icon' => 'chart', 'title' => 'Depreciation', 'description' => 'Multiple methods', 'value' => '5+']],
        'workflow_steps' => [['step' => 1, 'title' => 'Register Assets', 'description' => 'Add with photos and docs', 'icon' => 'document'], ['step' => 2, 'title' => 'Tag & Track', 'description' => 'Barcode/RFID tagging', 'icon' => 'link'], ['step' => 3, 'title' => 'Maintain & Audit', 'description' => 'Schedule maintenance', 'icon' => 'check']],
        'benefits' => [['title' => 'Complete Visibility', 'description' => 'Know where every asset is'], ['title' => 'Compliance Ready', 'description' => 'Audit trails and reports'], ['title' => 'Cost Optimization', 'description' => 'Reduce maintenance costs']],
        'use_cases' => [['title' => 'IT Assets', 'description' => 'Laptops, servers, licenses', 'icon' => 'building'], ['title' => 'Fleet Management', 'description' => 'Vehicles and equipment', 'icon' => 'truck'], ['title' => 'Facilities', 'description' => 'Building infrastructure', 'icon' => 'briefcase']],
        'integrations' => [['name' => 'ServiceNow', 'logo' => '/assets/integrations/servicenow.png', 'description' => 'ITSM'], ['name' => 'RFID Tags', 'logo' => '/assets/integrations/rfid.png', 'description' => 'RFID tracking'], ['name' => 'GPS Devices', 'logo' => '/assets/integrations/gps.png', 'description' => 'Location tracking']],
        'faqs' => [['question' => 'What depreciation methods?', 'answer' => 'Straight-line, declining balance, and more.'], ['question' => 'Can I track movement?', 'answer' => 'Yes, complete movement history.'], ['question' => 'Is RFID supported?', 'answer' => 'Yes, full RFID support.']],
        'pricing_note' => '₹5 per asset per month.',
        'color_theme' => '#f59e0b'
    ],
    [
        'name' => 'Quality Management',
        'slug' => 'quality-management',
        'tagline' => 'ZERO DEFECTS',
        'hero_badge' => 'ISO Certified',
        'description' => 'Comprehensive QMS with inspection, NCR, and compliance tracking.',
        'subtitle' => 'Build a culture of quality with systematic inspection and improvement.',
        'stats' => [['value' => '60%', 'label' => 'Defect Reduction'], ['value' => '100%', 'label' => 'ISO Compliant'], ['value' => '5M+', 'label' => 'Inspections'], ['value' => '3K+', 'label' => 'Manufacturers']],
        'highlight_cards' => [['icon' => 'check', 'title' => 'Inspection Plans', 'description' => 'Configurable checklists', 'value' => 'Custom'], ['icon' => 'chart', 'title' => 'SPC Charts', 'description' => 'Statistical control', 'value' => 'Real-time'], ['icon' => 'security', 'title' => 'CAPA', 'description' => 'Corrective actions', 'value' => '8D']],
        'workflow_steps' => [['step' => 1, 'title' => 'Define Standards', 'description' => 'Create inspection plans', 'icon' => 'document'], ['step' => 2, 'title' => 'Inspect & Record', 'description' => 'Mobile inspection', 'icon' => 'check'], ['step' => 3, 'title' => 'Analyze & Improve', 'description' => 'Root cause analysis', 'icon' => 'settings']],
        'benefits' => [['title' => 'Standardized Processes', 'description' => 'Consistent quality checks'], ['title' => 'Traceability', 'description' => 'Complete audit trail'], ['title' => 'Continuous Improvement', 'description' => 'Data-driven initiatives']],
        'use_cases' => [['title' => 'Manufacturing', 'description' => 'Production QC', 'icon' => 'truck'], ['title' => 'Pharmaceuticals', 'description' => 'GMP compliance', 'icon' => 'heart'], ['title' => 'Food Processing', 'description' => 'FSSAI compliance', 'icon' => 'shopping']],
        'integrations' => [['name' => 'SAP QM', 'logo' => '/assets/integrations/sap.png', 'description' => 'SAP Quality'], ['name' => 'Minitab', 'logo' => '/assets/integrations/minitab.png', 'description' => 'Statistical analysis'], ['name' => 'IoT Sensors', 'logo' => '/assets/integrations/iot.png', 'description' => 'Auto data capture']],
        'faqs' => [['question' => 'Does it support ISO 9001?', 'answer' => 'Yes, fully compliant with ISO standards.'], ['question' => 'Can inspectors use mobile?', 'answer' => 'Yes, mobile app with offline.'], ['question' => 'How does CAPA work?', 'answer' => '8D methodology with root cause analysis.']],
        'pricing_note' => 'Enterprise pricing based on users.',
        'color_theme' => '#8b5cf6'
    ],
    [
        'name' => 'Supply Chain Management',
        'slug' => 'supply-chain-management',
        'tagline' => 'END-TO-END VISIBILITY',
        'hero_badge' => 'AI Powered',
        'description' => 'Optimize supply chain with demand planning and logistics tracking.',
        'subtitle' => 'Complete visibility from demand forecasting to last-mile delivery.',
        'stats' => [['value' => '25%', 'label' => 'Cost Reduction'], ['value' => '95%', 'label' => 'Forecast Accuracy'], ['value' => '₹500Cr+', 'label' => 'Shipments'], ['value' => '1K+', 'label' => 'Enterprises']],
        'highlight_cards' => [['icon' => 'chart', 'title' => 'Demand Planning', 'description' => 'AI forecasting', 'value' => 'ML'], ['icon' => 'globe', 'title' => 'Shipment Tracking', 'description' => 'Real-time logistics', 'value' => 'GPS'], ['icon' => 'users', 'title' => 'Supplier Portal', 'description' => 'Collaborative planning', 'value' => 'B2B']],
        'workflow_steps' => [['step' => 1, 'title' => 'Forecast Demand', 'description' => 'AI-based prediction', 'icon' => 'chart'], ['step' => 2, 'title' => 'Plan Supply', 'description' => 'Optimize procurement', 'icon' => 'settings'], ['step' => 3, 'title' => 'Execute & Track', 'description' => 'Monitor shipments', 'icon' => 'check']],
        'benefits' => [['title' => 'Demand Sensing', 'description' => 'Predict changes early'], ['title' => 'Inventory Optimization', 'description' => 'Right stock, right place'], ['title' => 'Risk Management', 'description' => 'Identify and mitigate risks']],
        'use_cases' => [['title' => 'FMCG', 'description' => 'Consumer goods', 'icon' => 'shopping'], ['title' => 'Automotive', 'description' => 'Just-in-time', 'icon' => 'truck'], ['title' => 'Retail', 'description' => 'Omnichannel fulfillment', 'icon' => 'building']],
        'integrations' => [['name' => 'Blue Dart', 'logo' => '/assets/integrations/bluedart.png', 'description' => 'Logistics'], ['name' => 'Oracle SCM', 'logo' => '/assets/integrations/oracle.png', 'description' => 'ERP integration'], ['name' => 'Weather API', 'logo' => '/assets/integrations/weather.png', 'description' => 'Demand factors']],
        'faqs' => [['question' => 'How accurate is forecasting?', 'answer' => '90-95% accuracy with AI models.'], ['question' => 'Can I track multiple carriers?', 'answer' => 'Yes, unified tracking across 50+ providers.'], ['question' => 'Does it support S&OP?', 'answer' => 'Yes, full Sales & Operations Planning.']],
        'pricing_note' => 'Enterprise solution with custom pricing.',
        'color_theme' => '#06b6d4'
    ],
    [
        'name' => 'Business Intelligence',
        'slug' => 'business-intelligence',
        'tagline' => 'DATA-DRIVEN DECISIONS',
        'hero_badge' => 'Analytics',
        'description' => 'Transform data into insights with dashboards and predictive analytics.',
        'subtitle' => 'Make smarter decisions with real-time dashboards and AI insights.',
        'stats' => [['value' => '100+', 'label' => 'Pre-built Reports'], ['value' => '10x', 'label' => 'Faster Insights'], ['value' => '50+', 'label' => 'Data Sources'], ['value' => '20K+', 'label' => 'Users']],
        'highlight_cards' => [['icon' => 'chart', 'title' => 'Live Dashboards', 'description' => 'Real-time KPIs', 'value' => 'Real-time'], ['icon' => 'speed', 'title' => 'Self-service BI', 'description' => 'Drag-and-drop', 'value' => 'No-code'], ['icon' => 'globe', 'title' => 'Mobile Access', 'description' => 'Insights on the go', 'value' => 'App']],
        'workflow_steps' => [['step' => 1, 'title' => 'Connect Data', 'description' => 'Link all sources', 'icon' => 'link'], ['step' => 2, 'title' => 'Build Reports', 'description' => 'Drag-and-drop creation', 'icon' => 'document'], ['step' => 3, 'title' => 'Share Insights', 'description' => 'Scheduled reports', 'icon' => 'send']],
        'benefits' => [['title' => 'Unified Data View', 'description' => 'Combine multiple sources'], ['title' => 'Self-service Analytics', 'description' => 'No IT help needed'], ['title' => 'Predictive Insights', 'description' => 'AI-powered forecasting']],
        'use_cases' => [['title' => 'Executive Dashboards', 'description' => 'C-suite KPIs', 'icon' => 'briefcase'], ['title' => 'Sales Analytics', 'description' => 'Pipeline analysis', 'icon' => 'chart'], ['title' => 'Operations', 'description' => 'Efficiency metrics', 'icon' => 'building']],
        'integrations' => [['name' => 'Power BI', 'logo' => '/assets/integrations/powerbi.png', 'description' => 'Microsoft BI'], ['name' => 'Tableau', 'logo' => '/assets/integrations/tableau.png', 'description' => 'Tableau export'], ['name' => 'Google Sheets', 'logo' => '/assets/integrations/gsheets.png', 'description' => 'Spreadsheet sync']],
        'faqs' => [['question' => 'Can I create custom reports?', 'answer' => 'Yes, drag-and-drop with 50+ chart types.'], ['question' => 'How often is data refreshed?', 'answer' => 'Real-time or scheduled intervals.'], ['question' => 'Can I embed reports?', 'answer' => 'Yes, embed in websites or apps.']],
        'pricing_note' => 'Included with ERP. Standalone from ₹999/user/month.',
        'color_theme' => '#ec4899'
    ],
    [
        'name' => 'Document Management',
        'slug' => 'document-management',
        'tagline' => 'GO PAPERLESS',
        'hero_badge' => 'DMS',
        'description' => 'Centralized document storage with version control and workflows.',
        'subtitle' => 'Eliminate paper chaos with digital document management.',
        'stats' => [['value' => '10M+', 'label' => 'Documents Stored'], ['value' => '70%', 'label' => 'Time Saved'], ['value' => '256-bit', 'label' => 'Encryption'], ['value' => '15K+', 'label' => 'Organizations']],
        'highlight_cards' => [['icon' => 'security', 'title' => 'Secure Storage', 'description' => 'Bank-grade encryption', 'value' => 'AES-256'], ['icon' => 'clock', 'title' => 'Version Control', 'description' => 'Track all changes', 'value' => 'Unlimited'], ['icon' => 'users', 'title' => 'Collaboration', 'description' => 'Real-time co-editing', 'value' => 'Live']],
        'workflow_steps' => [['step' => 1, 'title' => 'Upload Documents', 'description' => 'Drag-and-drop or scan', 'icon' => 'upload'], ['step' => 2, 'title' => 'Organize & Tag', 'description' => 'AI auto-categorization', 'icon' => 'document'], ['step' => 3, 'title' => 'Share & Collaborate', 'description' => 'Secure sharing', 'icon' => 'users']],
        'benefits' => [['title' => 'Instant Search', 'description' => 'Full-text search with OCR'], ['title' => 'Approval Workflows', 'description' => 'Route for review'], ['title' => 'Compliance Ready', 'description' => 'Audit trails and retention']],
        'use_cases' => [['title' => 'Legal', 'description' => 'Contract management', 'icon' => 'briefcase'], ['title' => 'HR', 'description' => 'Employee documents', 'icon' => 'users'], ['title' => 'Finance', 'description' => 'Invoices and records', 'icon' => 'building']],
        'integrations' => [['name' => 'Google Drive', 'logo' => '/assets/integrations/gdrive.png', 'description' => 'Cloud sync'], ['name' => 'DocuSign', 'logo' => '/assets/integrations/docusign.png', 'description' => 'E-signatures'], ['name' => 'Scanner', 'logo' => '/assets/integrations/scanner.png', 'description' => 'Document scanning']],
        'faqs' => [['question' => 'Can I search inside documents?', 'answer' => 'Yes, full-text search with OCR.'], ['question' => 'How secure is storage?', 'answer' => 'AES-256 encryption, SOC 2 certified.'], ['question' => 'Can I set document expiry?', 'answer' => 'Yes, retention policies with auto-deletion.']],
        'pricing_note' => '₹199/user/month with 100GB storage.',
        'color_theme' => '#14b8a6'
    ],
    [
        'name' => 'Customer Support',
        'slug' => 'customer-support',
        'tagline' => 'DELIGHT CUSTOMERS',
        'hero_badge' => 'Helpdesk',
        'description' => 'Omnichannel helpdesk with ticketing and knowledge base.',
        'subtitle' => 'Deliver exceptional support across all channels.',
        'stats' => [['value' => '50%', 'label' => 'Faster Resolution'], ['value' => '4.8/5', 'label' => 'CSAT Score'], ['value' => '10M+', 'label' => 'Tickets Resolved'], ['value' => '25K+', 'label' => 'Support Teams']],
        'highlight_cards' => [['icon' => 'globe', 'title' => 'Omnichannel', 'description' => 'Email, chat, phone, social', 'value' => '10+'], ['icon' => 'speed', 'title' => 'AI Routing', 'description' => 'Smart ticket assignment', 'value' => 'Auto'], ['icon' => 'chart', 'title' => 'Analytics', 'description' => 'CSAT and SLA tracking', 'value' => 'Real-time']],
        'workflow_steps' => [['step' => 1, 'title' => 'Receive Tickets', 'description' => 'Unified inbox', 'icon' => 'document'], ['step' => 2, 'title' => 'Route & Assign', 'description' => 'AI-powered routing', 'icon' => 'users'], ['step' => 3, 'title' => 'Resolve & Measure', 'description' => 'Track satisfaction', 'icon' => 'check']],
        'benefits' => [['title' => 'Unified Inbox', 'description' => 'All conversations in one place'], ['title' => 'Self-service Portal', 'description' => 'Knowledge base and FAQ'], ['title' => 'SLA Management', 'description' => 'Automatic escalations']],
        'use_cases' => [['title' => 'SaaS Companies', 'description' => 'Product support', 'icon' => 'building'], ['title' => 'E-commerce', 'description' => 'Order support', 'icon' => 'shopping'], ['title' => 'Services', 'description' => 'Service requests', 'icon' => 'users']],
        'integrations' => [['name' => 'Freshdesk', 'logo' => '/assets/integrations/freshdesk.png', 'description' => 'Helpdesk sync'], ['name' => 'WhatsApp', 'logo' => '/assets/integrations/whatsapp.png', 'description' => 'WhatsApp support'], ['name' => 'Intercom', 'logo' => '/assets/integrations/intercom.png', 'description' => 'Live chat']],
        'faqs' => [['question' => 'Which channels are supported?', 'answer' => 'Email, chat, phone, WhatsApp, social media.'], ['question' => 'Can I create a knowledge base?', 'answer' => 'Yes, articles, FAQs, and videos.'], ['question' => 'How does AI routing work?', 'answer' => 'AI routes based on skills and workload.']],
        'pricing_note' => 'Free for up to 3 agents.',
        'color_theme' => '#ef4444'
    ],
    [
        'name' => 'Expense Management',
        'slug' => 'expense-management',
        'tagline' => 'CONTROL SPENDING',
        'hero_badge' => 'Smart',
        'description' => 'Automate expense reporting with receipt scanning and reimbursements.',
        'subtitle' => 'Simplify expenses - snap receipts, auto-fill, get reimbursed faster.',
        'stats' => [['value' => '80%', 'label' => 'Time Saved'], ['value' => '₹50Cr+', 'label' => 'Expenses Processed'], ['value' => '< 48 hrs', 'label' => 'Reimbursement'], ['value' => '30K+', 'label' => 'Employees']],
        'highlight_cards' => [['icon' => 'speed', 'title' => 'Receipt OCR', 'description' => 'Auto-extract data', 'value' => 'AI'], ['icon' => 'check', 'title' => 'Policy Engine', 'description' => 'Auto compliance check', 'value' => '100%'], ['icon' => 'money', 'title' => 'Fast Reimbursement', 'description' => 'Direct bank transfer', 'value' => '< 48h']],
        'workflow_steps' => [['step' => 1, 'title' => 'Capture Receipt', 'description' => 'Snap photo or forward email', 'icon' => 'upload'], ['step' => 2, 'title' => 'Submit Report', 'description' => 'Auto-filled report', 'icon' => 'document'], ['step' => 3, 'title' => 'Get Reimbursed', 'description' => 'Approval and deposit', 'icon' => 'send']],
        'benefits' => [['title' => 'Mobile-first', 'description' => 'Submit from anywhere'], ['title' => 'Policy Compliance', 'description' => 'Automatic policy checks'], ['title' => 'Corporate Cards', 'description' => 'Auto-import transactions']],
        'use_cases' => [['title' => 'Sales Teams', 'description' => 'Travel and entertainment', 'icon' => 'users'], ['title' => 'Field Staff', 'description' => 'Daily allowances', 'icon' => 'truck'], ['title' => 'Executives', 'description' => 'Business travel', 'icon' => 'briefcase']],
        'integrations' => [['name' => 'Corporate Cards', 'logo' => '/assets/integrations/cards.png', 'description' => 'Card feed'], ['name' => 'Uber', 'logo' => '/assets/integrations/uber.png', 'description' => 'Ride receipts'], ['name' => 'MakeMyTrip', 'logo' => '/assets/integrations/mmt.png', 'description' => 'Travel bookings']],
        'faqs' => [['question' => 'How does receipt scanning work?', 'answer' => 'AI OCR extracts vendor, amount, date automatically.'], ['question' => 'Can I set spending limits?', 'answer' => 'Yes, by category, level, or project.'], ['question' => 'How fast are reimbursements?', 'answer' => 'Within 48 hours via bank transfer.']],
        'pricing_note' => '₹99/employee/month.',
        'color_theme' => '#667eea'
    ],
    [
        'name' => 'Compliance & Audit',
        'slug' => 'compliance-audit',
        'tagline' => 'STAY COMPLIANT',
        'hero_badge' => 'GRC',
        'description' => 'Manage regulatory compliance, audits, and risk assessment.',
        'subtitle' => 'Stay ahead of compliance with automated monitoring and audit management.',
        'stats' => [['value' => '100%', 'label' => 'Audit Ready'], ['value' => '50+', 'label' => 'Frameworks'], ['value' => '60%', 'label' => 'Audit Time Saved'], ['value' => '5K+', 'label' => 'Organizations']],
        'highlight_cards' => [['icon' => 'security', 'title' => 'Risk Assessment', 'description' => 'Identify and mitigate', 'value' => 'Matrix'], ['icon' => 'check', 'title' => 'Audit Management', 'description' => 'Plan and execute', 'value' => 'Workflow'], ['icon' => 'chart', 'title' => 'Compliance Dashboard', 'description' => 'Real-time status', 'value' => 'Live']],
        'workflow_steps' => [['step' => 1, 'title' => 'Define Controls', 'description' => 'Map to regulations', 'icon' => 'document'], ['step' => 2, 'title' => 'Monitor Compliance', 'description' => 'Automated checks', 'icon' => 'check'], ['step' => 3, 'title' => 'Audit & Report', 'description' => 'Generate reports', 'icon' => 'send']],
        'benefits' => [['title' => 'Framework Library', 'description' => 'ISO, SOC 2, GDPR templates'], ['title' => 'Evidence Collection', 'description' => 'Automated gathering'], ['title' => 'Risk Scoring', 'description' => 'Quantify and prioritize']],
        'use_cases' => [['title' => 'IT Companies', 'description' => 'SOC 2 and ISO 27001', 'icon' => 'building'], ['title' => 'Financial Services', 'description' => 'RBI and SEBI', 'icon' => 'briefcase'], ['title' => 'Healthcare', 'description' => 'HIPAA compliance', 'icon' => 'heart']],
        'integrations' => [['name' => 'AWS', 'logo' => '/assets/integrations/aws.png', 'description' => 'Cloud compliance'], ['name' => 'Jira', 'logo' => '/assets/integrations/jira.png', 'description' => 'Issue tracking'], ['name' => 'Slack', 'logo' => '/assets/integrations/slack.png', 'description' => 'Notifications']],
        'faqs' => [['question' => 'Which frameworks are supported?', 'answer' => 'ISO 27001, SOC 2, GDPR, HIPAA, and 50+ more.'], ['question' => 'Can I schedule recurring audits?', 'answer' => 'Yes, with automatic task assignment.'], ['question' => 'How does evidence collection work?', 'answer' => 'Automated from integrated systems.']],
        'pricing_note' => 'Enterprise pricing based on organization size.',
        'color_theme' => '#10b981'
    ],
    [
        'name' => 'Fleet Management',
        'slug' => 'fleet-management',
        'tagline' => 'OPTIMIZE FLEET',
        'hero_badge' => 'GPS Enabled',
        'description' => 'Track vehicles, optimize routes, manage drivers, reduce costs.',
        'subtitle' => 'Complete fleet visibility with GPS tracking and route optimization.',
        'stats' => [['value' => '30%', 'label' => 'Fuel Savings'], ['value' => '100K+', 'label' => 'Vehicles Tracked'], ['value' => '25%', 'label' => 'Route Optimization'], ['value' => '10K+', 'label' => 'Fleets']],
        'highlight_cards' => [['icon' => 'globe', 'title' => 'Live Tracking', 'description' => 'Real-time location', 'value' => 'GPS'], ['icon' => 'chart', 'title' => 'Route Optimization', 'description' => 'AI-powered planning', 'value' => '25%↑'], ['icon' => 'clock', 'title' => 'Driver Behavior', 'description' => 'Monitor patterns', 'value' => 'Score']],
        'workflow_steps' => [['step' => 1, 'title' => 'Install Devices', 'description' => 'GPS trackers in vehicles', 'icon' => 'link'], ['step' => 2, 'title' => 'Plan Routes', 'description' => 'Optimize delivery routes', 'icon' => 'settings'], ['step' => 3, 'title' => 'Monitor & Analyze', 'description' => 'Track and improve', 'icon' => 'check']],
        'benefits' => [['title' => 'Fuel Management', 'description' => 'Track consumption and theft'], ['title' => 'Maintenance Alerts', 'description' => 'Preventive scheduling'], ['title' => 'Driver Safety', 'description' => 'Monitor speeding and braking']],
        'use_cases' => [['title' => 'Logistics', 'description' => 'Delivery fleet', 'icon' => 'truck'], ['title' => 'Field Services', 'description' => 'Service vehicles', 'icon' => 'users'], ['title' => 'Transportation', 'description' => 'Passenger transport', 'icon' => 'building']],
        'integrations' => [['name' => 'Google Maps', 'logo' => '/assets/integrations/gmaps.png', 'description' => 'Route planning'], ['name' => 'Fuel Cards', 'logo' => '/assets/integrations/fuel.png', 'description' => 'Fuel tracking'], ['name' => 'OBD Devices', 'logo' => '/assets/integrations/obd.png', 'description' => 'Vehicle diagnostics']],
        'faqs' => [['question' => 'What GPS devices are supported?', 'answer' => '50+ brands plus our own hardware.'], ['question' => 'Can I set geofence alerts?', 'answer' => 'Yes, unlimited geofences with alerts.'], ['question' => 'How does route optimization work?', 'answer' => 'AI considers traffic, windows, and capacity.']],
        'pricing_note' => '₹299/vehicle/month.',
        'color_theme' => '#f59e0b'
    ],
];

// Insert solutions
$insertSql = "INSERT INTO solutions (
    id, name, slug, tagline, subtitle, hero_badge, description,
    hero_cta_primary_text, hero_cta_primary_link, hero_cta_secondary_text, hero_cta_secondary_link,
    stats, highlight_cards, workflow_steps, benefits, use_cases, integrations, faqs,
    pricing_note, color_theme, display_order, status, featured_on_homepage, created_at
) VALUES (
    :id, :name, :slug, :tagline, :subtitle, :hero_badge, :description,
    :hero_cta_primary_text, :hero_cta_primary_link, :hero_cta_secondary_text, :hero_cta_secondary_link,
    :stats, :highlight_cards, :workflow_steps, :benefits, :use_cases, :integrations, :faqs,
    :pricing_note, :color_theme, :display_order, :status, :featured_on_homepage, NOW()
)";

$stmt = $db->prepare($insertSql);
$order = 1;

foreach ($solutions as $solution) {
    try {
        $stmt->execute([
            ':id' => generateUuid(),
            ':name' => $solution['name'],
            ':slug' => $solution['slug'],
            ':tagline' => $solution['tagline'],
            ':subtitle' => $solution['subtitle'],
            ':hero_badge' => $solution['hero_badge'],
            ':description' => $solution['description'],
            ':hero_cta_primary_text' => 'Get Started',
            ':hero_cta_primary_link' => '',
            ':hero_cta_secondary_text' => 'Watch Demo',
            ':hero_cta_secondary_link' => '',
            ':stats' => json_encode($solution['stats']),
            ':highlight_cards' => json_encode($solution['highlight_cards']),
            ':workflow_steps' => json_encode($solution['workflow_steps']),
            ':benefits' => json_encode($solution['benefits']),
            ':use_cases' => json_encode($solution['use_cases']),
            ':integrations' => json_encode($solution['integrations']),
            ':faqs' => json_encode($solution['faqs']),
            ':pricing_note' => $solution['pricing_note'],
            ':color_theme' => $solution['color_theme'],
            ':display_order' => $order,
            ':status' => 'PUBLISHED',
            ':featured_on_homepage' => $order <= 6 ? 1 : 0
        ]);
        echo "✓ Created: {$solution['name']}\n";
        $order++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo "⚠ Skipped (exists): {$solution['name']}\n";
        } else {
            echo "✗ Failed: {$solution['name']} - " . $e->getMessage() . "\n";
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✓ Seeding complete! Created " . count($solutions) . " ERP solutions.\n";
echo "\nView them at: /solutions.php\n";
echo "Detail page example: /solution/inventory-management\n";
