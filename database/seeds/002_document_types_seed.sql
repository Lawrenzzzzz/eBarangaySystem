USE e_brgy_db;

INSERT IGNORE INTO document_types (code, name, description, fee, processing_days, sort_order)
VALUES
  ('clearance', 'Barangay Clearance', 'Proof of good standing, usually required for work and permits.', 50.00, 2, 1),
  ('indigency', 'Certificate of Indigency', 'Confirms your household qualifies for free assistance and fee waivers.', 0.00, 1, 2),
  ('residency', 'Certificate of Residency', 'Confirms that you live in the barangay.', 30.00, 0, 3);
