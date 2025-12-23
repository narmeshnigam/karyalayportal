-- Migration 075: Add category field to solutions table
-- This allows categorizing solutions for the public solutions list page

ALTER TABLE solutions ADD COLUMN category VARCHAR(100) DEFAULT 'core' AFTER display_order;

-- Add index for faster category-based queries
CREATE INDEX idx_solutions_category ON solutions(category);

-- Update existing solutions with default category
UPDATE solutions SET category = 'core' WHERE category IS NULL OR category = '';
