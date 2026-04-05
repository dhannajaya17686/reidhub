-- Create FAQ table
CREATE TABLE IF NOT EXISTS faqs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer LONGTEXT NOT NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_display_order (display_order),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample FAQs
INSERT INTO faqs (question, answer, display_order) VALUES
(
    'How do I submit a question or feedback?',
    'Click on "Ask a Question" in the Help & Feedback menu. Fill in the category, subject, and your message, then click Submit. Your question will be saved and our admins will review it.',
    1
),
(
    'What should I do if I encounter a bug?',
    'Select "Bug Report" as the category when submitting your question. Describe the issue in detail, including steps to reproduce it. Our development team will investigate and fix it as soon as possible.',
    2
),
(
    'How do I request a new feature?',
    'Use the "Feature Request" category in the Help & Feedback menu. Describe your feature idea and why you think it would be useful. Feature requests help us understand what our users need.',
    3
),
(
    'How long does it take to get a reply?',
    'Our admin team typically reviews and responds to questions within 24-48 hours. You will receive an email notification when an admin replies to your question.',
    4
),
(
    'Can I edit or delete my submitted question?',
    'Once submitted, questions cannot be directly edited or deleted by users. However, you can submit a new question asking us to ignore your previous one, or contact support for assistance.',
    5
),
(
    'Will my question be kept confidential?',
    'Your question and its details are visible to our admin team. If your question contains sensitive information, please mention it in your message and admins will handle it appropriately.',
    6
),
(
    'What if I don\'t see my question in My Questions?',
    'Check that you are logged in with the correct account. If the question was just submitted, refresh the page. If it still doesn\'t appear, please submit a bug report.',
    7
),
(
    'Can I submit the same question multiple times?',
    'While it\'s technically possible, we recommend submitting your question only once. If you need to add more information, submit a new question mentioning your original topic.',
    8
);
