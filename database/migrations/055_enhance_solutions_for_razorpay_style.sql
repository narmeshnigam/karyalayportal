-- Migration 055: Enhance solutions table for Razorpay-style detail page
-- Adds new columns for a modern SaaS product page design

-- Add new columns for enhanced solution detail page
ALTER TABLE solutions 
    ADD COLUMN IF NOT EXISTS subtitle VARCHAR(500) AFTER tagline,
    ADD COLUMN IF NOT EXISTS hero_badge VARCHAR(100) AFTER subtitle,
    ADD COLUMN IF NOT EXISTS hero_cta_primary_text VARCHAR(100) DEFAULT 'Get Started' AFTER hero_badge,
    ADD COLUMN IF NOT EXISTS hero_cta_primary_link VARCHAR(500) AFTER hero_cta_primary_text,
    ADD COLUMN IF NOT EXISTS hero_cta_secondary_text VARCHAR(100) DEFAULT 'Watch Demo' AFTER hero_cta_primary_link,
    ADD COLUMN IF NOT EXISTS hero_cta_secondary_link VARCHAR(500) AFTER hero_cta_secondary_text,
    ADD COLUMN IF NOT EXISTS demo_video_url VARCHAR(500) AFTER hero_cta_secondary_link,
    ADD COLUMN IF NOT EXISTS highlight_cards JSON AFTER stats,
    ADD COLUMN IF NOT EXISTS integrations JSON AFTER highlight_cards,
    ADD COLUMN IF NOT EXISTS workflow_steps JSON AFTER integrations,
    ADD COLUMN IF NOT EXISTS testimonial_id CHAR(36) AFTER workflow_steps,
    ADD COLUMN IF NOT EXISTS pricing_note TEXT AFTER testimonial_id,
    ADD COLUMN IF NOT EXISTS meta_title VARCHAR(255) AFTER pricing_note,
    ADD COLUMN IF NOT EXISTS meta_description TEXT AFTER meta_title,
    ADD COLUMN IF NOT EXISTS meta_keywords VARCHAR(500) AFTER meta_description;

-- Add foreign key for testimonial if testimonials table exists
-- ALTER TABLE solutions ADD CONSTRAINT fk_solutions_testimonial 
--     FOREIGN KEY (testimonial_id) REFERENCES testimonials(id) ON DELETE SET NULL;

-- JSON field structures:
-- 
-- highlight_cards: [
--   {"icon": "speed", "title": "Instant Transfers", "description": "Send money in seconds", "value": "< 2 sec"},
--   {"icon": "security", "title": "Bank-grade Security", "description": "256-bit encryption", "value": "100%"}
-- ]
--
-- integrations: [
--   {"name": "Tally", "logo": "/assets/integrations/tally.png", "description": "Sync with Tally ERP"},
--   {"name": "QuickBooks", "logo": "/assets/integrations/quickbooks.png", "description": "Connect QuickBooks"}
-- ]
--
-- workflow_steps: [
--   {"step": 1, "title": "Connect Your Account", "description": "Link your bank account securely", "icon": "link"},
--   {"step": 2, "title": "Add Beneficiaries", "description": "Add vendors and employees", "icon": "users"},
--   {"step": 3, "title": "Make Payouts", "description": "Send money instantly", "icon": "send"}
-- ]
--
-- stats: [
--   {"value": "10M+", "label": "Transactions Processed"},
--   {"value": "99.9%", "label": "Uptime"},
--   {"value": "50K+", "label": "Businesses Trust Us"}
-- ]
--
-- use_cases: [
--   {"title": "Vendor Payments", "description": "Pay suppliers on time", "icon": "building"},
--   {"title": "Salary Disbursement", "description": "Automate payroll", "icon": "users"}
-- ]

