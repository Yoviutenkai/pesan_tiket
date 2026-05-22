<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$eventCount = (int)$pdo->query("SELECT COUNT(*) FROM event")->fetchColumn();
if ($eventCount >= 6) {
    flash_set('message', 'Demo data sudah cukup.');
    redirect('admin/dashboard.php');
}

$venues = [
    [
        'nama' => 'Lighthouse Dome',
        'alamat' => 'Jl. Sudirman No. 88',
        'kota' => 'Jakarta',
        'kapasitas' => 6000
    ],
    [
        'nama' => 'Horizon Garden',
        'alamat' => 'Jl. Malioboro No. 18',
        'kota' => 'Yogyakarta',
        'kapasitas' => 3000
    ],
    [
        'nama' => 'Metro Arts Hall',
        'alamat' => 'Jl. Pemuda No. 45',
        'kota' => 'Surabaya',
        'kapasitas' => 4500
    ]
];

$events = [
    [
        'nama' => 'Neon Soundscape',
        'kategori' => 'Music',
        'deskripsi' => 'Konser malam dengan tata cahaya immersive.',
        'organizer' => 'Eventa Studio',
        'mulai' => '2026-09-15 19:00:00',
        'selesai' => '2026-09-15 22:00:00'
    ],
    [
        'nama' => 'Future Creator Camp',
        'kategori' => 'Conference',
        'deskripsi' => 'Kelas premium untuk kreator dan brand.',
        'organizer' => 'Eventa Network',
        'mulai' => '2026-10-12 09:00:00',
        'selesai' => '2026-10-12 18:00:00'
    ],
    [
        'nama' => 'Cinematic Expo',
        'kategori' => 'Exhibition',
        'deskripsi' => 'Pameran teknologi visual dan instalasi digital.',
        'organizer' => 'Eventa Gallery',
        'mulai' => '2026-11-05 10:00:00',
        'selesai' => '2026-11-08 20:00:00'
    ],
    [
        'nama' => 'Vivid Tech Fest',
        'kategori' => 'Expo',
        'deskripsi' => 'Festival inovasi, startup, dan experience lab.',
        'organizer' => 'Eventa Labs',
        'mulai' => '2026-07-25 10:00:00',
        'selesai' => '2026-07-25 20:00:00'
    ],
    [
        'nama' => 'Orchid Night Run',
        'kategori' => 'Sport',
        'deskripsi' => 'Lari malam dengan visual lighting premium.',
        'organizer' => 'Eventa Sports',
        'mulai' => '2026-08-18 18:00:00',
        'selesai' => '2026-08-18 22:00:00'
    ],
    [
        'nama' => 'Design Week Masterclass',
        'kategori' => 'Workshop',
        'deskripsi' => 'Sesi intensif bersama praktisi desain ternama.',
        'organizer' => 'Eventa Academy',
        'mulai' => '2026-09-02 09:00:00',
        'selesai' => '2026-09-02 17:00:00'
    ]
];

try {
    $pdo->beginTransaction();

    $venueStmt = $pdo->prepare("INSERT INTO venue (nama_venue, alamat, kota, kapasitas) VALUES (:nama, :alamat, :kota, :kapasitas)");
    $venueIds = [];
    foreach ($venues as $venue) {
        $venueStmt->execute($venue);
        $venueIds[] = (int)$pdo->lastInsertId();
    }

    if (!$venueIds) {
        $venueIds = $pdo->query("SELECT id_venue FROM venue ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);
    }

    $eventStmt = $pdo->prepare("INSERT INTO event (id_venue, nama_event, kategori, deskripsi, organizer, tanggal_mulai, tanggal_selesai, status_event) VALUES (:id_venue, :nama, :kategori, :deskripsi, :organizer, :mulai, :selesai, 'upcoming')");
    $eventIds = [];
    foreach ($events as $index => $event) {
        $event['id_venue'] = $venueIds[$index % count($venueIds)];
        $eventStmt->execute($event);
        $eventIds[] = (int)$pdo->lastInsertId();
    }

    $ticketStmt = $pdo->prepare("INSERT INTO tiket (id_event, nama_tiket, deskripsi_tiket, harga, kuota, tiket_terjual, benefit, status_tiket) VALUES (:id_event, :nama, :deskripsi, :harga, :kuota, :terjual, :benefit, 'available')");
    foreach ($eventIds as $eventId) {
        $ticketStmt->execute([
            'id_event' => $eventId,
            'nama' => 'VIP Lounge',
            'deskripsi' => 'Akses lounge premium dan priority entry.',
            'harga' => 850000,
            'kuota' => 200,
            'terjual' => 20,
            'benefit' => 'Lounge, welcome kit, priority gate'
        ]);
        $ticketStmt->execute([
            'id_event' => $eventId,
            'nama' => 'Festival Pass',
            'deskripsi' => 'Akses area utama dan program utama.',
            'harga' => 350000,
            'kuota' => 1200,
            'terjual' => 75,
            'benefit' => 'Main stage access'
        ]);
    }

    $voucherStmt = $pdo->prepare("INSERT IGNORE INTO voucher (kode_voucher, jenis_diskon, nilai_diskon, minimum_transaksi, maksimal_diskon, tanggal_mulai, tanggal_expired, kuota_voucher, status_voucher) VALUES (:kode, :jenis, :nilai, :minimum, :maks, :mulai, :expired, :kuota, 'aktif')");
    $voucherStmt->execute([
        'kode' => 'WEEKEND15',
        'jenis' => 'persen',
        'nilai' => 15,
        'minimum' => 250000,
        'maks' => 120000,
        'mulai' => date('Y-m-d'),
        'expired' => date('Y-m-d', strtotime('+90 days')),
        'kuota' => 120
    ]);
    $voucherStmt->execute([
        'kode' => 'EVENTA80K',
        'jenis' => 'nominal',
        'nilai' => 80000,
        'minimum' => 400000,
        'maks' => 80000,
        'mulai' => date('Y-m-d'),
        'expired' => date('Y-m-d', strtotime('+60 days')),
        'kuota' => 60
    ]);

    $userId = $pdo->query("SELECT id_user FROM users WHERE role = 'user' ORDER BY id_user ASC LIMIT 1")->fetchColumn();
    if ($userId) {
        $testiStmt = $pdo->prepare("INSERT INTO testimonial (id_user, isi_testimonial, rating) VALUES (:id_user, :isi, :rating)");
        $testiStmt->execute([
            'id_user' => $userId,
            'isi' => 'Booking tiket makin cepat dan transparan.',
            'rating' => 5
        ]);
        $testiStmt->execute([
            'id_user' => $userId,
            'isi' => 'Dashboardnya bikin mudah cek status tiket.',
            'rating' => 4
        ]);
    }

    $pdo->commit();
    flash_set('message', 'Demo data berhasil ditambahkan.');
} catch (Exception $e) {
    $pdo->rollBack();
    flash_set('message', 'Gagal menambah demo data.');
}

redirect('admin/dashboard.php');
