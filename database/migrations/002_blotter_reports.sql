USE e_brgy_db;

CREATE TABLE IF NOT EXISTS blotter_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  reference_no VARCHAR(30) NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  resident_id BIGINT UNSIGNED NOT NULL,
  filed_by VARCHAR(150) NOT NULL,
  address_line VARCHAR(255) NOT NULL,
  subject VARCHAR(160) NOT NULL,
  details TEXT NOT NULL,
  status ENUM('pending', 'under_review', 'scheduled', 'resolved', 'dismissed', 'referred', 'cancelled') NOT NULL DEFAULT 'pending',
  remarks VARCHAR(255) NULL,
  resolution TEXT NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  resolved_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blotter_reports_reference (reference_no),
  KEY idx_blotter_reports_user_status (user_id, status),
  KEY idx_blotter_reports_resident (resident_id),
  KEY idx_blotter_reports_created (created_at),
  KEY idx_blotter_reports_reviewed_by (reviewed_by),
  CONSTRAINT fk_blotter_reports_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_blotter_reports_resident
    FOREIGN KEY (resident_id) REFERENCES residents (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_blotter_reports_reviewed_by
    FOREIGN KEY (reviewed_by) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
