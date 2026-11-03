USE e_brgy_db;

ALTER TABLE residents
  DROP FOREIGN KEY fk_residents_verified_by,
  DROP COLUMN verification_status,
  DROP COLUMN verified_by,
  DROP COLUMN verified_at;
