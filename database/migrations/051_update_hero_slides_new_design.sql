-- Update hero_slides table for new hero section design
-- Adds two-liner highlight text, description, button settings, and color customization

ALTER TABLE hero_slides
    -- Two-liner highlight text (max 30 chars each)
    ADD COLUMN highlight_line1 VARCHAR(30) DEFAULT NULL AFTER title,
    ADD COLUMN highlight_line2 VARCHAR(30) DEFAULT NULL AFTER highlight_line1,
    -- Small description text
    ADD COLUMN description TEXT DEFAULT NULL AFTER highlight_line2,
    -- Know More button link (image link updated through admin)
    ADD COLUMN know_more_url VARCHAR(500) DEFAULT NULL AFTER link_text,
    -- Text colors (hex format)
    ADD COLUMN highlight_line1_color VARCHAR(12) DEFAULT '#FFFFFF' AFTER know_more_url,
    ADD COLUMN highlight_line2_color VARCHAR(12) DEFAULT '#FFFFFF' AFTER highlight_line1_color,
    ADD COLUMN description_color VARCHAR(12) DEFAULT '#FFFFFF' AFTER highlight_line2_color,
    -- Button colors
    ADD COLUMN primary_btn_bg_color VARCHAR(12) DEFAULT '#3B82F6' AFTER description_color,
    ADD COLUMN primary_btn_text_color VARCHAR(12) DEFAULT '#FFFFFF' AFTER primary_btn_bg_color,
    ADD COLUMN secondary_btn_bg_color VARCHAR(12) DEFAULT 'transparent' AFTER primary_btn_text_color,
    ADD COLUMN secondary_btn_text_color VARCHAR(12) DEFAULT '#FFFFFF' AFTER secondary_btn_bg_color,
    ADD COLUMN secondary_btn_border_color VARCHAR(12) DEFAULT '#FFFFFF' AFTER secondary_btn_text_color;

-- Update existing slides to use new structure (migrate title to highlight_line1 if short enough)
UPDATE hero_slides 
SET highlight_line1 = LEFT(title, 30),
    description = subtitle
WHERE title IS NOT NULL;
