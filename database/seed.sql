USE db_event_ticketing;

INSERT INTO users (nama, username, email, password, role) VALUES
('Admin Eventa', 'admin', 'admin@eventa.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Nadia Putri', 'nadia', 'nadia@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

INSERT INTO venue (nama_venue, alamat, kota, kapasitas, gambar_venue) VALUES
('Skyline Arena', 'Jl. Merdeka No. 1', 'Jakarta', 5000, NULL),
('Aurora Hall', 'Jl. Pahlawan No. 12', 'Bandung', 2500, NULL),
('Oceanic Stage', 'Jl. Pantai Selatan No. 88', 'Bali', 4000, NULL);

INSERT INTO event (id_venue, nama_event, kategori, deskripsi, organizer, tanggal_mulai, tanggal_selesai, banner_event, status_event) VALUES
(1, 'Eventa Live Concert', 'Music', 'Konser premium dengan tata cahaya sinematik.', 'Eventa Studio', '2026-06-20 19:00:00', '2026-06-20 22:00:00', NULL, 'upcoming'),
(2, 'Creator Summit 2026', 'Conference', 'Temu kreator dan brand dengan sesi eksklusif.', 'Eventa Network', '2026-07-12 09:00:00', '2026-07-12 17:00:00', NULL, 'upcoming'),
(3, 'Art Week Immersive', 'Exhibition', 'Pameran seni modern dan instalasi interaktif.', 'Eventa Gallery', '2026-08-05 10:00:00', '2026-08-10 21:00:00', NULL, 'upcoming');

INSERT INTO tiket (id_event, nama_tiket, deskripsi_tiket, harga, kuota, tiket_terjual, benefit) VALUES
(1, 'VIP Lounge', 'Akses lounge, meet and greet, seating terbaik.', 950000, 300, 25, 'Lounge, VIP gift, priority entry'),
(1, 'Festival', 'Akses area festival utama.', 350000, 2000, 150, 'Festival entry'),
(2, 'Premium Seat', 'Seat utama dekat panggung.', 650000, 500, 40, 'Premium seat, coffee break'),
(2, 'General', 'Akses reguler.', 250000, 1200, 75, 'General entry'),
(3, 'All Access', 'Semua instalasi dan workshop.', 400000, 800, 60, 'All zones, workshop seat');

INSERT INTO voucher (kode_voucher, jenis_diskon, nilai_diskon, minimum_transaksi, maksimal_diskon, tanggal_mulai, tanggal_expired, kuota_voucher, voucher_digunakan, status_voucher) VALUES
('EVENTA10', 'persen', 10, 200000, 100000, '2026-05-01', '2026-12-31', 100, 5, 'aktif'),
('FEST50K', 'nominal', 50000, 300000, 50000, '2026-05-01', '2026-12-31', 50, 3, 'aktif');

INSERT INTO testimonial (id_user, isi_testimonial, rating) VALUES
(2, 'Pengalaman beli tiketnya rapi, cepat, dan elegan.', 5),
(2, 'Dashboardnya bikin gampang cek semua tiket.', 4);
