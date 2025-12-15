-- Migration 066: Add Testimonials Section fields to solution_styling table
-- Allows admin to customize the testimonials section on solution detail pages
-- Fields: theme (dark/light), heading (48 chars), subheading (120 chars)

ALTER TABLE solution_styling
    ADD COLUMN testimonials_section_theme VARCHAR(10) DEFAULT 'light' AFTER industries_section_card_btn_text_color,
    ADD COLUMN testimonials_section_heading VARCHAR(48) DEFAULT 'What Our Customers Say' AFTER testimonials_section_theme,
    ADD COLUMN testimonials_section_subheading VARCHAR(120) DEFAULT 'Trusted by leading businesses who have transformed their operations with our solutions' AFTER testimonials_section_heading;
