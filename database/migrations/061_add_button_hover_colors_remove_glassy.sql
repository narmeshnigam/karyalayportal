-- Migration 061: Add button hover text colors and remove glassy effect fields
-- This migration adds hover text color fields for buttons and removes glassy effect controls
-- since glassy effects are now part of the template (always enabled)

-- Add hover text color fields for buttons
ALTER TABLE solutions 
ADD COLUMN hero_primary_btn_text_hover_color VARCHAR(50) DEFAULT '#FFFFFF' AFTER hero_primary_btn_text_color,
ADD COLUMN hero_secondary_btn_text_hover_color VARCHAR(50) DEFAULT '#FFFFFF' AFTER hero_secondary_btn_text_color;

-- Remove glassy effect fields (no longer needed as they're part of template)
-- Note: These fields may not exist in all installations, so we use IF EXISTS
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'solutions' 
     AND column_name = 'hero_media_glassy' 
     AND table_schema = DATABASE()) > 0,
    'ALTER TABLE solutions DROP COLUMN hero_media_glassy',
    'SELECT "Column hero_media_glassy does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'solutions' 
     AND column_name = 'hero_buttons_glassy' 
     AND table_schema = DATABASE()) > 0,
    'ALTER TABLE solutions DROP COLUMN hero_buttons_glassy',
    'SELECT "Column hero_buttons_glassy does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing solutions to have default hover colors if they don't have them
UPDATE solutions 
SET hero_primary_btn_text_hover_color = COALESCE(hero_primary_btn_text_hover_color, hero_primary_btn_text_color, '#FFFFFF'),
    hero_secondary_btn_text_hover_color = COALESCE(hero_secondary_btn_text_hover_color, hero_secondary_btn_text_color, '#FFFFFF')
WHERE hero_primary_btn_text_hover_color IS NULL 
   OR hero_secondary_btn_text_hover_color IS NULL;