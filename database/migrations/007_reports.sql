USE e_brgy_db;

CREATE TABLE IF NOT EXISTS reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  report_type VARCHAR(40) NOT NULL,
  title VARCHAR(160) NOT NULL,
  report_year SMALLINT UNSIGNED NOT NULL,
  barangay_name VARCHAR(120) NOT NULL,
  municipality VARCHAR(120) NOT NULL,
  chairman_name VARCHAR(150) NOT NULL,
  generated_by BIGINT UNSIGNED NULL,
  generated_at DATETIME NOT NULL,
  data LONGTEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reports_year (report_year),
  KEY idx_reports_type_year (report_type, report_year),
  KEY idx_reports_generated_by (generated_by),
  CONSTRAINT fk_reports_generated_by
    FOREIGN KEY (generated_by) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
