CREATE TABLE IF NOT EXISTS document_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(40) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    processing_days SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_document_types_code (code),
    KEY idx_document_types_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO document_types (code, name, description, fee, processing_days, is_active, sort_order)
VALUES
    ('clearance', 'Barangay Clearance', 'Barangay clearance certificate for residents.', 50.00, 1, 1, 1),
    ('indigency', 'Certificate of Indigency', 'Certificate issued to indigent residents.', 0.00, 1, 1, 2),
    ('residency', 'Certificate of Residency', 'Proof of residency in the barangay.', 30.00, 1, 1, 3)
ON DUPLICATE KEY UPDATE code = code;
