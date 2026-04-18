-- Step 1: Expand ENUM to include both old and new values
ALTER TABLE user_questions 
MODIFY COLUMN category ENUM('general_question', 'bug_report', 'feature_request', 'feedback', 'academic_issues', 'extracurricular_issues', 'sports_issues', 'infrastructure_issues', 'other_issues', 'feedbacks') NOT NULL DEFAULT 'academic_issues';

-- Step 2: Migrate existing categories to new ones
UPDATE user_questions SET category = 'academic_issues' WHERE category = 'general_question';
UPDATE user_questions SET category = 'infrastructure_issues' WHERE category = 'bug_report';
UPDATE user_questions SET category = 'other_issues' WHERE category = 'feature_request';
UPDATE user_questions SET category = 'feedbacks' WHERE category = 'feedback';

-- Step 3: Remove old ENUM values
ALTER TABLE user_questions 
MODIFY COLUMN category ENUM('academic_issues', 'extracurricular_issues', 'sports_issues', 'infrastructure_issues', 'other_issues', 'feedbacks') NOT NULL DEFAULT 'academic_issues';
