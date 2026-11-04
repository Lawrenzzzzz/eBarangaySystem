CREATE DATABASE IF NOT EXISTS e_brgy_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE e_brgy_db;

CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(40) NOT NULL,
  name VARCHAR(80) NOT NULL,
  description VARCHAR(255) NULL,
  is_system TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_code (code),
  UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id BIGINT UNSIGNED NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(20) NULL,
  password_hash VARCHAR(255) NOT NULL,
  account_type ENUM('resident', 'official') NOT NULL DEFAULT 'resident',
  status ENUM('pending', 'active', 'suspended', 'deactivated') NOT NULL DEFAULT 'pending',
  email_verified_at DATETIME NULL,
  phone_verified_at DATETIME NULL,
  last_login_at DATETIME NULL,
  password_changed_at DATETIME NULL,
  failed_login_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_phone (phone),
  KEY idx_users_role (role_id),
  KEY idx_users_account_type (account_type),
  KEY idx_users_locked_until (locked_until),
  KEY idx_users_deleted_at (deleted_at),
  CONSTRAINT fk_users_role
    FOREIGN KEY (role_id) REFERENCES roles (id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS residents (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  resident_code VARCHAR(30) NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  first_name VARCHAR(80) NULL,
  middle_name VARCHAR(80) NULL,
  last_name VARCHAR(80) NULL,
  suffix VARCHAR(20) NULL,
  household_number VARCHAR(40) NULL,
  address_line VARCHAR(255) NOT NULL,
  birth_date DATE NULL,
  sex ENUM('male', 'female', 'other') NULL,
  civil_status ENUM('single', 'married', 'widowed', 'separated', 'annulled') NULL,
  profile_completed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_residents_user (user_id),
  UNIQUE KEY uq_residents_code (resident_code),
  CONSTRAINT fk_residents_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS official_applications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  requested_position VARCHAR(80) NOT NULL,
  employee_number VARCHAR(40) NOT NULL,
  status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
  remarks VARCHAR(255) NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_official_applications_user_status (user_id, status),
  KEY idx_official_applications_reviewed_by (reviewed_by),
  CONSTRAINT fk_official_applications_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_official_applications_reviewed_by
    FOREIGN KEY (reviewed_by) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS announcements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  created_by BIGINT UNSIGNED NOT NULL,
  type ENUM('health', 'education', 'infrastructure', 'safety', 'notice') NOT NULL,
  title VARCHAR(160) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_announcements_type (type),
  KEY idx_announcements_created (created_at),
  KEY idx_announcements_created_by (created_by),
  CONSTRAINT fk_announcements_created_by
    FOREIGN KEY (created_by) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  document_request_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  received_by BIGINT UNSIGNED NULL,
  payee VARCHAR(150) NOT NULL,
  payment_method ENUM('cash', 'online') NOT NULL DEFAULT 'cash',
  reference_no VARCHAR(80) NOT NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('received', 'pending', 'void') NOT NULL DEFAULT 'received',
  paid_at DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_payments_reference (reference_no),
  KEY idx_payments_request (document_request_id),
  KEY idx_payments_user (user_id),
  KEY idx_payments_received_by (received_by),
  KEY idx_payments_paid_at (paid_at),
  CONSTRAINT fk_payments_request
    FOREIGN KEY (document_request_id) REFERENCES document_requests (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_payments_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_payments_received_by
    FOREIGN KEY (received_by) REFERENCES users (id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS auth_tokens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  type ENUM('refresh', 'email_verification', 'password_reset', 'phone_verification') NOT NULL,
  destination VARCHAR(190) NULL,
  token_hash VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  revoked_at DATETIME NULL,
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  requested_ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  metadata JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_auth_tokens_hash (token_hash),
  KEY idx_auth_tokens_user_type (user_id, type),
  KEY idx_auth_tokens_destination_type (destination, type),
  KEY idx_auth_tokens_expires (expires_at),
  KEY idx_auth_tokens_type_used (type, used_at),
  CONSTRAINT fk_auth_tokens_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW v_user_accounts AS
SELECT
  u.id AS user_id,
  u.email,
  u.phone,
  u.account_type,
  u.status AS account_status,
  u.email_verified_at,
  u.phone_verified_at,
  u.last_login_at,
  r.code AS role_code,
  r.name AS role_name,
  res.id AS resident_id,
  res.resident_code,
  res.full_name,
  res.household_number,
  res.address_line,
  oa.id AS official_application_id,
  oa.requested_position,
  oa.employee_number,
  oa.status AS official_application_status,
  oa.created_at AS official_application_created_at
FROM users u
JOIN roles r ON r.id = u.role_id
LEFT JOIN residents res ON res.user_id = u.id
LEFT JOIN (
  SELECT application.*
  FROM official_applications application
  JOIN (
    SELECT user_id, MAX(id) AS id
    FROM official_applications
    GROUP BY user_id
  ) latest ON latest.id = application.id
) oa ON oa.user_id = u.id
WHERE u.deleted_at IS NULL;
