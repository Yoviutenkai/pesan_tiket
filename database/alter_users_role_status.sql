ALTER TABLE users
  MODIFY role ENUM('admin','petugas','user') DEFAULT 'user',
  MODIFY status_akun ENUM('aktif','nonaktif') DEFAULT 'aktif';
