-- Migration 064: Create normalized solutions tables from scratch
-- Creates three tables:
-- 1. solutions - Core solution data
-- 2. solution_styling - All styling/color fields
-- 3. solution_content - JSON array content

-- ============================================================================
-- STEP 1: Create main solutions table
-- ============================================================================
CREATE TABLE IF NOT EXISTS solutions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    tagline VARCHAR(255) DEFAULT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    icon_image VARCHAR(500) DEFAULT NULL,
    video_url VARCHAR(500) DEFAULT NULL,
    demo_video_url VARCHAR(500) DEFAULT NULL,
    color_theme VARCHAR(50) DEFAULT '#667eea',
    testimonial_id CHAR(36) DEFAULT NULL,
    pricing_note TEXT DEFAULT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    meta_keywords VARCHAR(500) DEFAULT NULL,
    display_order INT DEFAULT 0,
    status ENUM('DRAFT', 'PUBLISHED', 'ARCHIVED') DEFAULT 'DRAFT',
    featured_on_homepage BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_solutions_slug (slug),
    INDEX idx_solutions_status (status),
    INDEX idx_solutions_status_order (status, display_order),
    INDEX idx_solutions_featured (featured_on_homepage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STEP 2: Create solution_styling table
-- ============================================================================
CREATE TABLE IF NOT EXISTS solution_styling (
    id CHAR(36) PRIMARY KEY,
    solution_id CHAR(36) NOT NULL,
    
    -- Hero Section Styling
    hero_badge VARCHAR(100) DEFAULT NULL,
    hero_title_text VARCHAR(24) DEFAULT NULL,
    hero_title_color VARCHAR(7) DEFAULT '#FFFFFF',
    hero_subtitle_color VARCHAR(7) DEFAULT '#FFFFFF',
    hero_media_url VARCHAR(500) DEFAULT NULL,
    hero_media_type ENUM('image', 'gif', 'video') DEFAULT 'image',
    hero_bg_color VARCHAR(50) DEFAULT '#0a1628',
    hero_bg_gradient_color VARCHAR(7) DEFAULT NULL,
    hero_bg_gradient_opacity DECIMAL(3,2) DEFAULT 0.60,
    hero_bg_pattern_opacity DECIMAL(3,2) DEFAULT 0.03,
    
    -- Hero CTA Buttons
    hero_cta_primary_text VARCHAR(100) DEFAULT 'Get Started',
    hero_cta_primary_link VARCHAR(500) DEFAULT NULL,
    hero_cta_secondary_text VARCHAR(100) DEFAULT 'Watch Demo',
    hero_cta_secondary_link VARCHAR(500) DEFAULT NULL,
    
    -- Hero Primary Button Colors
    hero_primary_btn_bg_color VARCHAR(50) DEFAULT 'rgba(255,255,255,0.15)',
    hero_primary_btn_text_color VARCHAR(50) DEFAULT '#FFFFFF',
    hero_primary_btn_text_hover_color VARCHAR(50) DEFAULT '#FFFFFF',
    hero_primary_btn_border_color VARCHAR(50) DEFAULT 'rgba(255,255,255,0.3)',
    
    -- Hero Secondary Button Colors
    hero_secondary_btn_bg_color VARCHAR(50) DEFAULT 'rgba(255,255,255,0.1)',
    hero_secondary_btn_text_color VARCHAR(50) DEFAULT '#FFFFFF',
    hero_secondary_btn_text_hover_color VARCHAR(50) DEFAULT '#FFFFFF',
    hero_secondary_btn_border_color VARCHAR(50) DEFAULT 'rgba(255,255,255,0.2)',
    
    -- Key Benefits Section Styling
    key_benefits_section_enabled BOOLEAN DEFAULT FALSE,
    key_benefits_section_bg_color VARCHAR(50) DEFAULT '#0a1628',
    key_benefits_section_heading1 VARCHAR(24) DEFAULT '',
    key_benefits_section_heading2 VARCHAR(24) DEFAULT '',
    key_benefits_section_subheading TEXT DEFAULT NULL,
    key_benefits_section_heading_color VARCHAR(50) DEFAULT '#FFFFFF',
    key_benefits_section_subheading_color VARCHAR(50) DEFAULT '#ffffffb3',
    key_benefits_section_card_bg_color VARCHAR(50) DEFAULT '#ffffff14',
    key_benefits_section_card_border_color VARCHAR(50) DEFAULT '#ffffff1a',
    key_benefits_section_card_hover_bg_color VARCHAR(50) DEFAULT '#2563eb',
    key_benefits_section_card_text_color VARCHAR(50) DEFAULT '#FFFFFF',
    key_benefits_section_card_icon_color VARCHAR(50) DEFAULT '#ffffff99',
    
    -- Feature Showcase Section Styling
    feature_showcase_section_enabled BOOLEAN DEFAULT FALSE,
    feature_showcase_section_title VARCHAR(100) DEFAULT 'One solution. All business sizes.',
    feature_showcase_section_subtitle VARCHAR(255) DEFAULT NULL,
    feature_showcase_section_bg_color VARCHAR(50) DEFAULT '#ffffff',
    feature_showcase_section_title_color VARCHAR(50) DEFAULT '#1a202c',
    feature_showcase_section_subtitle_color VARCHAR(50) DEFAULT '#718096',
    feature_showcase_card_bg_color VARCHAR(50) DEFAULT '#ffffff',
    feature_showcase_card_border_color VARCHAR(50) DEFAULT '#e2e8f0',
    feature_showcase_card_badge_bg_color VARCHAR(50) DEFAULT '#ebf8ff',
    feature_showcase_card_badge_text_color VARCHAR(50) DEFAULT '#2b6cb0',
    feature_showcase_card_heading_color VARCHAR(50) DEFAULT '#1a202c',
    feature_showcase_card_text_color VARCHAR(50) DEFAULT '#4a5568',
    feature_showcase_card_icon_color VARCHAR(50) DEFAULT '#38a169',
    
    -- CTA Banner Section Styling
    cta_banner_enabled BOOLEAN DEFAULT TRUE,
    cta_banner_image_url VARCHAR(500) DEFAULT NULL,
    cta_banner_overlay_color VARCHAR(50) DEFAULT 'rgba(0,0,0,0.5)',
    cta_banner_overlay_intensity DECIMAL(3,2) DEFAULT 0.50,
    cta_banner_heading1 VARCHAR(100) DEFAULT 'Streamline across 30+ modules.',
    cta_banner_heading2 VARCHAR(100) DEFAULT 'Transform your business today!',
    cta_banner_heading_color VARCHAR(20) DEFAULT '#FFFFFF',
    cta_banner_button_text VARCHAR(50) DEFAULT 'Explore ERP Solutions',
    cta_banner_button_link VARCHAR(255) DEFAULT '#contact-form',
    cta_banner_button_bg_color VARCHAR(20) DEFAULT '#FFFFFF',
    cta_banner_button_text_color VARCHAR(20) DEFAULT '#2563eb',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_solution_styling_solution_id (solution_id),
    UNIQUE INDEX idx_solution_styling_unique (solution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STEP 3: Create solution_content table
-- ============================================================================
CREATE TABLE IF NOT EXISTS solution_content (
    id CHAR(36) PRIMARY KEY,
    solution_id CHAR(36) NOT NULL,
    
    -- JSON Content Arrays
    features JSON DEFAULT NULL,
    screenshots JSON DEFAULT NULL,
    faqs JSON DEFAULT NULL,
    key_benefits_cards JSON DEFAULT NULL,
    feature_showcase_cards JSON DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_solution_content_solution_id (solution_id),
    UNIQUE INDEX idx_solution_content_unique (solution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STEP 4: Add foreign key constraints
-- ============================================================================
ALTER TABLE solution_styling
    ADD CONSTRAINT fk_solution_styling_solution 
    FOREIGN KEY (solution_id) REFERENCES solutions(id) ON DELETE CASCADE;

ALTER TABLE solution_content
    ADD CONSTRAINT fk_solution_content_solution 
    FOREIGN KEY (solution_id) REFERENCES solutions(id) ON DELETE CASCADE;
