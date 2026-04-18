-- ============================================================
-- Community Schema: Forum Questions, Answers & Community Posts
-- Run this against the `reidhub` database to enable
-- forum, blog, post and event features on the dashboard.
-- ============================================================

-- ------------------------------------------------------------
-- 1. forum_questions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS forum_questions (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    title       VARCHAR(500)    NOT NULL,
    body        TEXT            NOT NULL,
    votes       INT             NOT NULL DEFAULT 0,
    views       INT             NOT NULL DEFAULT 0,
    status      ENUM('open','answered','closed') NOT NULL DEFAULT 'open',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fq_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_fq_status  (status),
    INDEX idx_fq_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. forum_answers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS forum_answers (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    user_id     BIGINT UNSIGNED NOT NULL,
    body        TEXT            NOT NULL,
    votes       INT             NOT NULL DEFAULT 0,
    is_accepted TINYINT(1)      NOT NULL DEFAULT 0,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fa_question FOREIGN KEY (question_id) REFERENCES forum_questions(id) ON DELETE CASCADE,
    CONSTRAINT fk_fa_user     FOREIGN KEY (user_id)     REFERENCES users(id)            ON DELETE CASCADE,
    INDEX idx_fa_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. community_posts  (handles posts, blogs AND events in one table)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS community_posts (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          BIGINT UNSIGNED                         NOT NULL,
    type             ENUM('post','blog','event')             NOT NULL DEFAULT 'post',
    title            VARCHAR(500)                            NOT NULL,
    body             TEXT                                    NOT NULL,
    image_url        VARCHAR(500)                            NULL,
    group_name       VARCHAR(200)                            NULL,       -- e.g. "ACM Student Chapter"
    likes            INT                                     NOT NULL DEFAULT 0,
    comments_count   INT                                     NOT NULL DEFAULT 0,
    shares           INT                                     NOT NULL DEFAULT 0,
    is_featured      TINYINT(1)                              NOT NULL DEFAULT 0,
    -- Event-specific columns (NULL for posts/blogs)
    event_date       DATE                                    NULL,
    event_time_start TIME                                    NULL,
    event_time_end   TIME                                    NULL,
    event_location   VARCHAR(255)                            NULL,
    status           ENUM('active','archived')               NOT NULL DEFAULT 'active',
    created_at       DATETIME                                NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME                                NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_cp_type_featured (type, is_featured),
    INDEX idx_cp_type_created  (type, created_at),
    INDEX idx_cp_event_date    (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
