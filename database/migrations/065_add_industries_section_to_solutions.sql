-- Migration 065: Add Industries Section to Solutions
-- Adds editable industries gallery section to solution pages

-- ============================================================================
-- STEP 1: Add industries section styling fields to solution_styling table
-- ============================================================================
ALTER TABLE solution_styling
    ADD COLUMN industries_section_enabled BOOLEAN DEFAULT TRUE AFTER cta_banner_button_text_color,
    ADD COLUMN industries_section_title VARCHAR(100) DEFAULT 'Industries We Serve' AFTER industries_section_enabled,
    ADD COLUMN industries_section_subtitle VARCHAR(255) DEFAULT 'Trusted by leading organizations across diverse sectors' AFTER industries_section_title,
    ADD COLUMN industries_section_bg_color VARCHAR(50) DEFAULT '#f8fafc' AFTER industries_section_subtitle,
    ADD COLUMN industries_section_title_color VARCHAR(50) DEFAULT '#1a202c' AFTER industries_section_bg_color,
    ADD COLUMN industries_section_subtitle_color VARCHAR(50) DEFAULT '#718096' AFTER industries_section_title_color,
    ADD COLUMN industries_section_card_overlay_color VARCHAR(50) DEFAULT 'rgba(0,0,0,0.4)' AFTER industries_section_subtitle_color,
    ADD COLUMN industries_section_card_title_color VARCHAR(50) DEFAULT '#FFFFFF' AFTER industries_section_card_overlay_color,
    ADD COLUMN industries_section_card_desc_color VARCHAR(50) DEFAULT 'rgba(255,255,255,0.9)' AFTER industries_section_card_title_color,
    ADD COLUMN industries_section_card_btn_bg_color VARCHAR(50) DEFAULT 'rgba(255,255,255,0.2)' AFTER industries_section_card_desc_color,
    ADD COLUMN industries_section_card_btn_text_color VARCHAR(50) DEFAULT '#FFFFFF' AFTER industries_section_card_btn_bg_color;

-- ============================================================================
-- STEP 2: Add industries_cards JSON field to solution_content table
-- ============================================================================
ALTER TABLE solution_content
    ADD COLUMN industries_cards JSON DEFAULT NULL AFTER feature_showcase_cards;
