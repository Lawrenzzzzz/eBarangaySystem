CREATE TABLE IF NOT EXISTS barangay_settings (
    id TINYINT UNSIGNED NOT NULL,
    barangay_name VARCHAR(150) NOT NULL DEFAULT '',
    municipality VARCHAR(150) NOT NULL DEFAULT '',
    province VARCHAR(150) NOT NULL DEFAULT '',
    chairman VARCHAR(150) NOT NULL DEFAULT '',
    term VARCHAR(60) NOT NULL DEFAULT '',
    puroks SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    sitios SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    contact_number VARCHAR(20) DEFAULT NULL,
    email VARCHAR(190) DEFAULT NULL,
    office_address VARCHAR(255) NOT NULL DEFAULT '',
    updated_by BIGINT UNSIGNED DEFAULT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_barangay_settings_updated_by (updated_by),
    CONSTRAINT fk_barangay_settings_updated_by FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO barangay_settings (id, barangay_name, municipality, province, chairman, term, puroks, sitios)
VALUES (1, 'Barangay Bucana', 'Nasugbu', 'Batangas', 'Capt. Choi Seungcheol Batumbakal', '2022-2025', 7, 8)
ON DUPLICATE KEY UPDATE id = id;
