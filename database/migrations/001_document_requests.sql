USE e_brgy_db;

CREATE TABLE IF NOT EXISTS document_types (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(40) NOT NULL,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(255) NOT NULL,
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

CREATE TABLE IF NOT EXISTS document_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  reference_no VARCHAR(30) NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  resident_id BIGINT UNSIGNED NOT NULL,
  document_type_id BIGINT UNSIGNED NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  resident_code VARCHAR(30) NOT NULL,
  address_line VARCHAR(255) NOT NULL,
  household_number VARCHAR(40) NULL,
  purpose VARCHAR(300) NOT NULL,
  fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('pending', 'processing', 'for_payment', 'ready', 'released', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
  remarks VARCHAR(255) NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  released_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_document_requests_reference (reference_no),
  KEY idx_document_requests_user_status (user_id, status),
  KEY idx_document_requests_resident (resident_id),
  KEY idx_document_requests_type (document_type_id),
  KEY idx_document_requests_created (created_at),
  KEY idx_document_requests_reviewed_by (reviewed_by),
  CONSTRAINT fk_document_requests_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_document_requests_resident
    FOREIGN KEY (resident_id) REFERENCES residents (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_document_requests_type
    FOREIGN KEY (document_type_id) REFERENCES document_types (id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_document_requests_reviewed_by
    FOREIGN KEY (reviewed_by) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
