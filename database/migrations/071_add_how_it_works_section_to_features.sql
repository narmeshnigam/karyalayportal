-- Migration 071: Add How It Works Section fields to feature_styling table
-- This follows the same pattern as hero and key benefits sections
-- Allows dynamic management of the "How It Works" section from admin panel

-- ============================================================================
-- Add How It Works Section columns to feature_styling table
-- ============================================================================

-- How It Works Section Enable/Disable
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_enabled BOOLEAN DEFAULT TRUE AFTER benefits_cards;

-- How It Works Section Badge/Heading
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_badge VARCHAR(50) DEFAULT 'Simple Process' AFTER how_it_works_enabled;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_heading VARCHAR(100) DEFAULT 'How It Works' AFTER how_it_works_badge;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_subheading VARCHAR(200) DEFAULT 'Get started in four simple steps and transform your business operations' AFTER how_it_works_heading;

-- How It Works Section Colors
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_bg_color VARCHAR(20) DEFAULT '#f9fafb' AFTER how_it_works_subheading;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_badge_bg_color VARCHAR(20) DEFAULT '#667eea1a' AFTER how_it_works_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_badge_text_color VARCHAR(20) DEFAULT '#667eea' AFTER how_it_works_badge_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_heading_color VARCHAR(20) DEFAULT '#111827' AFTER how_it_works_badge_text_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_subheading_color VARCHAR(20) DEFAULT '#6b7280' AFTER how_it_works_heading_color;

-- How It Works Step Card Styling
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_card_bg_color VARCHAR(20) DEFAULT '#ffffff' AFTER how_it_works_subheading_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_card_border_color VARCHAR(20) DEFAULT '#e5e7eb' AFTER how_it_works_card_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_card_hover_border_color VARCHAR(20) DEFAULT '#667eea' AFTER how_it_works_card_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_step_color VARCHAR(20) DEFAULT '#667eea' AFTER how_it_works_card_hover_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_step_bg_color VARCHAR(20) DEFAULT '#667eea1a' AFTER how_it_works_step_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_title_color VARCHAR(20) DEFAULT '#111827' AFTER how_it_works_step_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_desc_color VARCHAR(20) DEFAULT '#6b7280' AFTER how_it_works_title_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_connector_color VARCHAR(20) DEFAULT '#d1d5db' AFTER how_it_works_desc_color;

-- How It Works Steps JSON (stores array of step objects with step number, title, description)
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS how_it_works_steps JSON DEFAULT NULL AFTER how_it_works_connector_color;
