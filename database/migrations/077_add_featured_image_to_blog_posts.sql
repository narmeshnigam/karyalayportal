-- Migration: Add featured_image to blog_posts table
-- Date: 2025-12-25
-- Description: Adds featured_image field for blog post cover images

ALTER TABLE blog_posts ADD COLUMN featured_image VARCHAR(500) NULL AFTER excerpt;

