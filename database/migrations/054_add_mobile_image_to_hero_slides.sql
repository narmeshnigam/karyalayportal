-- Add mobile_image_url column to hero_slides table for responsive images
-- This allows uploading a separate image optimized for mobile view

ALTER TABLE hero_slides
    ADD COLUMN mobile_image_url VARCHAR(500) DEFAULT NULL AFTER image_url;

-- Add comment for documentation
-- mobile_image_url: Optional mobile-specific background image. If not set, the main image_url will be used.
