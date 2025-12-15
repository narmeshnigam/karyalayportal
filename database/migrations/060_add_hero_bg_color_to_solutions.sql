-- Migration 060: Add Hero Background Color Field to Solutions
-- This adds a hero_bg_color field to control the base background color of the hero section

-- Add hero_bg_color column
ALTER TABLE solutions ADD COLUMN hero_bg_color VARCHAR(50) DEFAULT '#0a1628' AFTER hero_bg_gradient_color;