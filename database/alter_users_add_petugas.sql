ALTER TABLE users
  MODIFY role ENUM('admin','petugas','user') DEFAULT 'user';
