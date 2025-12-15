-- Migration 062: Add Feature Showcase Section to Solutions
-- This adds fields for the dynamic "Key Solution" / Feature Showcase section
-- Maximum of 6 cards allowed

-- Section enable/disable and header text
ALTER TABLE solutions ADD COLUMN feature_showcase_section_enabled BOOLEAN DEFAULT FALSE AFTER key_benefits_cards;
ALTER TABLE solutions ADD COLUMN feature_showcase_section_title VARCHAR(100) DEFAULT 'One solution. All business sizes.' AFTER feature_showcase_section_enabled;
ALTER TABLE solutions ADD COLUMN feature_showcase_section_subtitle VARCHAR(255) DEFAULT 'From instant, self-serve payouts to custom integrations for enterprise scale operations' AFTER feature_showcase_section_title;

-- Section colors
ALTER TABLE solutions ADD COLUMN feature_showcase_section_bg_color VARCHAR(50) DEFAULT '#ffffff' AFTER feature_showcase_section_subtitle;
ALTER TABLE solutions ADD COLUMN feature_showcase_section_title_color VARCHAR(50) DEFAULT '#1a202c' AFTER feature_showcase_section_bg_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_section_subtitle_color VARCHAR(50) DEFAULT '#718096' AFTER feature_showcase_section_title_color;

-- Card styling
ALTER TABLE solutions ADD COLUMN feature_showcase_card_bg_color VARCHAR(50) DEFAULT '#ffffff' AFTER feature_showcase_section_subtitle_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_card_border_color VARCHAR(50) DEFAULT '#e2e8f0' AFTER feature_showcase_card_bg_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_card_badge_bg_color VARCHAR(50) DEFAULT '#ebf8ff' AFTER feature_showcase_card_border_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_card_badge_text_color VARCHAR(50) DEFAULT '#2b6cb0' AFTER feature_showcase_card_badge_bg_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_card_heading_color VARCHAR(50) DEFAULT '#1a202c' AFTER feature_showcase_card_badge_text_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_card_text_color VARCHAR(50) DEFAULT '#4a5568' AFTER feature_showcase_card_heading_color;
ALTER TABLE solutions ADD COLUMN feature_showcase_card_icon_color VARCHAR(50) DEFAULT '#38a169' AFTER feature_showcase_card_text_color;

-- Cards data (JSON array, max 6 cards)
-- Each card: { nav_label, badge, heading, image_url, features: [string, string, string] }
ALTER TABLE solutions ADD COLUMN feature_showcase_cards JSON DEFAULT NULL AFTER feature_showcase_card_icon_color;
