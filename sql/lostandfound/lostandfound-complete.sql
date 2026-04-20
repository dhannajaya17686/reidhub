
-- Table: lost_items
-- Stores all lost item reports submitted by users
CREATE TABLE IF NOT EXISTS lost_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NULL,
    description TEXT NOT NULL,
    last_known_location VARCHAR(255) NOT NULL,
    specific_area VARCHAR(255) NULL,
    date_time_lost DATETIME NOT NULL,
    contact_details VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    alt_contact VARCHAR(255) NULL,
    severity_level ENUM('General', 'Important', 'Critical') DEFAULT 'General',
    status ENUM('Still Missing', 'Returned') DEFAULT 'Still Missing',
    noc_notified BOOLEAN DEFAULT FALSE,
    reward_offered BOOLEAN DEFAULT FALSE,
    reward_amount DECIMAL(10,2) NULL,
    reward_details TEXT NULL,
    special_instructions TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_severity (severity_level),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: found_items
-- Stores all found item reports submitted by users
CREATE TABLE IF NOT EXISTS found_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NULL,
    description TEXT NOT NULL,
    found_location VARCHAR(255) NOT NULL,
    specific_area VARCHAR(255) NULL,
    date_time_found DATETIME NOT NULL,
    contact_details VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    alt_contact VARCHAR(255) NULL,
    item_condition ENUM('Excellent', 'Good', 'Fair', 'Poor') DEFAULT 'Good',
    current_location VARCHAR(255) NULL,
    special_instructions TEXT NULL,
    reported_to_union BOOLEAN DEFAULT TRUE,
    status ENUM('Available', 'Collected', 'Returned to Owner') DEFAULT 'Available',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_condition (item_condition),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lostandfound_matches
-- Optional: Track potential matches between lost and found items
CREATE TABLE IF NOT EXISTS lostandfound_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lost_item_id INT NOT NULL,
    found_item_id INT NOT NULL,
    match_score DECIMAL(5,2) DEFAULT 0.00,
    match_status ENUM('Potential', 'Confirmed', 'Dismissed') DEFAULT 'Potential',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lost_item_id) REFERENCES lost_items(id) ON DELETE CASCADE,
    FOREIGN KEY (found_item_id) REFERENCES found_items(id) ON DELETE CASCADE,
    INDEX idx_lost_item (lost_item_id),
    INDEX idx_found_item (found_item_id),
    INDEX idx_status (match_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lostandfound_images
-- Stores images for both lost and found items
CREATE TABLE IF NOT EXISTS lostandfound_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_type ENUM('lost', 'found') NOT NULL,
    item_id INT NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    file_size INT NULL,
    mime_type VARCHAR(50) NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_item (item_type, item_id),
    INDEX idx_main (is_main)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SECTION 2: STORED PROCEDURES
-- ============================================

DELIMITER $$

-- ============================================
-- PROCEDURE: Submit Lost Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_submit_lost_item$$
CREATE PROCEDURE sp_submit_lost_item(
    IN p_user_id BIGINT UNSIGNED,
    IN p_item_name VARCHAR(255),
    IN p_category VARCHAR(50),
    IN p_description TEXT,
    IN p_last_known_location VARCHAR(255),
    IN p_specific_area VARCHAR(255),
    IN p_date_time_lost DATETIME,
    IN p_mobile VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_alt_contact VARCHAR(255),
    IN p_contact_details VARCHAR(255),
    IN p_severity_level ENUM('General', 'Important', 'Critical'),
    IN p_reward_offered BOOLEAN,
    IN p_reward_amount DECIMAL(10,2),
    IN p_reward_details TEXT,
    IN p_special_instructions TEXT,
    OUT p_lost_item_id INT
)
BEGIN
    DECLARE v_noc_notified BOOLEAN DEFAULT FALSE;
    
    -- Set NOC notification flag for Critical items
    IF p_severity_level = 'Critical' THEN
        SET v_noc_notified = TRUE;
    END IF;
    
    -- Insert the lost item
    INSERT INTO lost_items (
        user_id, item_name, category, description, last_known_location, specific_area,
        date_time_lost, mobile, email, alt_contact, contact_details, severity_level, 
        status, noc_notified, reward_offered, reward_amount, reward_details, special_instructions
    ) VALUES (
        p_user_id, p_item_name, p_category, p_description, p_last_known_location, p_specific_area,
        p_date_time_lost, p_mobile, p_email, p_alt_contact, p_contact_details, p_severity_level,
        'Still Missing', v_noc_notified, p_reward_offered, p_reward_amount, p_reward_details, p_special_instructions
    );
    
    SET p_lost_item_id = LAST_INSERT_ID();
END$$

-- ============================================
-- PROCEDURE: Submit Found Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_submit_found_item$$
CREATE PROCEDURE sp_submit_found_item(
    IN p_user_id BIGINT UNSIGNED,
    IN p_item_name VARCHAR(255),
    IN p_category VARCHAR(50),
    IN p_description TEXT,
    IN p_found_location VARCHAR(255),
    IN p_specific_area VARCHAR(255),
    IN p_date_time_found DATETIME,
    IN p_mobile VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_alt_contact VARCHAR(255),
    IN p_contact_details VARCHAR(255),
    IN p_item_condition ENUM('Excellent', 'Good', 'Fair', 'Poor'),
    IN p_current_location VARCHAR(255),
    IN p_special_instructions TEXT,
    OUT p_found_item_id INT
)
BEGIN
    -- Insert the found item
    INSERT INTO found_items (
        user_id, item_name, category, description, found_location, specific_area,
        date_time_found, mobile, email, alt_contact, contact_details, 
        item_condition, current_location, special_instructions, reported_to_union, status
    ) VALUES (
        p_user_id, p_item_name, p_category, p_description, p_found_location, p_specific_area,
        p_date_time_found, p_mobile, p_email, p_alt_contact, p_contact_details,
        p_item_condition, p_current_location, p_special_instructions, TRUE, 'Available'
    );
    
    SET p_found_item_id = LAST_INSERT_ID();
END$$

-- ============================================
-- PROCEDURE: Get All Lost Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_all_lost_items$$
CREATE PROCEDURE sp_get_all_lost_items()
BEGIN
    SELECT 
        li.id,
        li.item_name,
        li.description,
        li.last_known_location,
        li.date_time_lost,
        li.contact_details,
        li.severity_level,
        li.status,
        li.created_at,
        li.updated_at,
        u.id as user_id,
        u.username,
        u.email
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    ORDER BY li.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Get All Found Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_all_found_items$$
CREATE PROCEDURE sp_get_all_found_items()
BEGIN
    SELECT 
        fi.id,
        fi.item_name,
        fi.description,
        fi.found_location,
        fi.date_time_found,
        fi.contact_details,
        fi.status,
        fi.admin_notes,
        fi.created_at,
        fi.updated_at,
        u.id as user_id,
        u.first_name,
        u.last_name,
        u.email
    FROM found_items fi
    INNER JOIN users u ON fi.user_id = u.id
    ORDER BY fi.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Get User's Lost Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_user_lost_items$$
CREATE PROCEDURE sp_get_user_lost_items(IN p_user_id BIGINT UNSIGNED)
BEGIN
    SELECT 
        id, item_name, description, last_known_location,
        date_time_lost, contact_details, severity_level,
        status, noc_notified, created_at, updated_at
    FROM lost_items
    WHERE user_id = p_user_id
    ORDER BY created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Get User's Found Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_user_found_items$$
CREATE PROCEDURE sp_get_user_found_items(IN p_user_id BIGINT UNSIGNED)
BEGIN
    SELECT 
        id, item_name, description, found_location,
        date_time_found, contact_details, status,
        admin_notes, created_at, updated_at
    FROM found_items
    WHERE user_id = p_user_id
    ORDER BY created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Update Lost Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_update_lost_item$$
CREATE PROCEDURE sp_update_lost_item(
    IN p_item_id INT,
    IN p_user_id BIGINT UNSIGNED,
    IN p_item_name VARCHAR(255),
    IN p_category VARCHAR(50),
    IN p_description TEXT,
    IN p_last_known_location VARCHAR(255),
    IN p_specific_area VARCHAR(255),
    IN p_date_time_lost DATETIME,
    IN p_mobile VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_alt_contact VARCHAR(255),
    IN p_contact_details VARCHAR(255),
    IN p_severity_level ENUM('General', 'Important', 'Critical'),
    IN p_reward_offered BOOLEAN,
    IN p_reward_amount DECIMAL(10,2),
    IN p_reward_details TEXT,
    IN p_special_instructions TEXT
)
BEGIN
    UPDATE lost_items
    SET 
        item_name = p_item_name,
        category = p_category,
        description = p_description,
        last_known_location = p_last_known_location,
        specific_area = p_specific_area,
        date_time_lost = p_date_time_lost,
        mobile = p_mobile,
        email = p_email,
        alt_contact = p_alt_contact,
        contact_details = p_contact_details,
        severity_level = p_severity_level,
        reward_offered = p_reward_offered,
        reward_amount = p_reward_amount,
        reward_details = p_reward_details,
        special_instructions = p_special_instructions,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_item_id AND user_id = p_user_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ============================================
-- PROCEDURE: Update Lost Item Status
-- ============================================
DROP PROCEDURE IF EXISTS sp_update_lost_item_status$$
CREATE PROCEDURE sp_update_lost_item_status(
    IN p_item_id INT,
    IN p_user_id BIGINT UNSIGNED,
    IN p_status ENUM('Still Missing', 'Returned')
)
BEGIN
    UPDATE lost_items
    SET 
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_item_id AND user_id = p_user_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ============================================
-- PROCEDURE: Update Found Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_update_found_item$$
CREATE PROCEDURE sp_update_found_item(
    IN p_item_id INT,
    IN p_user_id BIGINT UNSIGNED,
    IN p_item_name VARCHAR(255),
    IN p_category VARCHAR(50),
    IN p_description TEXT,
    IN p_found_location VARCHAR(255),
    IN p_specific_area VARCHAR(255),
    IN p_date_time_found DATETIME,
    IN p_mobile VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_alt_contact VARCHAR(255),
    IN p_contact_details VARCHAR(255),
    IN p_item_condition ENUM('Excellent', 'Good', 'Fair', 'Poor'),
    IN p_current_location VARCHAR(255),
    IN p_special_instructions TEXT
)
BEGIN
    UPDATE found_items
    SET 
        item_name = p_item_name,
        category = p_category,
        description = p_description,
        found_location = p_found_location,
        specific_area = p_specific_area,
        date_time_found = p_date_time_found,
        mobile = p_mobile,
        email = p_email,
        alt_contact = p_alt_contact,
        contact_details = p_contact_details,
        item_condition = p_item_condition,
        current_location = p_current_location,
        special_instructions = p_special_instructions,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_item_id AND user_id = p_user_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ============================================
-- PROCEDURE: Delete Lost Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_delete_lost_item$$
CREATE PROCEDURE sp_delete_lost_item(
    IN p_item_id INT,
    IN p_user_id BIGINT UNSIGNED
)
BEGIN
    DELETE FROM lost_items
    WHERE id = p_item_id AND user_id = p_user_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ============================================
-- PROCEDURE: Delete Found Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_delete_found_item$$
CREATE PROCEDURE sp_delete_found_item(
    IN p_item_id INT,
    IN p_user_id BIGINT UNSIGNED
)
BEGIN
    DELETE FROM found_items
    WHERE id = p_item_id AND user_id = p_user_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ============================================
-- PROCEDURE: Filter Lost Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_filter_lost_items$$
CREATE PROCEDURE sp_filter_lost_items(
    IN p_severity_level VARCHAR(50),
    IN p_status VARCHAR(50),
    IN p_location VARCHAR(255),
    IN p_date_from DATE,
    IN p_date_to DATE
)
BEGIN
    SELECT 
        li.id,
        li.item_name,
        li.description,
        li.last_known_location as location,
        li.date_time_lost,
        li.contact_details,
        li.severity_level,
        li.status,
        li.created_at,
        u.first_name,
        u.last_name,
        u.email,
        'lost' as item_type
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    WHERE 1=1
        AND (p_severity_level IS NULL OR p_severity_level = '' OR li.severity_level = p_severity_level)
        AND (p_status IS NULL OR p_status = '' OR li.status = p_status)
        AND (p_location IS NULL OR p_location = '' OR li.last_known_location LIKE CONCAT('%', p_location, '%'))
        AND (p_date_from IS NULL OR DATE(li.date_time_lost) >= p_date_from)
        AND (p_date_to IS NULL OR DATE(li.date_time_lost) <= p_date_to)
    ORDER BY li.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Filter Found Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_filter_found_items$$
CREATE PROCEDURE sp_filter_found_items(
    IN p_status VARCHAR(50),
    IN p_location VARCHAR(255),
    IN p_date_from DATE,
    IN p_date_to DATE
)
BEGIN
    SELECT 
        fi.id,
        fi.item_name,
        fi.description,
        fi.found_location as location,
        fi.date_time_found,
        fi.contact_details,
        fi.status,
        fi.admin_notes,
        fi.created_at,
        u.first_name,
        u.last_name,
        u.email,
        'found' as item_type
    FROM found_items fi
    INNER JOIN users u ON fi.user_id = u.id
    WHERE 1=1
        AND (p_status IS NULL OR p_status = '' OR fi.status = p_status)
        AND (p_location IS NULL OR p_location = '' OR fi.found_location LIKE CONCAT('%', p_location, '%'))
        AND (p_date_from IS NULL OR DATE(fi.date_time_found) >= p_date_from)
        AND (p_date_to IS NULL OR DATE(fi.date_time_found) <= p_date_to)
    ORDER BY fi.created_at DESC;
END$$

-- ============================================
-- ADMIN PROCEDURES
-- ============================================

-- ============================================
-- PROCEDURE: Admin Update Lost Item Status
-- ============================================
DROP PROCEDURE IF EXISTS sp_admin_update_lost_item_status$$
CREATE PROCEDURE sp_admin_update_lost_item_status(
    IN p_item_id INT,
    IN p_status ENUM('Still Missing', 'Returned')
)
BEGIN
    UPDATE lost_items
    SET 
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_item_id;
    
    SELECT * FROM lost_items WHERE id = p_item_id;
END$$

-- ============================================
-- PROCEDURE: Admin Update Found Item Status
-- ============================================
DROP PROCEDURE IF EXISTS sp_admin_update_found_item_status$$
CREATE PROCEDURE sp_admin_update_found_item_status(
    IN p_item_id INT,
    IN p_status ENUM('Available', 'Collected', 'Returned to Owner'),
    IN p_admin_notes TEXT
)
BEGIN
    UPDATE found_items
    SET 
        status = p_status,
        admin_notes = p_admin_notes,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_item_id;
    
    SELECT * FROM found_items WHERE id = p_item_id;
END$$

-- ============================================
-- PROCEDURE: Admin Create Lost Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_admin_create_lost_item$$
CREATE PROCEDURE sp_admin_create_lost_item(
    IN p_user_id BIGINT UNSIGNED,
    IN p_item_name VARCHAR(255),
    IN p_category VARCHAR(50),
    IN p_description TEXT,
    IN p_last_known_location VARCHAR(255),
    IN p_specific_area VARCHAR(255),
    IN p_date_time_lost DATETIME,
    IN p_mobile VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_alt_contact VARCHAR(255),
    IN p_contact_details VARCHAR(255),
    IN p_severity_level ENUM('General', 'Important', 'Critical'),
    IN p_status ENUM('Still Missing', 'Returned'),
    IN p_reward_offered BOOLEAN,
    IN p_reward_amount DECIMAL(10,2),
    IN p_reward_details TEXT,
    IN p_special_instructions TEXT,
    OUT p_lost_item_id INT
)
BEGIN
    INSERT INTO lost_items (
        user_id, item_name, category, description, last_known_location, specific_area,
        date_time_lost, mobile, email, alt_contact, contact_details, severity_level, 
        status, reward_offered, reward_amount, reward_details, special_instructions
    ) VALUES (
        p_user_id, p_item_name, p_category, p_description, p_last_known_location, p_specific_area,
        p_date_time_lost, p_mobile, p_email, p_alt_contact, p_contact_details, p_severity_level,
        p_status, p_reward_offered, p_reward_amount, p_reward_details, p_special_instructions
    );
    
    SET p_lost_item_id = LAST_INSERT_ID();
END$$

-- ============================================
-- PROCEDURE: Admin Create Found Item
-- ============================================
DROP PROCEDURE IF EXISTS sp_admin_create_found_item$$
CREATE PROCEDURE sp_admin_create_found_item(
    IN p_user_id BIGINT UNSIGNED,
    IN p_item_name VARCHAR(255),
    IN p_category VARCHAR(50),
    IN p_description TEXT,
    IN p_found_location VARCHAR(255),
    IN p_specific_area VARCHAR(255),
    IN p_date_time_found DATETIME,
    IN p_mobile VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_alt_contact VARCHAR(255),
    IN p_contact_details VARCHAR(255),
    IN p_item_condition ENUM('Excellent', 'Good', 'Fair', 'Poor'),
    IN p_current_location VARCHAR(255),
    IN p_special_instructions TEXT,
    IN p_status ENUM('Available', 'Collected', 'Returned to Owner'),
    IN p_admin_notes TEXT,
    OUT p_found_item_id INT
)
BEGIN
    INSERT INTO found_items (
        user_id, item_name, category, description, found_location, specific_area,
        date_time_found, mobile, email, alt_contact, contact_details,
        item_condition, current_location, special_instructions, status, admin_notes
    ) VALUES (
        p_user_id, p_item_name, p_category, p_description, p_found_location, p_specific_area,
        p_date_time_found, p_mobile, p_email, p_alt_contact, p_contact_details,
        p_item_condition, p_current_location, p_special_instructions, p_status, p_admin_notes
    );
    
    SET p_found_item_id = LAST_INSERT_ID();
END$$

-- ============================================
-- PROCEDURE: Get Critical Items (NOC Coordination)
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_critical_items$$
CREATE PROCEDURE sp_get_critical_items()
BEGIN
    SELECT 
        li.id,
        li.item_name,
        li.description,
        li.last_known_location,
        li.date_time_lost,
        li.contact_details,
        li.severity_level,
        li.status,
        li.noc_notified,
        li.created_at,
        li.updated_at,
        u.id as user_id,
        u.first_name,
        u.last_name,
        u.email
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    WHERE li.severity_level IN ('Critical', 'Important')
      AND li.status = 'Still Missing'
    ORDER BY 
        FIELD(li.severity_level, 'Critical', 'Important'),
        li.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Get Item Details
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_lost_item_details$$
CREATE PROCEDURE sp_get_lost_item_details(IN p_item_id INT)
BEGIN
    SELECT 
        li.*,
        u.first_name,
        u.last_name,
        u.email,
        u.reg_no
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    WHERE li.id = p_item_id;
END$$

DROP PROCEDURE IF EXISTS sp_get_found_item_details$$
CREATE PROCEDURE sp_get_found_item_details(IN p_item_id INT)
BEGIN
    SELECT 
        fi.*,
        u.first_name,
        u.last_name,
        u.email,
        u.reg_no
    FROM found_items fi
    INNER JOIN users u ON fi.user_id = u.id
    WHERE fi.id = p_item_id;
END$$

-- ============================================
-- PROCEDURE: Search Items
-- ============================================
DROP PROCEDURE IF EXISTS sp_search_items$$
CREATE PROCEDURE sp_search_items(IN p_search_term VARCHAR(255))
BEGIN
    -- Search in both lost and found items
    SELECT 
        id,
        item_name,
        description,
        last_known_location as location,
        date_time_lost as date_time,
        severity_level,
        status,
        created_at,
        'lost' as item_type
    FROM lost_items
    WHERE item_name LIKE CONCAT('%', p_search_term, '%')
       OR description LIKE CONCAT('%', p_search_term, '%')
       OR last_known_location LIKE CONCAT('%', p_search_term, '%')
    
    UNION ALL
    
    SELECT 
        id,
        item_name,
        description,
        found_location as location,
        date_time_found as date_time,
        NULL as severity_level,
        status,
        created_at,
        'found' as item_type
    FROM found_items
    WHERE item_name LIKE CONCAT('%', p_search_term, '%')
       OR description LIKE CONCAT('%', p_search_term, '%')
       OR found_location LIKE CONCAT('%', p_search_term, '%')
    
    ORDER BY created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Add Image
-- ============================================
DROP PROCEDURE IF EXISTS sp_add_item_image$$
CREATE PROCEDURE sp_add_item_image(
    IN p_item_type ENUM('lost', 'found'),
    IN p_item_id INT,
    IN p_image_path VARCHAR(500),
    IN p_image_name VARCHAR(255),
    IN p_is_main BOOLEAN,
    IN p_file_size INT,
    IN p_mime_type VARCHAR(50),
    OUT p_image_id INT
)
BEGIN
    -- If this is marked as main, unmark all other images for this item
    IF p_is_main = TRUE THEN
        UPDATE lostandfound_images
        SET is_main = FALSE
        WHERE item_type = p_item_type AND item_id = p_item_id;
    END IF;
    
    -- Insert the new image
    INSERT INTO lostandfound_images (
        item_type, item_id, image_path, image_name, is_main, file_size, mime_type
    ) VALUES (
        p_item_type, p_item_id, p_image_path, p_image_name, p_is_main, p_file_size, p_mime_type
    );
    
    SET p_image_id = LAST_INSERT_ID();
END$$

-- ============================================
-- PROCEDURE: Get Item Images
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_item_images$$
CREATE PROCEDURE sp_get_item_images(
    IN p_item_type ENUM('lost', 'found'),
    IN p_item_id INT
)
BEGIN
    SELECT 
        id, image_path, image_name, is_main, file_size, mime_type, uploaded_at
    FROM lostandfound_images
    WHERE item_type = p_item_type AND item_id = p_item_id
    ORDER BY is_main DESC, uploaded_at ASC;
END$$

-- ============================================
-- PROCEDURE: Delete Image
-- ============================================
DROP PROCEDURE IF EXISTS sp_delete_item_image$$
CREATE PROCEDURE sp_delete_item_image(
    IN p_image_id INT,
    IN p_item_type ENUM('lost', 'found'),
    IN p_item_id INT
)
BEGIN
    DELETE FROM lostandfound_images
    WHERE id = p_image_id 
      AND item_type = p_item_type 
      AND item_id = p_item_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ============================================
-- PROCEDURE: Filter Lost Items by Category
-- ============================================
DROP PROCEDURE IF EXISTS sp_filter_lost_items_by_category$$
CREATE PROCEDURE sp_filter_lost_items_by_category(IN p_category VARCHAR(50))
BEGIN
    SELECT 
        li.*,
        u.first_name,
        u.last_name,
        u.email as user_email
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    WHERE li.category = p_category
    ORDER BY li.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Filter Found Items by Category
-- ============================================
DROP PROCEDURE IF EXISTS sp_filter_found_items_by_category$$
CREATE PROCEDURE sp_filter_found_items_by_category(IN p_category VARCHAR(50))
BEGIN
    SELECT 
        fi.*,
        u.first_name,
        u.last_name,
        u.email as user_email
    FROM found_items fi
    INNER JOIN users u ON fi.user_id = u.id
    WHERE fi.category = p_category
    ORDER BY fi.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Get Items with Rewards
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_items_with_rewards$$
CREATE PROCEDURE sp_get_items_with_rewards()
BEGIN
    SELECT 
        li.*,
        u.first_name,
        u.last_name,
        u.email as user_email
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    WHERE li.reward_offered = TRUE
      AND li.status = 'Still Missing'
    ORDER BY li.reward_amount DESC, li.created_at DESC;
END$$

-- ============================================
-- PROCEDURE: Get Lost Item Details with Images
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_lost_item_with_images$$
CREATE PROCEDURE sp_get_lost_item_with_images(IN p_item_id INT)
BEGIN
    -- Get item details
    SELECT 
        li.*,
        u.first_name,
        u.last_name,
        u.email as user_email,
        u.reg_no
    FROM lost_items li
    INNER JOIN users u ON li.user_id = u.id
    WHERE li.id = p_item_id;
    
    -- Get images
    SELECT 
        id, image_path, image_name, is_main, uploaded_at
    FROM lostandfound_images
    WHERE item_type = 'lost' AND item_id = p_item_id
    ORDER BY is_main DESC, uploaded_at ASC;
END$$

-- ============================================
-- PROCEDURE: Get Found Item Details with Images
-- ============================================
DROP PROCEDURE IF EXISTS sp_get_found_item_with_images$$
CREATE PROCEDURE sp_get_found_item_with_images(IN p_item_id INT)
BEGIN
    -- Get item details
    SELECT 
        fi.*,
        u.first_name,
        u.last_name,
        u.email as user_email,
        u.reg_no
    FROM found_items fi
    INNER JOIN users u ON fi.user_id = u.id
    WHERE fi.id = p_item_id;
    
    -- Get images
    SELECT 
        id, image_path, image_name, is_main, uploaded_at
    FROM lostandfound_images
    WHERE item_type = 'found' AND item_id = p_item_id
    ORDER BY is_main DESC, uploaded_at ASC;
END$$

DELIMITER ;

-- ============================================
-- SECTION 3: SAMPLE DATA (Optional - for testing)
-- ============================================
-- Uncomment the following lines to insert sample data

/*
-- Sample Lost Items
INSERT INTO lost_items (user_id, item_name, description, last_known_location, date_time_lost, contact_details, severity_level, status) VALUES
(1, 'Blue Backpack', 'Blue backpack with laptop inside', 'Library 3rd Floor', '2026-02-09 14:30:00', 'john@ucsc.edu', 'Critical', 'Still Missing'),
(2, 'Student ID Card', 'Student ID card with photo', 'Cafeteria', '2026-02-08 12:00:00', 'jane@ucsc.edu', 'Important', 'Still Missing'),
(1, 'Black Umbrella', 'Plain black umbrella', 'Lecture Hall A', '2026-02-07 10:15:00', 'john@ucsc.edu', 'General', 'Still Missing');

-- Sample Found Items
INSERT INTO found_items (user_id, item_name, description, found_location, date_time_found, contact_details, status) VALUES
(2, 'Red Water Bottle', 'Red metal water bottle with stickers', 'Sports Complex', '2026-02-09 16:00:00', 'jane@ucsc.edu', 'Available'),
(3, 'Keys with Keychain', 'Set of keys with anime keychain', 'Parking Lot B', '2026-02-08 18:30:00', 'bob@ucsc.edu', 'Available');
*/

-- ============================================
-- SETUP COMPLETE
-- ============================================
-- All tables and stored procedures have been created
-- You can now use the Lost and Found system
-- ============================================
