-- Migration: Add CTA Banner Section fields to solutions table
-- This allows admin to customize the CTA banner section on solution detail pages

ALTER TABLE solutions
    -- CTA Banner Section Enable/Disable
    ADD COLUMN cta_banner_enabled BOOLEAN DEFAULT TRUE,
    
    -- Image and Overlay
    ADD COLUMN cta_banner_image_url VARCHAR(500) DEFAULT NULL,
    ADD COLUMN cta_banner_overlay_color VARCHAR(50) DEFAULT 'rgba(0,0,0,0.5)',
    ADD COLUMN cta_banner_overlay_intensity DECIMAL(3,2) DEFAULT 0.50,
    
    -- Heading Texts
    ADD COLUMN cta_banner_heading1 VARCHAR(100) DEFAULT 'Streamline across 30+ modules.',
    ADD COLUMN cta_banner_heading2 VARCHAR(100) DEFAULT 'Transform your business today!',
    ADD COLUMN cta_banner_heading_color VARCHAR(20) DEFAULT '#FFFFFF',
    
    -- Button
    ADD COLUMN cta_banner_button_text VARCHAR(50) DEFAULT 'Explore ERP Solutions',
    ADD COLUMN cta_banner_button_link VARCHAR(255) DEFAULT '#contact-form',
    ADD COLUMN cta_banner_button_bg_color VARCHAR(20) DEFAULT '#FFFFFF',
    ADD COLUMN cta_banner_button_text_color VARCHAR(20) DEFAULT '#2563eb';
