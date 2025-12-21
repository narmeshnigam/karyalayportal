-- Migration: Add FAQs section controls to feature_styling table
-- Date: 2024-12-19
-- Description: Adds theme, heading, subheading, and FAQs JSON controls for FAQs section

ALTER TABLE feature_styling
ADD COLUMN faqs_section_theme VARCHAR(10) DEFAULT 'light' AFTER use_cases_cards,
ADD COLUMN faqs_section_heading VARCHAR(100) DEFAULT 'Frequently Asked Questions' AFTER faqs_section_theme,
ADD COLUMN faqs_section_subheading VARCHAR(200) DEFAULT 'Everything you need to know about this feature. Can''t find what you''re looking for?' AFTER faqs_section_heading,
ADD COLUMN faqs_cards JSON DEFAULT NULL AFTER faqs_section_subheading;
