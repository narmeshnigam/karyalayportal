-- Migration 059: Rename Payout Section to Key Benefits Section
-- This renames all payout_* columns to key_benefits_* columns

-- Rename payout section columns to key_benefits
ALTER TABLE solutions CHANGE COLUMN payout_section_enabled key_benefits_section_enabled BOOLEAN DEFAULT FALSE;
ALTER TABLE solutions CHANGE COLUMN payout_section_bg_color key_benefits_section_bg_color VARCHAR(50) DEFAULT '#0a1628';
ALTER TABLE solutions CHANGE COLUMN payout_section_heading1 key_benefits_section_heading1 VARCHAR(24) DEFAULT '';
ALTER TABLE solutions CHANGE COLUMN payout_section_heading2 key_benefits_section_heading2 VARCHAR(24) DEFAULT '';
ALTER TABLE solutions CHANGE COLUMN payout_section_subheading key_benefits_section_subheading TEXT DEFAULT '';
ALTER TABLE solutions CHANGE COLUMN payout_section_heading_color key_benefits_section_heading_color VARCHAR(50) DEFAULT '#FFFFFF';
ALTER TABLE solutions CHANGE COLUMN payout_section_subheading_color key_benefits_section_subheading_color VARCHAR(50) DEFAULT '#ffffffb3';
ALTER TABLE solutions CHANGE COLUMN payout_section_card_bg_color key_benefits_section_card_bg_color VARCHAR(50) DEFAULT '#ffffff14';
ALTER TABLE solutions CHANGE COLUMN payout_section_card_border_color key_benefits_section_card_border_color VARCHAR(50) DEFAULT '#ffffff1a';
ALTER TABLE solutions CHANGE COLUMN payout_section_card_hover_bg_color key_benefits_section_card_hover_bg_color VARCHAR(50) DEFAULT '#2563eb';
ALTER TABLE solutions CHANGE COLUMN payout_section_card_text_color key_benefits_section_card_text_color VARCHAR(50) DEFAULT '#FFFFFF';
ALTER TABLE solutions CHANGE COLUMN payout_section_card_icon_color key_benefits_section_card_icon_color VARCHAR(50) DEFAULT '#ffffff99';
ALTER TABLE solutions CHANGE COLUMN payout_cards key_benefits_cards JSON DEFAULT NULL;