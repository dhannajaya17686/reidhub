-- Edu Archive admin moderation extensions

ALTER TABLE edu_resources
    ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0 AFTER admin_feedback;

CREATE INDEX idx_edu_resources_status_hidden ON edu_resources (status, is_hidden);
CREATE INDEX idx_edu_resources_subject_year ON edu_resources (subject, year_level);

CREATE TABLE IF NOT EXISTS edu_filter_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_edu_filter_tags_name ON edu_filter_tags (name);

INSERT INTO edu_filter_tags (name, slug) VALUES
('database', 'database'),
('operating systems', 'operating-systems'),
('algorithms', 'algorithms'),
('networking', 'networking'),
('oop', 'oop')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    slug = VALUES(slug);
