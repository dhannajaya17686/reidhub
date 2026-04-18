-- Update user_questions table to use new category values
ALTER TABLE user_questions 
MODIFY COLUMN category ENUM('academic_issues', 'extracurricular_issues', 'sports_issues', 'infrastructure_issues', 'other_issues', 'feedbacks') NOT NULL DEFAULT 'academic_issues';
