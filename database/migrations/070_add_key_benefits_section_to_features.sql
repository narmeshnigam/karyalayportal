-- Migration 070: Add Key Benefits Section fields to feature_styling table
-- This follows the same pattern as solution_styling for solutions
-- Allows dynamic management of the key benefits section from admin panel

-- ============================================================================
-- Add Key Benefits Section columns to feature_styling table
-- ============================================================================

-- Key Benefits Section Enable/Disable
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_enabled BOOLEAN DEFAULT TRUE AFTER hero_breadcrumb_separator_color;

-- Key Benefits Section Headings
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_heading1 VARCHAR(50) DEFAULT 'Why Choose' AFTER benefits_section_enabled;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_heading2 VARCHAR(50) DEFAULT '' AFTER benefits_section_heading1;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_subheading VARCHAR(200) DEFAULT 'Discover the key advantages that make this feature essential for your business operations.' AFTER benefits_section_heading2;

-- Key Benefits Section Colors
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_bg_color VARCHAR(20) DEFAULT '#ffffff' AFTER benefits_section_subheading;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_heading_color VARCHAR(20) DEFAULT '#111827' AFTER benefits_section_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_section_subheading_color VARCHAR(20) DEFAULT '#6b7280' AFTER benefits_section_heading_color;

-- Key Benefits Card Styling
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_bg_color VARCHAR(20) DEFAULT '#f8fafc' AFTER benefits_section_subheading_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_border_color VARCHAR(20) DEFAULT '#e2e8f0' AFTER benefits_card_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_hover_bg_color VARCHAR(20) DEFAULT '#667eea' AFTER benefits_card_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_title_color VARCHAR(20) DEFAULT '#111827' AFTER benefits_card_hover_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_text_color VARCHAR(20) DEFAULT '#6b7280' AFTER benefits_card_title_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_icon_color VARCHAR(20) DEFAULT '#667eea' AFTER benefits_card_text_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_card_hover_text_color VARCHAR(20) DEFAULT '#FFFFFF' AFTER benefits_card_icon_color;

-- Key Benefits Cards JSON (stores array of card objects with icon, title, description)
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS benefits_cards JSON DEFAULT NULL AFTER benefits_card_hover_text_color;
