-- Migration 068: Add FAQs Section fields to solution_styling table
-- Allows admin to customize the FAQs section on solution detail pages
-- Fields: theme (dark/light), heading (48 chars), subheading (120 chars)

ALTER TABLE solution_styling
    ADD COLUMN faqs_section_theme VARCHAR(10) DEFAULT 'light' AFTER testimonials_section_subheading,
    ADD COLUMN faqs_section_heading VARCHAR(48) DEFAULT 'Frequently Asked Questions' AFTER faqs_section_theme,
    ADD COLUMN faqs_section_subheading VARCHAR(120) DEFAULT 'Everything you need to know about our solution. Can''t find what you''re looking for? Feel free to contact us.' AFTER faqs_section_heading;
