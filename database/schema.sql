CREATE DATABASE db_event_ticketing;
USE db_event_ticketing;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(100) UNIQUE,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    foto_profile VARCHAR(255) DEFAULT 'default.png',
    no_hp VARCHAR(20),
    alamat TEXT,
    role ENUM('admin','user') DEFAULT 'user',
    status_akun ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE venue (
    id_venue INT AUTO_INCREMENT PRIMARY KEY,
    nama_venue VARCHAR(150) NOT NULL,
    alamat TEXT,
    kota VARCHAR(100),
    kapasitas INT,
    gambar_venue VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event (
    id_event INT AUTO_INCREMENT PRIMARY KEY,
    id_venue INT,
    nama_event VARCHAR(150) NOT NULL,
    kategori VARCHAR(100),
    deskripsi TEXT,
    organizer VARCHAR(100),
    tanggal_mulai DATETIME,
    tanggal_selesai DATETIME,
    banner_event VARCHAR(255),
    status_event ENUM('upcoming','ongoing','finished','cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_venue) REFERENCES venue(id_venue) ON DELETE CASCADE
);

CREATE TABLE tiket (
    id_tiket INT AUTO_INCREMENT PRIMARY KEY,
    id_event INT,
    nama_tiket VARCHAR(100),
    deskripsi_tiket TEXT,
    harga DECIMAL(12,2),
    kuota INT,
    tiket_terjual INT DEFAULT 0,
    benefit TEXT,
    status_tiket ENUM('available','sold_out') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_event) REFERENCES event(id_event) ON DELETE CASCADE
);

CREATE TABLE voucher (
    id_voucher INT AUTO_INCREMENT PRIMARY KEY,
    kode_voucher VARCHAR(50) UNIQUE,
    jenis_diskon ENUM('persen','nominal'),
    nilai_diskon DECIMAL(12,2),
    minimum_transaksi DECIMAL(12,2),
    maksimal_diskon DECIMAL(12,2),
    tanggal_mulai DATE,
    tanggal_expired DATE,
    kuota_voucher INT DEFAULT 0,
    voucher_digunakan INT DEFAULT 0,
    status_voucher ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id_order INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    kode_order VARCHAR(100) UNIQUE,
    subtotal DECIMAL(12,2),
    diskon DECIMAL(12,2) DEFAULT 0,
    biaya_admin DECIMAL(12,2) DEFAULT 0,
    total_bayar DECIMAL(12,2),
    metode_pembayaran VARCHAR(100),
    status_pembayaran ENUM('pending','paid','cancelled','expired') DEFAULT 'pending',
    status_order ENUM('menunggu','diproses','selesai') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);

CREATE TABLE order_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT,
    id_tiket INT,
    qty INT,
    harga DECIMAL(12,2),
    subtotal DECIMAL(12,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_order) REFERENCES orders(id_order) ON DELETE CASCADE,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id_tiket) ON DELETE CASCADE
);

CREATE TABLE attendee (
    id_attendee INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT,
    kode_tiket VARCHAR(100) UNIQUE,
    nama_pengunjung VARCHAR(100),
    email_pengunjung VARCHAR(100),
    no_hp VARCHAR(20),
    status_checkin ENUM('belum_hadir','sudah_hadir') DEFAULT 'belum_hadir',
    waktu_checkin DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_order) REFERENCES orders(id_order) ON DELETE CASCADE
);

CREATE TABLE payments (
    id_payment INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT,
    bukti_transfer VARCHAR(255),
    status_verifikasi ENUM('pending','approved','rejected') DEFAULT 'pending',
    catatan_admin TEXT,
    verified_by INT NULL,
    verified_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_order) REFERENCES orders(id_order) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id_user) ON DELETE SET NULL
);

CREATE TABLE event_gallery (
    id_gallery INT AUTO_INCREMENT PRIMARY KEY,
    id_event INT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_event) REFERENCES event(id_event) ON DELETE CASCADE
);

CREATE TABLE testimonial (
    id_testimonial INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    isi_testimonial TEXT,
    rating INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);
