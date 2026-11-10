ALTER TABLE users
    ADD COLUMN full_name VARCHAR(150) NULL AFTER email,
    ADD COLUMN avatar_filename VARCHAR(255) NULL AFTER phone;
