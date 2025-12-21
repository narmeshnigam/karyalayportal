-- Migration 073: Add Use Cases Section fields to feature_styling table
-- This follows the same pattern as other sections
-- Allows dynamic management of the "Use Cases" section from admin panel

-- ============================================================================
-- Add Use Cases Section columns to feature_styling table
-- ============================================================================

-- Use Cases Section Enable/Disable
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_enabled BOOLEAN DEFAULT TRUE AFTER highlights_cards;

-- Use Cases Section Badge/Heading
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_badge VARCHAR(50) DEFAULT 'Industries' AFTER use_cases_enabled;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_heading VARCHAR(100) DEFAULT 'Use Cases' AFTER use_cases_badge;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_subheading VARCHAR(200) DEFAULT 'See how different industries leverage this feature to drive success' AFTER use_cases_heading;

-- Use Cases Section Colors
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_bg_color VARCHAR(20) DEFAULT '#1e293b' AFTER use_cases_subheading;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_badge_bg_color VARCHAR(20) DEFAULT '#ffffff1a' AFTER use_cases_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_badge_text_color VARCHAR(20) DEFAULT '#ffffff' AFTER use_cases_badge_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_heading_color VARCHAR(20) DEFAULT '#ffffff' AFTER use_cases_badge_text_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_subheading_color VARCHAR(20) DEFAULT '#ffffffb3' AFTER use_cases_heading_color;

-- Use Cases Card Styling
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_card_bg_color VARCHAR(20) DEFAULT '#ffffff0d' AFTER use_cases_subheading_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_card_border_color VARCHAR(20) DEFAULT '#ffffff1a' AFTER use_cases_card_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_card_hover_border_color VARCHAR(20) DEFAULT '#667eea' AFTER use_cases_card_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_title_color VARCHAR(20) DEFAULT '#ffffff' AFTER use_cases_card_hover_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_desc_color VARCHAR(20) DEFAULT '#ffffffb3' AFTER use_cases_title_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_overlay_color VARCHAR(20) DEFAULT '#000000cc' AFTER use_cases_desc_color;

-- Use Cases Cards JSON (stores array of card objects with industry, description, image)
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS use_cases_cards JSON DEFAULT NULL AFTER use_cases_overlay_color;
