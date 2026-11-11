CREATE TABLE IF NOT EXISTS attendance_records (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    work_date DATE NOT NULL,
    time_in DATETIME NOT NULL,
    time_out DATETIME NULL,
    scanned_in_by BIGINT UNSIGNED DEFAULT NULL,
    scanned_out_by BIGINT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_attendance_records_user_date (user_id, work_date),
    KEY idx_attendance_records_date (work_date),
    CONSTRAINT fk_attendance_records_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_attendance_records_scanned_in_by FOREIGN KEY (scanned_in_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_attendance_records_scanned_out_by FOREIGN KEY (scanned_out_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
