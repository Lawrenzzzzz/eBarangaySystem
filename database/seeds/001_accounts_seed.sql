USE e_brgy_db;

INSERT IGNORE INTO roles (id, code, name, description, is_system)
VALUES
  (1, 'resident', 'Resident / User', 'Registered resident account with access to the resident portal.', 1),
  (2, 'official', 'Barangay Official', 'Barangay official account with access to official functions.', 1),
  (3, 'admin', 'Admin / Barangay Captain', 'Barangay captain or administrator with full access.', 1);

INSERT IGNORE INTO users (
  id, role_id, email, phone, password_hash,
  account_type, status, email_verified_at, phone_verified_at, password_changed_at
)
VALUES
  (1, 1, 'resident@ebarangay.local', '09171234567', '$2y$10$H.DLpjfeITOY6AwiGm/qpuAneIUDWjKOKZdavz8PZwu619hayGDOO', 'resident', 'active', NOW(), NOW(), NOW()),
  (2, 1, 'official@ebarangay.local', '09181234567', '$2y$10$H.DLpjfeITOY6AwiGm/qpuAneIUDWjKOKZdavz8PZwu619hayGDOO', 'official', 'active', NOW(), NOW(), NOW()),
  (3, 2, 'secretary@ebarangay.local', '09191234567', '$2y$10$H.DLpjfeITOY6AwiGm/qpuAneIUDWjKOKZdavz8PZwu619hayGDOO', 'official', 'active', NOW(), NOW(), NOW());

INSERT IGNORE INTO residents (
  id, user_id, resident_code, full_name, first_name, middle_name, last_name, suffix,
  household_number, address_line, verification_status, verified_by, verified_at
)
VALUES
  (1, 1, 'BSG-20417', 'Marco Dela Peña', 'Marco', NULL, 'Dela Peña', NULL, '4-118', 'Household #4-118, Barangay San Isidro', 'verified', 3, NOW()),
  (2, 2, 'BSG-20418', 'Maria Santos', 'Maria', NULL, 'Santos', NULL, NULL, 'Barangay San Isidro', 'verified', 3, NOW());

INSERT IGNORE INTO official_applications (
  id, user_id, requested_position, employee_number, status
)
VALUES (1, 2, 'Kagawad', 'EMP-1001', 'pending');
