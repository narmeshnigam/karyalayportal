-- Migration 072: Add Feature Highlights Section fields to feature_styling table
-- This follows the same pattern as hero, key benefits, and how it works sections
-- Allows dynamic management of the "Feature Highlights" section from admin panel

-- ============================================================================
-- Add Feature Highlights Section columns to feature_styling table
-- ============================================================================

-- Feature Highlights Section Enable/Disable
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_enabled BOOLEAN DEFAULT TRUE AFTER how_it_works_steps;

-- Feature Highlights Section Badge/Heading
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_badge VARCHAR(50) DEFAULT 'Capabilities' AFTER highlights_enabled;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_heading VARCHAR(100) DEFAULT 'Feature Highlights' AFTER highlights_badge;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_subheading VARCHAR(200) DEFAULT 'Powerful capabilities designed to streamline your business processes' AFTER highlights_heading;

-- Feature Highlights Section Colors
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_bg_color VARCHAR(20) DEFAULT '#f8fafc' AFTER highlights_subheading;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_badge_bg_color VARCHAR(20) DEFAULT '#667eea1a' AFTER highlights_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_badge_text_color VARCHAR(20) DEFAULT '#667eea' AFTER highlights_badge_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_heading_color VARCHAR(20) DEFAULT '#111827' AFTER highlights_badge_text_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_subheading_color VARCHAR(20) DEFAULT '#6b7280' AFTER highlights_heading_color;

-- Feature Highlights Card Styling
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_card_bg_color VARCHAR(20) DEFAULT '#ffffff' AFTER highlights_subheading_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_card_border_color VARCHAR(20) DEFAULT '#e5e7eb' AFTER highlights_card_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_card_hover_border_color VARCHAR(20) DEFAULT '#667eea' AFTER highlights_card_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_icon_bg_color VARCHAR(20) DEFAULT '#667eea1a' AFTER highlights_card_hover_border_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_icon_color VARCHAR(20) DEFAULT '#667eea' AFTER highlights_icon_bg_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_title_color VARCHAR(20) DEFAULT '#111827' AFTER highlights_icon_color;
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_desc_color VARCHAR(20) DEFAULT '#6b7280' AFTER highlights_title_color;

-- Feature Highlights Cards JSON (stores array of card objects with icon, title, description)
ALTER TABLE feature_styling ADD COLUMN IF NOT EXISTS highlights_cards JSON DEFAULT NULL AFTER highlights_desc_color;
