-- Migration: Fix double-encoded HTML entities in text content
-- This fixes content that was previously encoded with htmlspecialchars() on save
-- and would be double-encoded when displayed with htmlspecialchars() again

-- Fix case_studies table
UPDATE case_studies 
SET challenge = REPLACE(challenge, '&amp;', '&')
WHERE challenge LIKE '%&amp;%';

UPDATE case_studies 
SET solution = REPLACE(solution, '&amp;', '&')
WHERE solution LIKE '%&amp;%';

UPDATE case_studies 
SET results = REPLACE(results, '&amp;', '&')
WHERE results LIKE '%&amp;%';

UPDATE case_studies 
SET title = REPLACE(title, '&amp;', '&')
WHERE title LIKE '%&amp;%';

UPDATE case_studies 
SET client_name = REPLACE(client_name, '&amp;', '&')
WHERE client_name LIKE '%&amp;%';

UPDATE case_studies 
SET industry = REPLACE(industry, '&amp;', '&')
WHERE industry LIKE '%&amp;%';

-- Fix features table
UPDATE features 
SET name = REPLACE(name, '&amp;', '&')
WHERE name LIKE '%&amp;%';

UPDATE features 
SET description = REPLACE(description, '&amp;', '&')
WHERE description LIKE '%&amp;%';

-- Fix solutions table
UPDATE solutions 
SET name = REPLACE(name, '&amp;', '&')
WHERE name LIKE '%&amp;%';

UPDATE solutions 
SET description = REPLACE(description, '&amp;', '&')
WHERE description LIKE '%&amp;%';

UPDATE solutions 
SET tagline = REPLACE(tagline, '&amp;', '&')
WHERE tagline LIKE '%&amp;%';

-- Fix plans table
UPDATE plans 
SET name = REPLACE(name, '&amp;', '&')
WHERE name LIKE '%&amp;%';

UPDATE plans 
SET description = REPLACE(description, '&amp;', '&')
WHERE description LIKE '%&amp;%';

-- Fix hero_slides table
UPDATE hero_slides 
SET title = REPLACE(title, '&amp;', '&')
WHERE title LIKE '%&amp;%';

UPDATE hero_slides 
SET description = REPLACE(description, '&amp;', '&')
WHERE description LIKE '%&amp;%';

UPDATE hero_slides 
SET subtitle = REPLACE(subtitle, '&amp;', '&')
WHERE subtitle LIKE '%&amp;%';

UPDATE hero_slides 
SET highlight_line1 = REPLACE(highlight_line1, '&amp;', '&')
WHERE highlight_line1 LIKE '%&amp;%';

UPDATE hero_slides 
SET highlight_line2 = REPLACE(highlight_line2, '&amp;', '&')
WHERE highlight_line2 LIKE '%&amp;%';

-- Fix settings table
UPDATE settings 
SET setting_value = REPLACE(setting_value, '&amp;', '&')
WHERE setting_value LIKE '%&amp;%';

-- Fix testimonials table
UPDATE testimonials 
SET content = REPLACE(content, '&amp;', '&')
WHERE content LIKE '%&amp;%';

UPDATE testimonials 
SET author_name = REPLACE(author_name, '&amp;', '&')
WHERE author_name LIKE '%&amp;%';

UPDATE testimonials 
SET author_title = REPLACE(author_title, '&amp;', '&')
WHERE author_title LIKE '%&amp;%';

UPDATE testimonials 
SET company_name = REPLACE(company_name, '&amp;', '&')
WHERE company_name LIKE '%&amp;%';

-- Fix why_choose_cards table
UPDATE why_choose_cards 
SET title = REPLACE(title, '&amp;', '&')
WHERE title LIKE '%&amp;%';

UPDATE why_choose_cards 
SET description = REPLACE(description, '&amp;', '&')
WHERE description LIKE '%&amp;%';

-- Fix faqs table
UPDATE faqs 
SET question = REPLACE(question, '&amp;', '&')
WHERE question LIKE '%&amp;%';

UPDATE faqs 
SET answer = REPLACE(answer, '&amp;', '&')
WHERE answer LIKE '%&amp;%';

-- Also fix other common HTML entities that might have been double-encoded
-- Fix &lt; (less than)
UPDATE case_studies SET challenge = REPLACE(challenge, '&lt;', '<') WHERE challenge LIKE '%&lt;%';
UPDATE case_studies SET solution = REPLACE(solution, '&lt;', '<') WHERE solution LIKE '%&lt;%';
UPDATE case_studies SET results = REPLACE(results, '&lt;', '<') WHERE results LIKE '%&lt;%';

-- Fix &gt; (greater than)
UPDATE case_studies SET challenge = REPLACE(challenge, '&gt;', '>') WHERE challenge LIKE '%&gt;%';
UPDATE case_studies SET solution = REPLACE(solution, '&gt;', '>') WHERE solution LIKE '%&gt;%';
UPDATE case_studies SET results = REPLACE(results, '&gt;', '>') WHERE results LIKE '%&gt;%';

-- Fix &quot; (double quote)
UPDATE case_studies SET challenge = REPLACE(challenge, '&quot;', '"') WHERE challenge LIKE '%&quot;%';
UPDATE case_studies SET solution = REPLACE(solution, '&quot;', '"') WHERE solution LIKE '%&quot;%';
UPDATE case_studies SET results = REPLACE(results, '&quot;', '"') WHERE results LIKE '%&quot;%';

-- Fix &#039; or &apos; (single quote)
UPDATE case_studies SET challenge = REPLACE(challenge, '&#039;', "'") WHERE challenge LIKE '%&#039;%';
UPDATE case_studies SET solution = REPLACE(solution, '&#039;', "'") WHERE solution LIKE '%&#039;%';
UPDATE case_studies SET results = REPLACE(results, '&#039;', "'") WHERE results LIKE '%&#039;%';
