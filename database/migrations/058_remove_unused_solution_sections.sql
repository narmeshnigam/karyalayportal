-- Migration 058: Remove Unused Solution Sections
-- This removes stats, highlight_cards, workflow_steps, benefits, use_cases, integrations columns

-- Drop unused section columns
ALTER TABLE solutions DROP COLUMN IF EXISTS stats;
ALTER TABLE solutions DROP COLUMN IF EXISTS highlight_cards;
ALTER TABLE solutions DROP COLUMN IF EXISTS workflow_steps;
ALTER TABLE solutions DROP COLUMN IF EXISTS benefits;
ALTER TABLE solutions DROP COLUMN IF EXISTS use_cases;
ALTER TABLE solutions DROP COLUMN IF EXISTS integrations;