-- Migration 069: Create feature_styling table for hero section management
-- This follows the same pattern as solution_styling for solutions
-- Allows dynamic management of the feature detail page hero section from admin panel

-- ============================================================================
-- Create feature_styling table
-- ============================================================================
CREATE TABLE IF NOT EXISTS feature_styling (
    id CHAR(36) PRIMARY KEY,
    feature_id CHAR(36) NOT NULL,
    
    -- Hero Section Background
    hero_bg_color VARCHAR(20) DEFAULT '#fafafa',
    hero_bg_gradient_start VARCHAR(20) DEFAULT '#fafafa',
    hero_bg_gradient_end VARCHAR(20) DEFAULT '#f5f5f5',
    
    -- Hero Title Styling
    hero_title_gradient_start VARCHAR(20) DEFAULT '#111827',
    hero_title_gradient_middle VARCHAR(20) DEFAULT '#667eea',
    hero_title_gradient_end VARCHAR(20) DEFAULT '#764ba2',
    
    -- Hero Subtitle Color
    hero_subtitle_color VARCHAR(20) DEFAULT '#6b7280',
    
    -- Hero Decorative Elements
    hero_decor_line_color VARCHAR(20) DEFAULT '#667eea14',
    hero_decor_circle_color VARCHAR(20) DEFAULT '#667eea1a',
    
    -- Hero CTA Buttons
    hero_cta_primary_text VARCHAR(50) DEFAULT 'Get Started',
    hero_cta_primary_link VARCHAR(100) DEFAULT '#contact-form',
    hero_cta_primary_bg_color VARCHAR(20) DEFAULT '#667eea',
    hero_cta_primary_text_color VARCHAR(20) DEFAULT '#FFFFFF',
    hero_cta_primary_hover_bg_color VARCHAR(20) DEFAULT '#5a6fd6',
    
    hero_cta_secondary_text VARCHAR(50) DEFAULT 'Learn how it works',
    hero_cta_secondary_link VARCHAR(100) DEFAULT '#how-it-works',
    hero_cta_secondary_bg_color VARCHAR(20) DEFAULT 'transparent',
    hero_cta_secondary_text_color VARCHAR(20) DEFAULT '#374151',
    hero_cta_secondary_border_color VARCHAR(20) DEFAULT '#e5e7eb',
    
    -- Hero Stats
    hero_stats_enabled BOOLEAN DEFAULT TRUE,
    hero_stat1_value VARCHAR(20) DEFAULT '30+',
    hero_stat1_label VARCHAR(30) DEFAULT 'Modules',
    hero_stat2_value VARCHAR(20) DEFAULT '500+',
    hero_stat2_label VARCHAR(30) DEFAULT 'Businesses',
    hero_stat3_value VARCHAR(20) DEFAULT '24/7',
    hero_stat3_label VARCHAR(30) DEFAULT 'Support',
    hero_stats_value_color VARCHAR(20) DEFAULT '#111827',
    hero_stats_label_color VARCHAR(20) DEFAULT '#9ca3af',
    
    -- Breadcrumb Colors
    hero_breadcrumb_link_color VARCHAR(20) DEFAULT '#9ca3af',
    hero_breadcrumb_active_color VARCHAR(20) DEFAULT '#374151',
    hero_breadcrumb_separator_color VARCHAR(20) DEFAULT '#d1d5db',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_feature_styling_feature_id (feature_id),
    UNIQUE INDEX idx_feature_styling_unique (feature_id),
    CONSTRAINT fk_feature_styling_feature 
        FOREIGN KEY (feature_id) REFERENCES features(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Insert default styling for existing features
-- ============================================================================
INSERT INTO feature_styling (id, feature_id)
SELECT UUID(), id FROM features 
WHERE id NOT IN (SELECT feature_id FROM feature_styling);
