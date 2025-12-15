-- Migration 056: Solution Hero Section Enhancements
-- Adds support for:
-- 1. GIF/MP4 autoplaying media instead of static icon
-- 2. Glassy effect controls for buttons and media container
-- 3. Title and button styling controls similar to home page hero slider
-- 4. Single-line title limited to 24 characters
-- 5. Hero background pattern and gradient controls

-- Add hero media columns (gif/mp4 support)
ALTER TABLE solutions 
    ADD COLUMN IF NOT EXISTS hero_media_url VARCHAR(500) AFTER hero_image,
    ADD COLUMN IF NOT EXISTS hero_media_type ENUM('image', 'gif', 'video') DEFAULT 'image' AFTER hero_media_url;

-- Add hero background styling columns
ALTER TABLE solutions 
    ADD COLUMN IF NOT EXISTS hero_bg_gradient_opacity DECIMAL(3,2) DEFAULT 0.60 AFTER hero_media_glassy,
    ADD COLUMN IF NOT EXISTS hero_bg_pattern_opacity DECIMAL(3,2) DEFAULT 0.03 AFTER hero_bg_gradient_opacity,
    ADD COLUMN IF NOT EXISTS hero_bg_gradient_color VARCHAR(7) DEFAULT NULL AFTER hero_bg_pattern_opacity;

-- Add hero title styling columns (single line, 24 char limit)
ALTER TABLE solutions 
    ADD COLUMN IF NOT EXISTS hero_title_text VARCHAR(24) AFTER hero_badge,
    ADD COLUMN IF NOT EXISTS hero_title_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER hero_title_text,
    ADD COLUMN IF NOT EXISTS hero_subtitle_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER hero_title_color;

-- Add button styling columns (similar to hero slider)
ALTER TABLE solutions 
    ADD COLUMN IF NOT EXISTS hero_primary_btn_bg_color VARCHAR(20) DEFAULT '#ffffff26' AFTER hero_cta_secondary_link,
    ADD COLUMN IF NOT EXISTS hero_primary_btn_text_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER hero_primary_btn_bg_color,
    ADD COLUMN IF NOT EXISTS hero_primary_btn_border_color VARCHAR(20) DEFAULT '#ffffff4d' AFTER hero_primary_btn_text_color,
    ADD COLUMN IF NOT EXISTS hero_secondary_btn_bg_color VARCHAR(20) DEFAULT '#ffffff1a' AFTER hero_primary_btn_border_color,
    ADD COLUMN IF NOT EXISTS hero_secondary_btn_text_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER hero_secondary_btn_bg_color,
    ADD COLUMN IF NOT EXISTS hero_secondary_btn_border_color VARCHAR(20) DEFAULT '#ffffff33' AFTER hero_secondary_btn_text_color;

-- Add glassy effect toggle columns
ALTER TABLE solutions 
    ADD COLUMN IF NOT EXISTS hero_buttons_glassy BOOLEAN DEFAULT TRUE AFTER hero_secondary_btn_border_color,
    ADD COLUMN IF NOT EXISTS hero_media_glassy BOOLEAN DEFAULT TRUE AFTER hero_buttons_glassy;

-- Column descriptions:
-- hero_media_url: URL to GIF or MP4 file for autoplaying animation
-- hero_media_type: Type of media (image, gif, video)
-- hero_title_text: Single line title, max 24 characters
-- hero_title_color: Color of the hero title text
-- hero_subtitle_color: Color of the subtitle/description text
-- hero_primary_btn_bg_color: Background color for primary CTA button
-- hero_primary_btn_text_color: Text color for primary CTA button
-- hero_primary_btn_border_color: Border color for primary CTA button
-- hero_secondary_btn_bg_color: Background color for secondary CTA button
-- hero_secondary_btn_text_color: Text color for secondary CTA button
-- hero_secondary_btn_border_color: Border color for secondary CTA button
-- hero_buttons_glassy: Enable glassy/frosted effect on buttons
-- hero_media_glassy: Enable glassy/frosted effect on media container


-- Column descriptions for new background settings:
-- hero_bg_gradient_opacity: Opacity of the gradient overlay (0.00 to 1.00, default 0.60)
-- hero_bg_pattern_opacity: Opacity of the pattern overlay (0.00 to 0.10, default 0.03)
-- hero_bg_gradient_color: Custom gradient color (uses theme color if NULL)
