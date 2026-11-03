USE e_brgy_db;

ALTER TABLE residents
  ADD COLUMN profile_completed_at DATETIME NULL AFTER civil_status;
