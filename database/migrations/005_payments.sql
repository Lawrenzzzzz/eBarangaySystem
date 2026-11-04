USE e_brgy_db;

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
