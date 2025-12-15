<?php
/**
 * Seed 20 ERP Solutions with full Razorpay-style data
 * Run: php database/seed_solutions_razorpay_style.php
 */

require_once __DIR__ . '/../config/bootstrap.php';

use Karyalay\Database\Connection;

echo "Seeding 20 ERP Solutions with Razorpay-style data...\n";
echo str_repeat("=", 60) . "\n\n";

$db = Connection::getInstance();

// Helper function to generate UUID
function generateUuid(): string {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Color themes for variety
$colors = ['#667eea', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6'];

// Define 20 ERP solutions
$solutions = [
    [
        'name' => 'Inventory Management',
        'slug' => 'inventory-management',
        'tagline' => 'SMART INVENTORY CONTROL',
        'hero_badge' => 'Most Popular',
        'description' => 'Complete inventory tracking and management solution for modern businesses.',
        'subtitle' => 'Track stock levels in real-time, automate reordering, and eliminate stockouts with our AI-powered inventory management system trusted by 10,000+ businesses.',
        'stats' => [
            ['value' => '10M+', 'label' => 'Items Tracked Daily'],
            ['value' => '99.9%', 'label' => 'Accuracy Rate'],
            ['value' => '45%', 'label' => 'Cost Reduction'],
            ['value' => '24/7', 'label' => 'Real-time Sync']
        ],
        'highlight_cards' => [
            ['icon' => 'speed', 'title' => 'Real-time Tracking', 'description' => 'Track every item movement instantly', 'value' => 'Live'],
            ['icon' => 'chart', 'title' => 'Smart Analytics', 'description' => 'AI-powered demand forecasting', 'value' => '95%'],
            ['icon' => 'security', 'title' => 'Multi-warehouse', 'description' => 'Manage unlimited locations', 'value' => '∞']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Add Products', 'description' => 'Import your catalog or add items manually', 'icon' => 'upload'],
            ['step' => 2, 'title' => 'Set Thresholds', 'description' => 'Configure reorder points and alerts', 'icon' => 'settings'],
            ['step' => 3, 'title' => 'Track & Manage', 'description' => 'Monitor stock levels in real-time', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Eliminate Stockouts', 'description' => 'Never lose a sale due to out-of-stock items with predictive alerts'],
            ['title' => 'Reduce Carrying Costs', 'description' => 'Optimize inventory levels to minimize storage expenses'],
            ['title' => 'Improve Accuracy', 'description' => 'Barcode scanning and batch tracking ensure 99.9% accuracy']
        ],
        'use_cases' => [
            ['title' => 'Retail Stores', 'description' => 'Multi-location inventory sync for retail chains', 'icon' => 'shopping'],
            ['title' => 'Warehouses', 'description' => 'Large-scale warehouse management', 'icon' => 'building'],
            ['title' => 'E-commerce', 'description' => 'Sync with online marketplaces', 'icon' => 'truck']
        ],
        'integrations' => [
            ['name' => 'Shopify', 'logo' => '/assets/integrations/shopify.png', 'description' => 'Sync with Shopify stores'],
            ['name' => 'Amazon', 'logo' => '/assets/integrations/amazon.png', 'description' => 'FBA inventory sync'],
            ['name' => 'Tally', 'logo' => '/assets/integrations/tally.png', 'description' => 'Accounting integration']
        ],
        'faqs' => [
            ['question' => 'Can I track inventory across multiple warehouses?', 'answer' => 'Yes, our system supports unlimited warehouse locations with real-time sync between all of them.'],
            ['question' => 'Does it support barcode scanning?', 'answer' => 'Absolutely! We support all major barcode formats including QR codes, and work with most handheld scanners.'],
            ['question' => 'How does the reorder alert system work?', 'answer' => 'Set minimum stock thresholds for each product, and receive automatic email/SMS alerts when levels drop below your defined points.']
        ],
        'pricing_note' => 'Start with our free tier for up to 100 SKUs. Enterprise plans available for unlimited inventory.',
        'color_theme' => '#667eea'
    ],
    [
        'name' => 'Sales & CRM',
        'slug' => 'sales-crm',
        'tagline' => 'CLOSE MORE DEALS',
        'hero_badge' => 'New',
        'description' => 'Powerful CRM to manage leads, track deals, and boost your sales team productivity.',
        'subtitle' => 'Convert more leads into customers with intelligent pipeline management, automated follow-ups, and actionable sales insights.',
        'stats' => [
            ['value' => '35%', 'label' => 'More Conversions'],
            ['value' => '2.5x', 'label' => 'Faster Deal Closure'],
            ['value' => '50K+', 'label' => 'Sales Teams'],
            ['value' => '₹500Cr+', 'label' => 'Deals Closed']
        ],
        'highlight_cards' => [
            ['icon' => 'users', 'title' => 'Lead Scoring', 'description' => 'AI-powered lead prioritization', 'value' => 'Smart'],
            ['icon' => 'chart', 'title' => 'Pipeline View', 'description' => 'Visual deal tracking', 'value' => 'Kanban'],
            ['icon' => 'clock', 'title' => 'Auto Follow-up', 'description' => 'Never miss a follow-up', 'value' => '100%']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Capture Leads', 'description' => 'Import leads from multiple sources', 'icon' => 'users'],
            ['step' => 2, 'title' => 'Nurture & Score', 'description' => 'Automated lead nurturing campaigns', 'icon' => 'send'],
            ['step' => 3, 'title' => 'Close Deals', 'description' => 'Track and close deals efficiently', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Unified Customer View', 'description' => '360-degree view of all customer interactions and history'],
            ['title' => 'Automated Workflows', 'description' => 'Set up automated email sequences and task assignments'],
            ['title' => 'Sales Forecasting', 'description' => 'Accurate revenue predictions with AI-powered analytics']
        ],
        'use_cases' => [
            ['title' => 'B2B Sales', 'description' => 'Complex enterprise sales cycles', 'icon' => 'building'],
            ['title' => 'Real Estate', 'description' => 'Property lead management', 'icon' => 'briefcase'],
            ['title' => 'Services', 'description' => 'Service-based business sales', 'icon' => 'users']
        ],
        'integrations' => [
            ['name' => 'Gmail', 'logo' => '/assets/integrations/gmail.png', 'description' => 'Email sync'],
            ['name' => 'WhatsApp', 'logo' => '/assets/integrations/whatsapp.png', 'description' => 'WhatsApp Business'],
            ['name' => 'Zoom', 'logo' => '/assets/integrations/zoom.png', 'description' => 'Video meetings']
        ],
        'faqs' => [
            ['question' => 'Can I import my existing contacts?', 'answer' => 'Yes, import from Excel, CSV, or directly from other CRMs like Salesforce, HubSpot, etc.'],
            ['question' => 'Is there a mobile app?', 'answer' => 'Yes, our mobile app is available for both iOS and Android with full CRM functionality.'],
            ['question' => 'How does lead scoring work?', 'answer' => 'Our AI analyzes engagement patterns, demographics, and behavior to automatically score and prioritize leads.']
        ],
        'pricing_note' => 'Free for teams up to 3 users. Scale as you grow with flexible per-user pricing.',
        'color_theme' => '#10b981'
    ],
    [
        'name' => 'Accounting & Finance',
        'slug' => 'accounting-finance',
        'tagline' => 'FINANCIAL CLARITY',
        'hero_badge' => 'GST Ready',
        'description' => 'Complete accounting solution with GST compliance, invoicing, and financial reporting.',
        'subtitle' => 'Simplify your finances with automated bookkeeping, GST-compliant invoicing, and real-time financial insights for smarter business decisions.',
        'stats' => [
            ['value' => '₹10Cr+', 'label' => 'Invoices Generated'],
            ['value' => '100%', 'label' => 'GST Compliant'],
            ['value' => '80%', 'label' => 'Time Saved'],
            ['value' => '25K+', 'label' => 'Businesses']
        ],
        'highlight_cards' => [
            ['icon' => 'security', 'title' => 'GST Ready', 'description' => 'Auto-calculate GST & file returns', 'value' => 'GSTIN'],
            ['icon' => 'speed', 'title' => 'Auto Reconciliation', 'description' => 'Bank statement matching', 'value' => '< 1 min'],
            ['icon' => 'chart', 'title' => 'Financial Reports', 'description' => 'P&L, Balance Sheet, Cash Flow', 'value' => '50+']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Connect Bank', 'description' => 'Link your bank accounts securely', 'icon' => 'link'],
            ['step' => 2, 'title' => 'Record Transactions', 'description' => 'Auto-categorize income & expenses', 'icon' => 'document'],
            ['step' => 3, 'title' => 'Generate Reports', 'description' => 'Get instant financial insights', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'GST Compliance', 'description' => 'Auto-generate GSTR-1, GSTR-3B with one-click filing'],
            ['title' => 'Multi-currency Support', 'description' => 'Handle international transactions with automatic conversion'],
            ['title' => 'Audit Trail', 'description' => 'Complete transaction history for compliance and audits']
        ],
        'use_cases' => [
            ['title' => 'SMEs', 'description' => 'Small business accounting', 'icon' => 'briefcase'],
            ['title' => 'Freelancers', 'description' => 'Invoice and expense tracking', 'icon' => 'users'],
            ['title' => 'Enterprises', 'description' => 'Multi-entity consolidation', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'Tally', 'logo' => '/assets/integrations/tally.png', 'description' => 'Two-way Tally sync'],
            ['name' => 'ICICI Bank', 'logo' => '/assets/integrations/icici.png', 'description' => 'Direct bank feed'],
            ['name' => 'Razorpay', 'logo' => '/assets/integrations/razorpay.png', 'description' => 'Payment reconciliation']
        ],
        'faqs' => [
            ['question' => 'Is it GST compliant?', 'answer' => 'Yes, fully compliant with Indian GST regulations. Generate e-invoices, e-way bills, and file returns directly.'],
            ['question' => 'Can I connect my bank account?', 'answer' => 'Yes, we support direct bank feeds from all major Indian banks for automatic transaction import.'],
            ['question' => 'Does it support TDS?', 'answer' => 'Yes, automatic TDS calculation, deduction tracking, and Form 26Q generation is included.']
        ],
        'pricing_note' => 'Starts at ₹499/month. Free trial for 14 days with full features.',
        'color_theme' => '#f59e0b'
    ],
    [
        'name' => 'Human Resources',
        'slug' => 'human-resources',
        'tagline' => 'PEOPLE FIRST',
        'hero_badge' => 'All-in-One',
        'description' => 'Complete HR management from recruitment to retirement with payroll and compliance.',
        'subtitle' => 'Streamline your entire HR operations - from hiring to payroll, attendance to performance reviews - all in one unified platform.',
        'stats' => [
            ['value' => '500K+', 'label' => 'Employees Managed'],
            ['value' => '99%', 'label' => 'Payroll Accuracy'],
            ['value' => '60%', 'label' => 'HR Time Saved'],
            ['value' => '15K+', 'label' => 'Companies']
        ],
        'highlight_cards' => [
            ['icon' => 'users', 'title' => 'Employee Self-Service', 'description' => 'Empower employees with ESS portal', 'value' => '24/7'],
            ['icon' => 'clock', 'title' => 'Attendance Tracking', 'description' => 'Biometric & geo-fencing support', 'value' => 'GPS'],
            ['icon' => 'money', 'title' => 'Payroll Processing', 'description' => 'One-click salary disbursement', 'value' => '< 5 min']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Onboard Employees', 'description' => 'Digital onboarding with document collection', 'icon' => 'users'],
            ['step' => 2, 'title' => 'Track Attendance', 'description' => 'Multiple attendance capture methods', 'icon' => 'clock'],
            ['step' => 3, 'title' => 'Process Payroll', 'description' => 'Automated salary calculation & disbursement', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Paperless Onboarding', 'description' => 'Digital document collection and e-signatures'],
            ['title' => 'Statutory Compliance', 'description' => 'Auto-calculate PF, ESI, PT, and generate challans'],
            ['title' => 'Performance Management', 'description' => 'Goal setting, reviews, and 360-degree feedback']
        ],
        'use_cases' => [
            ['title' => 'IT Companies', 'description' => 'Tech workforce management', 'icon' => 'building'],
            ['title' => 'Manufacturing', 'description' => 'Shift and overtime management', 'icon' => 'truck'],
            ['title' => 'Retail Chains', 'description' => 'Multi-location HR', 'icon' => 'shopping']
        ],
        'integrations' => [
            ['name' => 'Slack', 'logo' => '/assets/integrations/slack.png', 'description' => 'Team notifications'],
            ['name' => 'Zoho', 'logo' => '/assets/integrations/zoho.png', 'description' => 'Zoho People sync'],
            ['name' => 'HDFC Bank', 'logo' => '/assets/integrations/hdfc.png', 'description' => 'Salary disbursement']
        ],
        'faqs' => [
            ['question' => 'Does it handle statutory compliance?', 'answer' => 'Yes, automatic PF, ESI, PT calculations with challan generation and return filing support.'],
            ['question' => 'Can employees apply for leave online?', 'answer' => 'Yes, complete leave management with approval workflows, balance tracking, and calendar integration.'],
            ['question' => 'Is biometric integration supported?', 'answer' => 'Yes, we integrate with all major biometric devices and also support mobile attendance with GPS.']
        ],
        'pricing_note' => '₹49 per employee per month. Volume discounts available for 100+ employees.',
        'color_theme' => '#8b5cf6'
    ],
    [
        'name' => 'Purchase Management',
        'slug' => 'purchase-management',
        'tagline' => 'SMART PROCUREMENT',
        'hero_badge' => 'Cost Saver',
        'description' => 'Streamline procurement with vendor management, purchase orders, and cost optimization.',
        'subtitle' => 'Take control of your procurement process with automated PO generation, vendor comparison, and spend analytics to reduce costs by up to 25%.',
        'stats' => [
            ['value' => '25%', 'label' => 'Cost Reduction'],
            ['value' => '₹100Cr+', 'label' => 'Purchases Processed'],
            ['value' => '3x', 'label' => 'Faster Approvals'],
            ['value' => '10K+', 'label' => 'Vendors Managed']
        ],
        'highlight_cards' => [
            ['icon' => 'chart', 'title' => 'Spend Analytics', 'description' => 'Track spending patterns', 'value' => 'Real-time'],
            ['icon' => 'users', 'title' => 'Vendor Portal', 'description' => 'Self-service vendor management', 'value' => 'Portal'],
            ['icon' => 'check', 'title' => 'Auto PO', 'description' => 'Generate POs from requisitions', 'value' => '1-Click']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Create Requisition', 'description' => 'Raise purchase requests with approvals', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Compare Vendors', 'description' => 'Get quotes and compare prices', 'icon' => 'users'],
            ['step' => 3, 'title' => 'Generate PO', 'description' => 'Create and send purchase orders', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Vendor Comparison', 'description' => 'Compare quotes from multiple vendors side-by-side'],
            ['title' => 'Approval Workflows', 'description' => 'Multi-level approval chains based on amount'],
            ['title' => 'Contract Management', 'description' => 'Track vendor contracts and renewal dates']
        ],
        'use_cases' => [
            ['title' => 'Manufacturing', 'description' => 'Raw material procurement', 'icon' => 'truck'],
            ['title' => 'Retail', 'description' => 'Merchandise buying', 'icon' => 'shopping'],
            ['title' => 'Construction', 'description' => 'Material procurement', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'SAP', 'logo' => '/assets/integrations/sap.png', 'description' => 'SAP integration'],
            ['name' => 'IndiaMART', 'logo' => '/assets/integrations/indiamart.png', 'description' => 'Vendor discovery'],
            ['name' => 'Tally', 'logo' => '/assets/integrations/tally.png', 'description' => 'Accounting sync']
        ],
        'faqs' => [
            ['question' => 'Can vendors submit quotes online?', 'answer' => 'Yes, vendors get access to a self-service portal to submit quotes, invoices, and track payment status.'],
            ['question' => 'How does the approval workflow work?', 'answer' => 'Configure multi-level approvals based on amount, category, or department with email/mobile notifications.'],
            ['question' => 'Can I track delivery status?', 'answer' => 'Yes, track GRN (Goods Receipt Notes) and match with POs for three-way matching.']
        ],
        'pricing_note' => 'Enterprise pricing based on transaction volume. Contact us for a custom quote.',
        'color_theme' => '#06b6d4'
    ],
    [
        'name' => 'Project Management',
        'slug' => 'project-management',
        'tagline' => 'DELIVER ON TIME',
        'hero_badge' => 'Agile Ready',
        'description' => 'Plan, track, and deliver projects on time with Gantt charts, resource allocation, and team collaboration.',
        'subtitle' => 'Keep your projects on track with visual planning tools, real-time progress tracking, and seamless team collaboration features.',
        'stats' => [
            ['value' => '40%', 'label' => 'Faster Delivery'],
            ['value' => '100K+', 'label' => 'Projects Completed'],
            ['value' => '95%', 'label' => 'On-time Delivery'],
            ['value' => '50K+', 'label' => 'Teams']
        ],
        'highlight_cards' => [
            ['icon' => 'chart', 'title' => 'Gantt Charts', 'description' => 'Visual project timelines', 'value' => 'Interactive'],
            ['icon' => 'users', 'title' => 'Resource Planning', 'description' => 'Optimize team allocation', 'value' => 'Smart'],
            ['icon' => 'clock', 'title' => 'Time Tracking', 'description' => 'Track hours per task', 'value' => 'Automatic']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Plan Project', 'description' => 'Create tasks, milestones, and timelines', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Assign Resources', 'description' => 'Allocate team members to tasks', 'icon' => 'users'],
            ['step' => 3, 'title' => 'Track Progress', 'description' => 'Monitor and report on progress', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Visual Planning', 'description' => 'Gantt charts, Kanban boards, and calendar views'],
            ['title' => 'Resource Optimization', 'description' => 'See team workload and prevent over-allocation'],
            ['title' => 'Budget Tracking', 'description' => 'Track project costs against budgets in real-time']
        ],
        'use_cases' => [
            ['title' => 'IT Projects', 'description' => 'Software development sprints', 'icon' => 'building'],
            ['title' => 'Construction', 'description' => 'Construction project tracking', 'icon' => 'truck'],
            ['title' => 'Marketing', 'description' => 'Campaign management', 'icon' => 'users']
        ],
        'integrations' => [
            ['name' => 'Jira', 'logo' => '/assets/integrations/jira.png', 'description' => 'Jira sync'],
            ['name' => 'GitHub', 'logo' => '/assets/integrations/github.png', 'description' => 'Code commits'],
            ['name' => 'Slack', 'logo' => '/assets/integrations/slack.png', 'description' => 'Team updates']
        ],
        'faqs' => [
            ['question' => 'Does it support Agile methodology?', 'answer' => 'Yes, full support for Scrum and Kanban with sprints, backlogs, and burndown charts.'],
            ['question' => 'Can I track billable hours?', 'answer' => 'Yes, time tracking with billable/non-billable categorization and client invoicing.'],
            ['question' => 'Is there a mobile app?', 'answer' => 'Yes, iOS and Android apps for task updates, time tracking, and team communication on the go.']
        ],
        'pricing_note' => 'Free for up to 5 users. Pro plans start at ₹299/user/month.',
        'color_theme' => '#ec4899'
    ],
    [
        'name' => 'Manufacturing & Production',
        'slug' => 'manufacturing-production',
        'tagline' => 'INDUSTRY 4.0',
        'hero_badge' => 'Enterprise',
        'description' => 'End-to-end manufacturing management with BOM, production planning, and quality control.',
        'subtitle' => 'Digitize your factory floor with production planning, BOM management, work orders, and real-time shop floor tracking.',
        'stats' => [
            ['value' => '30%', 'label' => 'Efficiency Gain'],
            ['value' => '1M+', 'label' => 'Units Produced'],
            ['value' => '99.5%', 'label' => 'Quality Rate'],
            ['value' => '5K+', 'label' => 'Factories']
        ],
        'highlight_cards' => [
            ['icon' => 'chart', 'title' => 'Production Planning', 'description' => 'MRP and capacity planning', 'value' => 'MRP II'],
            ['icon' => 'check', 'title' => 'Quality Control', 'description' => 'Inspection and QC tracking', 'value' => 'ISO'],
            ['icon' => 'clock', 'title' => 'Shop Floor', 'description' => 'Real-time production tracking', 'value' => 'Live']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Create BOM', 'description' => 'Define bill of materials and routing', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Plan Production', 'description' => 'Schedule work orders and allocate resources', 'icon' => 'settings'],
            ['step' => 3, 'title' => 'Track & Control', 'description' => 'Monitor production and quality', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'BOM Management', 'description' => 'Multi-level BOMs with version control and costing'],
            ['title' => 'Capacity Planning', 'description' => 'Optimize machine and labor utilization'],
            ['title' => 'Traceability', 'description' => 'Full batch and serial number tracking']
        ],
        'use_cases' => [
            ['title' => 'Discrete Manufacturing', 'description' => 'Assembly and fabrication', 'icon' => 'truck'],
            ['title' => 'Process Manufacturing', 'description' => 'Chemical and food processing', 'icon' => 'building'],
            ['title' => 'Job Shops', 'description' => 'Custom manufacturing', 'icon' => 'briefcase']
        ],
        'integrations' => [
            ['name' => 'IoT Sensors', 'logo' => '/assets/integrations/iot.png', 'description' => 'Machine data'],
            ['name' => 'SAP', 'logo' => '/assets/integrations/sap.png', 'description' => 'ERP integration'],
            ['name' => 'AutoCAD', 'logo' => '/assets/integrations/autocad.png', 'description' => 'Design import']
        ],
        'faqs' => [
            ['question' => 'Does it support multi-level BOMs?', 'answer' => 'Yes, unlimited BOM levels with sub-assemblies, by-products, and co-products support.'],
            ['question' => 'Can I track machine downtime?', 'answer' => 'Yes, OEE tracking with downtime reasons, maintenance scheduling, and alerts.'],
            ['question' => 'Is batch tracking supported?', 'answer' => 'Yes, full batch and lot tracking with expiry management and recall capabilities.']
        ],
        'pricing_note' => 'Custom pricing based on factory size and modules. Request a demo for detailed quote.',
        'color_theme' => '#14b8a6'
    ],
    [
        'name' => 'Point of Sale',
        'slug' => 'point-of-sale',
        'tagline' => 'RETAIL SIMPLIFIED',
        'hero_badge' => 'Offline Ready',
        'description' => 'Modern POS system for retail stores with billing, inventory sync, and customer loyalty.',
        'subtitle' => 'Fast, reliable point of sale that works online and offline. Accept all payment modes and delight customers with quick checkouts.',
        'stats' => [
            ['value' => '₹500Cr+', 'label' => 'Daily Transactions'],
            ['value' => '< 3 sec', 'label' => 'Checkout Time'],
            ['value' => '50K+', 'label' => 'Retail Stores'],
            ['value' => '99.99%', 'label' => 'Uptime']
        ],
        'highlight_cards' => [
            ['icon' => 'speed', 'title' => 'Quick Billing', 'description' => 'Barcode scan and bill in seconds', 'value' => '< 3s'],
            ['icon' => 'globe', 'title' => 'Offline Mode', 'description' => 'Works without internet', 'value' => '100%'],
            ['icon' => 'money', 'title' => 'All Payments', 'description' => 'Cash, card, UPI, wallets', 'value' => '15+']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Scan Products', 'description' => 'Barcode scanning or search', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Apply Discounts', 'description' => 'Promotions and loyalty points', 'icon' => 'check'],
            ['step' => 3, 'title' => 'Collect Payment', 'description' => 'Multiple payment modes', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Offline Capability', 'description' => 'Continue billing even without internet connection'],
            ['title' => 'Multi-store Support', 'description' => 'Centralized management of multiple outlets'],
            ['title' => 'Customer Loyalty', 'description' => 'Points, rewards, and membership programs']
        ],
        'use_cases' => [
            ['title' => 'Retail Stores', 'description' => 'Fashion, electronics, general stores', 'icon' => 'shopping'],
            ['title' => 'Restaurants', 'description' => 'Table management and KOT', 'icon' => 'heart'],
            ['title' => 'Pharmacies', 'description' => 'Drug license and batch tracking', 'icon' => 'briefcase']
        ],
        'integrations' => [
            ['name' => 'Paytm', 'logo' => '/assets/integrations/paytm.png', 'description' => 'UPI payments'],
            ['name' => 'Pine Labs', 'logo' => '/assets/integrations/pinelabs.png', 'description' => 'Card terminals'],
            ['name' => 'Swiggy', 'logo' => '/assets/integrations/swiggy.png', 'description' => 'Online orders']
        ],
        'faqs' => [
            ['question' => 'Does it work offline?', 'answer' => 'Yes, full offline capability with automatic sync when internet is restored.'],
            ['question' => 'What hardware is supported?', 'answer' => 'Works on tablets, desktops, and supports thermal printers, barcode scanners, and cash drawers.'],
            ['question' => 'Can I manage multiple stores?', 'answer' => 'Yes, centralized dashboard to manage inventory, pricing, and reports across all locations.']
        ],
        'pricing_note' => '₹999/month per terminal. Hardware bundles available.',
        'color_theme' => '#ef4444'
    ],
    [
        'name' => 'E-commerce Integration',
        'slug' => 'ecommerce-integration',
        'tagline' => 'OMNICHANNEL SELLING',
        'hero_badge' => 'Multi-channel',
        'description' => 'Sync your ERP with online marketplaces and manage omnichannel operations seamlessly.',
        'subtitle' => 'Sell everywhere from one platform. Sync inventory, orders, and pricing across Amazon, Flipkart, Shopify, and your own website.',
        'stats' => [
            ['value' => '15+', 'label' => 'Marketplaces'],
            ['value' => '₹200Cr+', 'label' => 'GMV Processed'],
            ['value' => '99.9%', 'label' => 'Sync Accuracy'],
            ['value' => '10K+', 'label' => 'Sellers']
        ],
        'highlight_cards' => [
            ['icon' => 'globe', 'title' => 'Multi-channel', 'description' => 'Sell on 15+ platforms', 'value' => '15+'],
            ['icon' => 'speed', 'title' => 'Real-time Sync', 'description' => 'Inventory updates in seconds', 'value' => '< 30s'],
            ['icon' => 'chart', 'title' => 'Unified Analytics', 'description' => 'Cross-channel insights', 'value' => 'Dashboard']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Connect Channels', 'description' => 'Link your marketplace accounts', 'icon' => 'link'],
            ['step' => 2, 'title' => 'Sync Catalog', 'description' => 'Push products to all channels', 'icon' => 'upload'],
            ['step' => 3, 'title' => 'Manage Orders', 'description' => 'Centralized order processing', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Inventory Sync', 'description' => 'Real-time stock updates across all channels'],
            ['title' => 'Order Management', 'description' => 'Process orders from all channels in one place'],
            ['title' => 'Pricing Control', 'description' => 'Channel-specific pricing and promotions']
        ],
        'use_cases' => [
            ['title' => 'D2C Brands', 'description' => 'Direct to consumer selling', 'icon' => 'shopping'],
            ['title' => 'Distributors', 'description' => 'B2B and B2C channels', 'icon' => 'truck'],
            ['title' => 'Retailers', 'description' => 'Online + offline integration', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'Amazon', 'logo' => '/assets/integrations/amazon.png', 'description' => 'Amazon Seller Central'],
            ['name' => 'Flipkart', 'logo' => '/assets/integrations/flipkart.png', 'description' => 'Flipkart Seller Hub'],
            ['name' => 'Shopify', 'logo' => '/assets/integrations/shopify.png', 'description' => 'Shopify stores']
        ],
        'faqs' => [
            ['question' => 'Which marketplaces are supported?', 'answer' => 'Amazon, Flipkart, Myntra, Ajio, Shopify, WooCommerce, Magento, and 10+ more platforms.'],
            ['question' => 'How fast is inventory sync?', 'answer' => 'Near real-time sync within 30 seconds of any stock movement.'],
            ['question' => 'Can I manage returns?', 'answer' => 'Yes, centralized returns management with automatic inventory updates and refund processing.']
        ],
        'pricing_note' => 'Based on order volume. Starts at ₹2,999/month for up to 1,000 orders.',
        'color_theme' => '#667eea'
    ],
    [
        'name' => 'Warehouse Management',
        'slug' => 'warehouse-management',
        'tagline' => 'OPTIMIZE SPACE',
        'hero_badge' => 'WMS',
        'description' => 'Advanced warehouse management with bin locations, pick-pack-ship, and space optimization.',
        'subtitle' => 'Maximize warehouse efficiency with intelligent putaway, wave picking, and real-time visibility into every corner of your warehouse.',
        'stats' => [
            ['value' => '50%', 'label' => 'Faster Picking'],
            ['value' => '99.8%', 'label' => 'Order Accuracy'],
            ['value' => '30%', 'label' => 'Space Saved'],
            ['value' => '2K+', 'label' => 'Warehouses']
        ],
        'highlight_cards' => [
            ['icon' => 'chart', 'title' => 'Bin Management', 'description' => 'Location-based tracking', 'value' => '3D Map'],
            ['icon' => 'speed', 'title' => 'Wave Picking', 'description' => 'Optimized pick routes', 'value' => '50%↑'],
            ['icon' => 'check', 'title' => 'Cycle Counting', 'description' => 'Continuous inventory audits', 'value' => 'Auto']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Receive Goods', 'description' => 'GRN with quality inspection', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Putaway', 'description' => 'System-suggested bin locations', 'icon' => 'upload'],
            ['step' => 3, 'title' => 'Pick-Pack-Ship', 'description' => 'Optimized order fulfillment', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Space Optimization', 'description' => 'AI-powered slotting recommendations'],
            ['title' => 'Pick Efficiency', 'description' => 'Batch picking and zone picking strategies'],
            ['title' => 'Real-time Visibility', 'description' => 'Know exactly where every item is located']
        ],
        'use_cases' => [
            ['title' => '3PL Providers', 'description' => 'Multi-client warehouse management', 'icon' => 'building'],
            ['title' => 'E-commerce', 'description' => 'High-volume fulfillment centers', 'icon' => 'truck'],
            ['title' => 'Distribution', 'description' => 'Cross-docking operations', 'icon' => 'shopping']
        ],
        'integrations' => [
            ['name' => 'Delhivery', 'logo' => '/assets/integrations/delhivery.png', 'description' => 'Shipping integration'],
            ['name' => 'Zebra', 'logo' => '/assets/integrations/zebra.png', 'description' => 'Barcode hardware'],
            ['name' => 'SAP', 'logo' => '/assets/integrations/sap.png', 'description' => 'ERP integration']
        ],
        'faqs' => [
            ['question' => 'Does it support multiple warehouses?', 'answer' => 'Yes, manage unlimited warehouses with inter-warehouse transfers and consolidated reporting.'],
            ['question' => 'Can I use RF scanners?', 'answer' => 'Yes, full support for RF/handheld devices with our mobile WMS app.'],
            ['question' => 'How does putaway optimization work?', 'answer' => 'System suggests optimal bin locations based on velocity, size, and picking frequency.']
        ],
        'pricing_note' => 'Per-warehouse pricing. Contact sales for enterprise volume discounts.',
        'color_theme' => '#10b981'
    ],
    [
        'name' => 'Asset Management',
        'slug' => 'asset-management',
        'tagline' => 'TRACK EVERYTHING',
        'hero_badge' => 'RFID Ready',
        'description' => 'Track and manage fixed assets with depreciation, maintenance scheduling, and audits.',
        'subtitle' => 'Complete visibility into your assets - from procurement to disposal. Track location, maintenance, depreciation, and compliance effortlessly.',
        'stats' => [
            ['value' => '₹1000Cr+', 'label' => 'Assets Tracked'],
            ['value' => '40%', 'label' => 'Maintenance Cost↓'],
            ['value' => '100%', 'label' => 'Audit Ready'],
            ['value' => '8K+', 'label' => 'Companies']
        ],
        'highlight_cards' => [
            ['icon' => 'globe', 'title' => 'GPS Tracking', 'description' => 'Real-time asset location', 'value' => 'Live'],
            ['icon' => 'clock', 'title' => 'Maintenance', 'description' => 'Preventive maintenance alerts', 'value' => 'Auto'],
            ['icon' => 'chart', 'title' => 'Depreciation', 'description' => 'Multiple depreciation methods', 'value' => '5+']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Register Assets', 'description' => 'Add assets with photos and documents', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Tag & Track', 'description' => 'Barcode/RFID tagging', 'icon' => 'link'],
            ['step' => 3, 'title' => 'Maintain & Audit', 'description' => 'Schedule maintenance and audits', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Complete Visibility', 'description' => 'Know where every asset is and who is using it'],
            ['title' => 'Compliance Ready', 'description' => 'Maintain audit trails and generate compliance reports'],
            ['title' => 'Cost Optimization', 'description' => 'Reduce maintenance costs with preventive scheduling']
        ],
        'use_cases' => [
            ['title' => 'IT Assets', 'description' => 'Laptops, servers, software licenses', 'icon' => 'building'],
            ['title' => 'Fleet Management', 'description' => 'Vehicles and equipment', 'icon' => 'truck'],
            ['title' => 'Facilities', 'description' => 'Building and infrastructure', 'icon' => 'briefcase']
        ],
        'integrations' => [
            ['name' => 'ServiceNow', 'logo' => '/assets/integrations/servicenow.png', 'description' => 'ITSM integration'],
            ['name' => 'RFID Tags', 'logo' => '/assets/integrations/rfid.png', 'description' => 'RFID tracking'],
            ['name' => 'GPS Devices', 'logo' => '/assets/integrations/gps.png', 'description' => 'Location tracking']
        ],
        'faqs' => [
            ['question' => 'What depreciation methods are supported?', 'answer' => 'Straight-line, declining balance, sum-of-years, units of production, and custom methods.'],
            ['question' => 'Can I track asset movement?', 'answer' => 'Yes, complete movement history with check-in/check-out and transfer tracking.'],
            ['question' => 'Is RFID supported?', 'answer' => 'Yes, full RFID support for bulk scanning and real-time location tracking.']
        ],
        'pricing_note' => '₹5 per asset per month. Minimum 100 assets.',
        'color_theme' => '#f59e0b'
    ],
    [
        'name' => 'Quality Management',
        'slug' => 'quality-management',
        'tagline' => 'ZERO DEFECTS',
        'hero_badge' => 'ISO Certified',
        'description' => 'Comprehensive QMS with inspection checklists, NCR management, and compliance tracking.',
        'subtitle' => 'Build a culture of quality with systematic inspection processes, non-conformance tracking, and continuous improvement workflows.',
        'stats' => [
            ['value' => '60%', 'label' => 'Defect Reduction'],
            ['value' => '100%', 'label' => 'ISO Compliant'],
            ['value' => '5M+', 'label' => 'Inspections'],
            ['value' => '3K+', 'label' => 'Manufacturers']
        ],
        'highlight_cards' => [
            ['icon' => 'check', 'title' => 'Inspection Plans', 'description' => 'Configurable checklists', 'value' => 'Custom'],
            ['icon' => 'chart', 'title' => 'SPC Charts', 'description' => 'Statistical process control', 'value' => 'Real-time'],
            ['icon' => 'security', 'title' => 'CAPA', 'description' => 'Corrective action tracking', 'value' => '8D']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Define Standards', 'description' => 'Create inspection plans and criteria', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Inspect & Record', 'description' => 'Mobile inspection with photos', 'icon' => 'check'],
            ['step' => 3, 'title' => 'Analyze & Improve', 'description' => 'Root cause analysis and CAPA', 'icon' => 'settings']
        ],
        'benefits' => [
            ['title' => 'Standardized Processes', 'description' => 'Consistent quality checks across all operations'],
            ['title' => 'Traceability', 'description' => 'Complete audit trail for every inspection'],
            ['title' => 'Continuous Improvement', 'description' => 'Data-driven quality improvement initiatives']
        ],
        'use_cases' => [
            ['title' => 'Manufacturing', 'description' => 'Production quality control', 'icon' => 'truck'],
            ['title' => 'Pharmaceuticals', 'description' => 'GMP compliance', 'icon' => 'heart'],
            ['title' => 'Food Processing', 'description' => 'FSSAI compliance', 'icon' => 'shopping']
        ],
        'integrations' => [
            ['name' => 'SAP QM', 'logo' => '/assets/integrations/sap.png', 'description' => 'SAP Quality Module'],
            ['name' => 'Minitab', 'logo' => '/assets/integrations/minitab.png', 'description' => 'Statistical analysis'],
            ['name' => 'IoT Sensors', 'logo' => '/assets/integrations/iot.png', 'description' => 'Automated data capture']
        ],
        'faqs' => [
            ['question' => 'Does it support ISO 9001?', 'answer' => 'Yes, fully compliant with ISO 9001, ISO 14001, IATF 16949, and other quality standards.'],
            ['question' => 'Can inspectors use mobile devices?', 'answer' => 'Yes, mobile app for inspections with offline capability, photo capture, and digital signatures.'],
            ['question' => 'How does CAPA workflow work?', 'answer' => '8D methodology with root cause analysis, corrective actions, and effectiveness verification.']
        ],
        'pricing_note' => 'Enterprise pricing based on users and inspection volume.',
        'color_theme' => '#8b5cf6'
    ],
    [
        'name' => 'Supply Chain Management',
        'slug' => 'supply-chain-management',
        'tagline' => 'END-TO-END VISIBILITY',
        'hero_badge' => 'AI Powered',
        'description' => 'Optimize your supply chain with demand planning, logistics tracking, and supplier collaboration.',
        'subtitle' => 'Gain complete visibility across your supply chain. From demand forecasting to last-mile delivery, optimize every link in the chain.',
        'stats' => [
            ['value' => '25%', 'label' => 'Cost Reduction'],
            ['value' => '95%', 'label' => 'Forecast Accuracy'],
            ['value' => '₹500Cr+', 'label' => 'Shipments Tracked'],
            ['value' => '1K+', 'label' => 'Enterprises']
        ],
        'highlight_cards' => [
            ['icon' => 'chart', 'title' => 'Demand Planning', 'description' => 'AI-powered forecasting', 'value' => 'ML'],
            ['icon' => 'globe', 'title' => 'Shipment Tracking', 'description' => 'Real-time logistics visibility', 'value' => 'GPS'],
            ['icon' => 'users', 'title' => 'Supplier Portal', 'description' => 'Collaborative planning', 'value' => 'B2B']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Forecast Demand', 'description' => 'AI-based demand prediction', 'icon' => 'chart'],
            ['step' => 2, 'title' => 'Plan Supply', 'description' => 'Optimize procurement and production', 'icon' => 'settings'],
            ['step' => 3, 'title' => 'Execute & Track', 'description' => 'Monitor shipments in real-time', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Demand Sensing', 'description' => 'Predict demand changes before they happen'],
            ['title' => 'Inventory Optimization', 'description' => 'Right stock at the right place at the right time'],
            ['title' => 'Risk Management', 'description' => 'Identify and mitigate supply chain risks']
        ],
        'use_cases' => [
            ['title' => 'FMCG', 'description' => 'Fast-moving consumer goods', 'icon' => 'shopping'],
            ['title' => 'Automotive', 'description' => 'Just-in-time supply chains', 'icon' => 'truck'],
            ['title' => 'Retail', 'description' => 'Omnichannel fulfillment', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'Blue Dart', 'logo' => '/assets/integrations/bluedart.png', 'description' => 'Logistics tracking'],
            ['name' => 'Oracle SCM', 'logo' => '/assets/integrations/oracle.png', 'description' => 'ERP integration'],
            ['name' => 'Weather API', 'logo' => '/assets/integrations/weather.png', 'description' => 'Demand factors']
        ],
        'faqs' => [
            ['question' => 'How accurate is demand forecasting?', 'answer' => 'Our AI models achieve 90-95% accuracy by analyzing historical data, seasonality, and external factors.'],
            ['question' => 'Can I track shipments from multiple carriers?', 'answer' => 'Yes, unified tracking across 50+ logistics providers with a single dashboard.'],
            ['question' => 'Does it support S&OP?', 'answer' => 'Yes, full Sales & Operations Planning with collaborative workflows and scenario planning.']
        ],
        'pricing_note' => 'Enterprise solution with custom pricing. Schedule a consultation.',
        'color_theme' => '#06b6d4'
    ],
    [
        'name' => 'Business Intelligence',
        'slug' => 'business-intelligence',
        'tagline' => 'DATA-DRIVEN DECISIONS',
        'hero_badge' => 'Analytics',
        'description' => 'Transform data into insights with dashboards, reports, and predictive analytics.',
        'subtitle' => 'Make smarter decisions with real-time dashboards, custom reports, and AI-powered insights across all your business data.',
        'stats' => [
            ['value' => '100+', 'label' => 'Pre-built Reports'],
            ['value' => '10x', 'label' => 'Faster Insights'],
            ['value' => '50+', 'label' => 'Data Sources'],
            ['value' => '20K+', 'label' => 'Users']
        ],
        'highlight_cards' => [
            ['icon' => 'chart', 'title' => 'Live Dashboards', 'description' => 'Real-time KPI monitoring', 'value' => 'Real-time'],
            ['icon' => 'speed', 'title' => 'Self-service BI', 'description' => 'Drag-and-drop report builder', 'value' => 'No-code'],
            ['icon' => 'globe', 'title' => 'Mobile Access', 'description' => 'Insights on the go', 'value' => 'App']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Connect Data', 'description' => 'Link all your data sources', 'icon' => 'link'],
            ['step' => 2, 'title' => 'Build Reports', 'description' => 'Drag-and-drop report creation', 'icon' => 'document'],
            ['step' => 3, 'title' => 'Share Insights', 'description' => 'Scheduled reports and alerts', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Unified Data View', 'description' => 'Combine data from multiple sources in one place'],
            ['title' => 'Self-service Analytics', 'description' => 'Business users can create reports without IT help'],
            ['title' => 'Predictive Insights', 'description' => 'AI-powered forecasting and anomaly detection']
        ],
        'use_cases' => [
            ['title' => 'Executive Dashboards', 'description' => 'C-suite KPI monitoring', 'icon' => 'briefcase'],
            ['title' => 'Sales Analytics', 'description' => 'Pipeline and revenue analysis', 'icon' => 'chart'],
            ['title' => 'Operations', 'description' => 'Operational efficiency metrics', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'Power BI', 'logo' => '/assets/integrations/powerbi.png', 'description' => 'Microsoft Power BI'],
            ['name' => 'Tableau', 'logo' => '/assets/integrations/tableau.png', 'description' => 'Tableau export'],
            ['name' => 'Google Sheets', 'logo' => '/assets/integrations/gsheets.png', 'description' => 'Spreadsheet sync']
        ],
        'faqs' => [
            ['question' => 'Can I create custom reports?', 'answer' => 'Yes, drag-and-drop report builder with 50+ chart types and custom calculations.'],
            ['question' => 'How often is data refreshed?', 'answer' => 'Real-time for live dashboards, or scheduled refresh intervals from 5 minutes to daily.'],
            ['question' => 'Can I embed reports in other apps?', 'answer' => 'Yes, embed dashboards in your website or apps with our embed API.']
        ],
        'pricing_note' => 'Included with ERP subscription. Standalone BI starts at ₹999/user/month.',
        'color_theme' => '#ec4899'
    ],
    [
        'name' => 'Document Management',
        'slug' => 'document-management',
        'tagline' => 'GO PAPERLESS',
        'hero_badge' => 'DMS',
        'description' => 'Centralized document storage with version control, workflows, and secure sharing.',
        'subtitle' => 'Eliminate paper chaos with digital document management. Store, organize, and collaborate on documents with enterprise-grade security.',
        'stats' => [
            ['value' => '10M+', 'label' => 'Documents Stored'],
            ['value' => '70%', 'label' => 'Time Saved'],
            ['value' => '256-bit', 'label' => 'Encryption'],
            ['value' => '15K+', 'label' => 'Organizations']
        ],
        'highlight_cards' => [
            ['icon' => 'security', 'title' => 'Secure Storage', 'description' => 'Bank-grade encryption', 'value' => 'AES-256'],
            ['icon' => 'clock', 'title' => 'Version Control', 'description' => 'Track all document changes', 'value' => 'Unlimited'],
            ['icon' => 'users', 'title' => 'Collaboration', 'description' => 'Real-time co-editing', 'value' => 'Live']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Upload Documents', 'description' => 'Drag-and-drop or scan to upload', 'icon' => 'upload'],
            ['step' => 2, 'title' => 'Organize & Tag', 'description' => 'Auto-categorization with AI', 'icon' => 'document'],
            ['step' => 3, 'title' => 'Share & Collaborate', 'description' => 'Secure sharing with permissions', 'icon' => 'users']
        ],
        'benefits' => [
            ['title' => 'Instant Search', 'description' => 'Full-text search across all documents including scanned PDFs'],
            ['title' => 'Approval Workflows', 'description' => 'Route documents for review and approval'],
            ['title' => 'Compliance Ready', 'description' => 'Audit trails and retention policies for compliance']
        ],
        'use_cases' => [
            ['title' => 'Legal', 'description' => 'Contract and legal document management', 'icon' => 'briefcase'],
            ['title' => 'HR', 'description' => 'Employee documents and policies', 'icon' => 'users'],
            ['title' => 'Finance', 'description' => 'Invoices and financial records', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'Google Drive', 'logo' => '/assets/integrations/gdrive.png', 'description' => 'Cloud sync'],
            ['name' => 'DocuSign', 'logo' => '/assets/integrations/docusign.png', 'description' => 'E-signatures'],
            ['name' => 'Scanner', 'logo' => '/assets/integrations/scanner.png', 'description' => 'Document scanning']
        ],
        'faqs' => [
            ['question' => 'Can I search inside documents?', 'answer' => 'Yes, full-text search with OCR for scanned documents. Find any document in seconds.'],
            ['question' => 'How secure is the storage?', 'answer' => 'AES-256 encryption at rest and in transit. SOC 2 Type II certified data centers.'],
            ['question' => 'Can I set document expiry?', 'answer' => 'Yes, retention policies with automatic archival and deletion based on your rules.']
        ],
        'pricing_note' => '₹199/user/month with 100GB storage. Additional storage at ₹5/GB.',
        'color_theme' => '#14b8a6'
    ],
    [
        'name' => 'Customer Support',
        'slug' => 'customer-support',
        'tagline' => 'DELIGHT CUSTOMERS',
        'hero_badge' => 'Helpdesk',
        'description' => 'Omnichannel helpdesk with ticketing, knowledge base, and customer satisfaction tracking.',
        'subtitle' => 'Deliver exceptional customer support across all channels. Resolve issues faster with AI-powered routing and a comprehensive knowledge base.',
        'stats' => [
            ['value' => '50%', 'label' => 'Faster Resolution'],
            ['value' => '4.8/5', 'label' => 'CSAT Score'],
            ['value' => '10M+', 'label' => 'Tickets Resolved'],
            ['value' => '25K+', 'label' => 'Support Teams']
        ],
        'highlight_cards' => [
            ['icon' => 'globe', 'title' => 'Omnichannel', 'description' => 'Email, chat, phone, social', 'value' => '10+'],
            ['icon' => 'speed', 'title' => 'AI Routing', 'description' => 'Smart ticket assignment', 'value' => 'Auto'],
            ['icon' => 'chart', 'title' => 'Analytics', 'description' => 'CSAT and SLA tracking', 'value' => 'Real-time']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Receive Tickets', 'description' => 'Unified inbox for all channels', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Route & Assign', 'description' => 'AI-powered ticket routing', 'icon' => 'users'],
            ['step' => 3, 'title' => 'Resolve & Measure', 'description' => 'Track resolution and satisfaction', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Unified Inbox', 'description' => 'All customer conversations in one place'],
            ['title' => 'Self-service Portal', 'description' => 'Knowledge base and FAQ for customer self-help'],
            ['title' => 'SLA Management', 'description' => 'Automatic escalations and SLA breach alerts']
        ],
        'use_cases' => [
            ['title' => 'SaaS Companies', 'description' => 'Product support and onboarding', 'icon' => 'building'],
            ['title' => 'E-commerce', 'description' => 'Order and return support', 'icon' => 'shopping'],
            ['title' => 'Services', 'description' => 'Service request management', 'icon' => 'users']
        ],
        'integrations' => [
            ['name' => 'Freshdesk', 'logo' => '/assets/integrations/freshdesk.png', 'description' => 'Helpdesk sync'],
            ['name' => 'WhatsApp', 'logo' => '/assets/integrations/whatsapp.png', 'description' => 'WhatsApp support'],
            ['name' => 'Intercom', 'logo' => '/assets/integrations/intercom.png', 'description' => 'Live chat']
        ],
        'faqs' => [
            ['question' => 'Which channels are supported?', 'answer' => 'Email, live chat, phone, WhatsApp, Facebook, Twitter, Instagram, and web forms.'],
            ['question' => 'Can I create a knowledge base?', 'answer' => 'Yes, create articles, FAQs, and video tutorials. Customers can search and find answers.'],
            ['question' => 'How does AI routing work?', 'answer' => 'AI analyzes ticket content and routes to the best agent based on skills, workload, and history.']
        ],
        'pricing_note' => 'Free for up to 3 agents. Pro plans start at ₹799/agent/month.',
        'color_theme' => '#ef4444'
    ],
    [
        'name' => 'Expense Management',
        'slug' => 'expense-management',
        'tagline' => 'CONTROL SPENDING',
        'hero_badge' => 'Smart',
        'description' => 'Automate expense reporting with receipt scanning, policy compliance, and reimbursements.',
        'subtitle' => 'Simplify expense management for employees and finance teams. Snap receipts, auto-fill reports, and get reimbursed faster.',
        'stats' => [
            ['value' => '80%', 'label' => 'Time Saved'],
            ['value' => '₹50Cr+', 'label' => 'Expenses Processed'],
            ['value' => '< 48 hrs', 'label' => 'Reimbursement'],
            ['value' => '30K+', 'label' => 'Employees']
        ],
        'highlight_cards' => [
            ['icon' => 'speed', 'title' => 'Receipt OCR', 'description' => 'Auto-extract receipt data', 'value' => 'AI'],
            ['icon' => 'check', 'title' => 'Policy Engine', 'description' => 'Auto-check policy compliance', 'value' => '100%'],
            ['icon' => 'money', 'title' => 'Fast Reimbursement', 'description' => 'Direct bank transfer', 'value' => '< 48h']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Capture Receipt', 'description' => 'Snap photo or forward email', 'icon' => 'upload'],
            ['step' => 2, 'title' => 'Submit Report', 'description' => 'Auto-filled expense report', 'icon' => 'document'],
            ['step' => 3, 'title' => 'Get Reimbursed', 'description' => 'Approval and direct deposit', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Mobile-first', 'description' => 'Submit expenses from anywhere with the mobile app'],
            ['title' => 'Policy Compliance', 'description' => 'Automatic policy checks prevent violations'],
            ['title' => 'Corporate Cards', 'description' => 'Auto-import corporate card transactions']
        ],
        'use_cases' => [
            ['title' => 'Sales Teams', 'description' => 'Travel and client entertainment', 'icon' => 'users'],
            ['title' => 'Field Staff', 'description' => 'Daily allowances and mileage', 'icon' => 'truck'],
            ['title' => 'Executives', 'description' => 'Business travel expenses', 'icon' => 'briefcase']
        ],
        'integrations' => [
            ['name' => 'Corporate Cards', 'logo' => '/assets/integrations/cards.png', 'description' => 'Card feed import'],
            ['name' => 'Uber', 'logo' => '/assets/integrations/uber.png', 'description' => 'Ride receipts'],
            ['name' => 'MakeMyTrip', 'logo' => '/assets/integrations/mmt.png', 'description' => 'Travel bookings']
        ],
        'faqs' => [
            ['question' => 'How does receipt scanning work?', 'answer' => 'AI-powered OCR extracts vendor, amount, date, and category from receipt photos automatically.'],
            ['question' => 'Can I set spending limits?', 'answer' => 'Yes, configure limits by category, employee level, or project with automatic alerts.'],
            ['question' => 'How fast are reimbursements?', 'answer' => 'Approved expenses are reimbursed within 48 hours via direct bank transfer.']
        ],
        'pricing_note' => '₹99/employee/month. Volume discounts for 500+ employees.',
        'color_theme' => '#667eea'
    ],
    [
        'name' => 'Compliance & Audit',
        'slug' => 'compliance-audit',
        'tagline' => 'STAY COMPLIANT',
        'hero_badge' => 'GRC',
        'description' => 'Manage regulatory compliance, internal audits, and risk assessment in one platform.',
        'subtitle' => 'Stay ahead of compliance requirements with automated monitoring, audit management, and risk assessment tools.',
        'stats' => [
            ['value' => '100%', 'label' => 'Audit Ready'],
            ['value' => '50+', 'label' => 'Compliance Frameworks'],
            ['value' => '60%', 'label' => 'Audit Time Saved'],
            ['value' => '5K+', 'label' => 'Organizations']
        ],
        'highlight_cards' => [
            ['icon' => 'security', 'title' => 'Risk Assessment', 'description' => 'Identify and mitigate risks', 'value' => 'Matrix'],
            ['icon' => 'check', 'title' => 'Audit Management', 'description' => 'Plan and execute audits', 'value' => 'Workflow'],
            ['icon' => 'chart', 'title' => 'Compliance Dashboard', 'description' => 'Real-time compliance status', 'value' => 'Live']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Define Controls', 'description' => 'Map controls to regulations', 'icon' => 'document'],
            ['step' => 2, 'title' => 'Monitor Compliance', 'description' => 'Automated compliance checks', 'icon' => 'check'],
            ['step' => 3, 'title' => 'Audit & Report', 'description' => 'Generate audit reports', 'icon' => 'send']
        ],
        'benefits' => [
            ['title' => 'Framework Library', 'description' => 'Pre-built templates for ISO, SOC 2, GDPR, and more'],
            ['title' => 'Evidence Collection', 'description' => 'Automated evidence gathering for audits'],
            ['title' => 'Risk Scoring', 'description' => 'Quantify and prioritize risks with scoring models']
        ],
        'use_cases' => [
            ['title' => 'IT Companies', 'description' => 'SOC 2 and ISO 27001 compliance', 'icon' => 'building'],
            ['title' => 'Financial Services', 'description' => 'RBI and SEBI compliance', 'icon' => 'briefcase'],
            ['title' => 'Healthcare', 'description' => 'HIPAA and data privacy', 'icon' => 'heart']
        ],
        'integrations' => [
            ['name' => 'AWS', 'logo' => '/assets/integrations/aws.png', 'description' => 'Cloud compliance'],
            ['name' => 'Jira', 'logo' => '/assets/integrations/jira.png', 'description' => 'Issue tracking'],
            ['name' => 'Slack', 'logo' => '/assets/integrations/slack.png', 'description' => 'Notifications']
        ],
        'faqs' => [
            ['question' => 'Which compliance frameworks are supported?', 'answer' => 'ISO 27001, SOC 2, GDPR, HIPAA, PCI-DSS, RBI guidelines, and 50+ more frameworks.'],
            ['question' => 'Can I schedule recurring audits?', 'answer' => 'Yes, set up audit calendars with automatic task assignment and reminders.'],
            ['question' => 'How does evidence collection work?', 'answer' => 'Automated evidence gathering from integrated systems with manual upload options.']
        ],
        'pricing_note' => 'Enterprise pricing based on organization size and frameworks needed.',
        'color_theme' => '#10b981'
    ],
    [
        'name' => 'Fleet Management',
        'slug' => 'fleet-management',
        'tagline' => 'OPTIMIZE FLEET',
        'hero_badge' => 'GPS Enabled',
        'description' => 'Track vehicles, optimize routes, manage drivers, and reduce fleet operating costs.',
        'subtitle' => 'Complete fleet visibility with GPS tracking, route optimization, driver management, and fuel monitoring to reduce costs by up to 30%.',
        'stats' => [
            ['value' => '30%', 'label' => 'Fuel Savings'],
            ['value' => '100K+', 'label' => 'Vehicles Tracked'],
            ['value' => '25%', 'label' => 'Route Optimization'],
            ['value' => '10K+', 'label' => 'Fleets']
        ],
        'highlight_cards' => [
            ['icon' => 'globe', 'title' => 'Live Tracking', 'description' => 'Real-time vehicle location', 'value' => 'GPS'],
            ['icon' => 'chart', 'title' => 'Route Optimization', 'description' => 'AI-powered route planning', 'value' => '25%↑'],
            ['icon' => 'clock', 'title' => 'Driver Behavior', 'description' => 'Monitor driving patterns', 'value' => 'Score']
        ],
        'workflow_steps' => [
            ['step' => 1, 'title' => 'Install Devices', 'description' => 'GPS trackers in vehicles', 'icon' => 'link'],
            ['step' => 2, 'title' => 'Plan Routes', 'description' => 'Optimize delivery routes', 'icon' => 'settings'],
            ['step' => 3, 'title' => 'Monitor & Analyze', 'description' => 'Track and improve performance', 'icon' => 'check']
        ],
        'benefits' => [
            ['title' => 'Fuel Management', 'description' => 'Track fuel consumption and detect theft'],
            ['title' => 'Maintenance Alerts', 'description' => 'Preventive maintenance based on mileage and time'],
            ['title' => 'Driver Safety', 'description' => 'Monitor speeding, harsh braking, and idle time']
        ],
        'use_cases' => [
            ['title' => 'Logistics', 'description' => 'Delivery fleet management', 'icon' => 'truck'],
            ['title' => 'Field Services', 'description' => 'Service vehicle tracking', 'icon' => 'users'],
            ['title' => 'Transportation', 'description' => 'Passenger transport', 'icon' => 'building']
        ],
        'integrations' => [
            ['name' => 'Google Maps', 'logo' => '/assets/integrations/gmaps.png', 'description' => 'Route planning'],
            ['name' => 'Fuel Cards', 'logo' => '/assets/integrations/fuel.png', 'description' => 'Fuel tracking'],
            ['name' => 'OBD Devices', 'logo' => '/assets/integrations/obd.png', 'description' => 'Vehicle diagnostics']
        ],
        'faqs' => [
            ['question' => 'What GPS devices are supported?', 'answer' => 'We support 50+ GPS device brands and also offer our own hardware with easy installation.'],
            ['question' => 'Can I set geofence alerts?', 'answer' => 'Yes, create unlimited geofences with entry/exit alerts and time-based rules.'],
            ['question' => 'How does route optimization work?', 'answer' => 'AI considers traffic, delivery windows, vehicle capacity, and driver hours to optimize routes.']
        ],
        'pricing_note' => '₹299/vehicle/month. Hardware available for purchase or rental.',
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
