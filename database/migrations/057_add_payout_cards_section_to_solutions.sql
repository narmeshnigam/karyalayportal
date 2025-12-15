-- Migration 057: Add Payout Cards Section to Solutions
-- This adds a new section after the hero with 4 interactive cards (Razorpay-style)

-- Add payout cards section fields
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_enabled BOOLEAN DEFAULT FALSE;
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_bg_color VARCHAR(50) DEFAULT '#0a1628';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_heading1 VARCHAR(24) DEFAULT '';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_heading2 VARCHAR(24) DEFAULT '';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_subheading TEXT DEFAULT '';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_heading_color VARCHAR(50) DEFAULT '#FFFFFF';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_subheading_color VARCHAR(50) DEFAULT '#ffffffb3';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_card_bg_color VARCHAR(50) DEFAULT '#ffffff14';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_card_border_color VARCHAR(50) DEFAULT '#ffffff1a';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_card_hover_bg_color VARCHAR(50) DEFAULT '#2563eb';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_card_text_color VARCHAR(50) DEFAULT '#FFFFFF';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_section_card_icon_color VARCHAR(50) DEFAULT '#ffffff99';
ALTER TABLE solutions ADD COLUMN IF NOT EXISTS payout_cards JSON DEFAULT NULL;

-- payout_cards JSON structure:
-- [
--   {
--     "icon": "bank",
--     "title": "Card Title (max 24 chars)",
--     "description": "Card description text (max 240 chars)"
--   },
--   ... (exactly 4 cards)
-- ]
