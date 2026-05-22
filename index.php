<?php
require_once __DIR__ . '/config/bootstrap.php';

$baseSql = "SELECT e.*, v.nama_venue, v.kota,
    COALESCE(ts.total_kuota, 0) AS total_kuota,
    COALESCE(ts.total_terjual, 0) AS total_terjual
  FROM event e
  LEFT JOIN venue v ON e.id_venue = v.id_venue
  LEFT JOIN (
    SELECT id_event, SUM(kuota) AS total_kuota, SUM(tiket_terjual) AS total_terjual
    FROM tiket
    GROUP BY id_event
  ) ts ON ts.id_event = e.id_event";

$featured = $pdo->query($baseSql . " ORDER BY e.tanggal_mulai ASC LIMIT 3")->fetchAll();
$upcoming = $pdo->query($baseSql . " ORDER BY e.tanggal_mulai ASC LIMIT 6")->fetchAll();
$testimonials = $pdo->query("SELECT t.*, u.nama FROM testimonial t JOIN users u ON t.id_user = u.id_user ORDER BY t.created_at DESC LIMIT 4")->fetchAll();

if (!$featured) {
    $featured = [
        [
            'id_event' => 1,
            'nama_event' => 'Eventa Live Concert',
            'kategori' => 'Music',
            'deskripsi' => 'Konser premium dengan tata cahaya sinematik.',
            'tanggal_mulai' => '2026-06-20 19:00:00',
            'nama_venue' => 'Skyline Arena',
            'kota' => 'Jakarta',
            'banner_event' => '',
            'total_kuota' => 0,
            'total_terjual' => 0
        ],
        [
            'id_event' => 2,
            'nama_event' => 'Creator Summit 2026',
            'kategori' => 'Conference',
            'deskripsi' => 'Temu kreator dan brand dengan sesi eksklusif.',
            'tanggal_mulai' => '2026-07-12 09:00:00',
            'nama_venue' => 'Aurora Hall',
            'kota' => 'Bandung',
            'banner_event' => '',
            'total_kuota' => 0,
            'total_terjual' => 0
        ],
        [
            'id_event' => 3,
            'nama_event' => 'Art Week Immersive',
            'kategori' => 'Exhibition',
            'deskripsi' => 'Pameran seni modern dan instalasi interaktif.',
            'tanggal_mulai' => '2026-08-05 10:00:00',
            'nama_venue' => 'Oceanic Stage',
            'kota' => 'Bali',
            'banner_event' => '',
            'total_kuota' => 0,
            'total_terjual' => 0
        ]
    ];
}

if (!$upcoming) {
    $upcoming = $featured;
}

if (!$testimonials) {
    $testimonials = [
        ['nama' => 'Nadia Putri', 'isi_testimonial' => 'Pengalaman beli tiketnya rapi, cepat, dan elegan.', 'rating' => 5],
        ['nama' => 'Arga Malik', 'isi_testimonial' => 'Dashboardnya bikin gampang cek semua tiket.', 'rating' => 4],
        ['nama' => 'Salsa Gani', 'isi_testimonial' => 'Eventnya terasa premium dari awal booking.', 'rating' => 5],
        ['nama' => 'Reza Andi', 'isi_testimonial' => 'Semua informasi event lengkap dan jelas.', 'rating' => 4]
    ];
}

$ctaLink = is_logged_in() ? base_url('events.php') : base_url('login.php?return=events.php');
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventa - Pemesanan Tiket Event</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
  </head>
  <body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <section class="hero">
      <div class="container hero-content">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <span class="hero-badge mb-3"><i class="bi bi-stars"></i> Premium event booking</span>
            <h1>Bangun pengalaman event yang cinematic dan berkelas.</h1>
            <p class="mb-4">Eventa membantu kamu menemukan event terbaik, memesan tiket dengan aman, dan menikmati pengalaman sinematik dari awal sampai akhir.</p>
            <div class="d-flex gap-3 flex-wrap">
              <a class="btn btn-brand btn-lg" href="<?php echo $ctaLink; ?>">Pesan Tiket</a>
              <a class="btn btn-outline-light btn-lg" data-scroll href="#featured">Jelajahi Event</a>
            </div>
          </div>
          <div class="col-lg-5 mt-5 mt-lg-0">
            <div class="feature-panel">
              <h5 class="fw-semibold">Highlight minggu ini</h5>
              <p class="text-muted">Koleksi event eksklusif dengan promo dan pengalaman premium.</p>
              <div class="d-flex align-items-center gap-3">
                <div class="event-chip">VIP Access</div>
                <div class="event-chip">Limited Seat</div>
                <div class="event-chip">Special Bundle</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-pad" id="featured">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
          <div>
            <h2 class="section-title">Featured Event</h2>
            <p class="section-subtitle">Kurasi event terbaik dengan pengalaman premium.</p>
          </div>
          <a class="btn btn-outline-light" href="<?php echo base_url('events.php'); ?>">Lihat semua</a>
        </div>
        <div class="row g-4">
          <?php foreach ($featured as $index => $event): ?>
            <?php $coverClass = 'cover-' . (($index % 6) + 1); ?>
            <div class="col-lg-4">
              <div class="event-card">
                <?php if (!empty($event['banner_event'])): ?>
                  <img class="w-100" src="<?php echo base_url('uploads/events/' . e($event['banner_event'])); ?>" alt="<?php echo e($event['nama_event']); ?>">
                <?php else: ?>
                  <div class="event-cover <?php echo $coverClass; ?>"></div>
                <?php endif; ?>
                <div class="p-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="event-chip"><?php echo e($event['kategori']); ?></span>
                    <span class="event-meta"><i class="bi bi-geo"></i> <?php echo e($event['kota']); ?></span>
                  </div>
                  <h5 class="fw-semibold mb-2"><?php echo e($event['nama_event']); ?></h5>
                  <p class="text-muted mb-3"><?php echo e($event['deskripsi']); ?></p>
                  <div class="event-meta mb-3">
                    <i class="bi bi-calendar-event"></i>
                    <?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?>
                  </div>
                  <?php
                    $remaining = (int)$event['total_kuota'] - (int)$event['total_terjual'];
                    $isSoldOut = (int)$event['total_kuota'] > 0 && $remaining <= 0;
                    $detailLink = is_logged_in() ? base_url('event_detail.php?id=' . $event['id_event']) : base_url('login.php?return=event_detail.php?id=' . $event['id_event']);
                  ?>
                  <?php if ($isSoldOut): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="badge-soldout">SOLD OUT</span>
                      <span class="event-meta">Sisa: 0</span>
                    </div>
                    <button class="btn btn-disabled w-100" type="button" disabled>Sold Out</button>
                  <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="event-meta">Sisa: <?php echo max(0, $remaining); ?></span>
                      <?php if ((int)$event['total_kuota'] > 0): ?>
                        <span class="event-meta">Terjual: <?php echo (int)$event['total_terjual']; ?></span>
                      <?php endif; ?>
                    </div>
                    <a class="btn btn-brand w-100" href="<?php echo $detailLink; ?>">Pesan Tiket</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section-pad" id="upcoming">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
          <div>
            <h2 class="section-title">Upcoming Event</h2>
            <p class="section-subtitle">Event yang akan segera dimulai di kotamu.</p>
          </div>
          <a class="btn btn-outline-light" href="<?php echo base_url('events.php'); ?>">Lihat kalender</a>
        </div>
        <div class="row g-4">
          <?php foreach ($upcoming as $index => $event): ?>
            <?php $coverClass = 'cover-' . (($index % 6) + 1); ?>
            <div class="col-lg-4">
              <div class="event-card">
                <?php if (!empty($event['banner_event'])): ?>
                  <img class="w-100" src="<?php echo base_url('uploads/events/' . e($event['banner_event'])); ?>" alt="<?php echo e($event['nama_event']); ?>">
                <?php else: ?>
                  <div class="event-cover <?php echo $coverClass; ?>"></div>
                <?php endif; ?>
                <div class="p-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="event-chip"><?php echo e($event['kategori']); ?></span>
                    <span class="event-meta"><i class="bi bi-geo"></i> <?php echo e($event['kota']); ?></span>
                  </div>
                  <h5 class="fw-semibold mb-2"><?php echo e($event['nama_event']); ?></h5>
                  <div class="event-meta">
                    <i class="bi bi-calendar"></i>
                    <?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?>
                  </div>
                  <?php
                    $remaining = (int)$event['total_kuota'] - (int)$event['total_terjual'];
                    $isSoldOut = (int)$event['total_kuota'] > 0 && $remaining <= 0;
                    $detailLink = is_logged_in() ? base_url('event_detail.php?id=' . $event['id_event']) : base_url('login.php?return=event_detail.php?id=' . $event['id_event']);
                  ?>
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <?php if ($isSoldOut): ?>
                      <span class="badge-soldout">SOLD OUT</span>
                    <?php else: ?>
                      <span class="event-meta">Sisa: <?php echo max(0, $remaining); ?></span>
                    <?php endif; ?>
                  </div>
                  <a class="btn btn-outline-light w-100 mt-3" href="<?php echo $detailLink; ?>">Lihat Detail</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section-pad" id="experience">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <h2 class="section-title">Experience the next level of event booking.</h2>
            <p class="section-subtitle">Mulai dari pemilihan event, checkout, hingga validasi tiket, semuanya seamless dan profesional.</p>
            <div class="row g-3 mt-3">
              <div class="col-6">
                <div class="card-glow p-3">
                  <div class="fw-semibold">Voucher otomatis</div>
                  <small class="text-muted">Diskon langsung dihitung.</small>
                </div>
              </div>
              <div class="col-6">
                <div class="card-glow p-3">
                  <div class="fw-semibold">Ticketing aman</div>
                  <small class="text-muted">Kode tiket unik.</small>
                </div>
              </div>
              <div class="col-6">
                <div class="card-glow p-3">
                  <div class="fw-semibold">Smart dashboard</div>
                  <small class="text-muted">Riwayat dan ringkasan.</small>
                </div>
              </div>
              <div class="col-6">
                <div class="card-glow p-3">
                  <div class="fw-semibold">Check-in cepat</div>
                  <small class="text-muted">Validasi real time.</small>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div id="eventCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <div class="carousel-card">
                    <div class="event-cover cover-1 rounded mb-3"></div>
                    <h5 class="fw-semibold">Eventa Live Concert</h5>
                    <p class="text-muted">Music | Jakarta</p>
                  </div>
                </div>
                <div class="carousel-item">
                  <div class="carousel-card">
                    <div class="event-cover cover-2 rounded mb-3"></div>
                    <h5 class="fw-semibold">Creator Summit</h5>
                    <p class="text-muted">Conference | Bandung</p>
                  </div>
                </div>
                <div class="carousel-item">
                  <div class="carousel-card">
                    <div class="event-cover cover-3 rounded mb-3"></div>
                    <h5 class="fw-semibold">Art Week Immersive</h5>
                    <p class="text-muted">Exhibition | Bali</p>
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-pad" id="sponsor">
      <div class="container">
        <div class="text-center mb-4">
          <h2 class="section-title">Trusted by premium partners</h2>
          <p class="section-subtitle mx-auto">Kolaborasi dengan brand dan komunitas terbaik.</p>
        </div>
        <div class="sponsor-grid">
          <div class="sponsor-pill">Lumen</div>
          <div class="sponsor-pill">Aether</div>
          <div class="sponsor-pill">Vivid</div>
          <div class="sponsor-pill">Spectra</div>
          <div class="sponsor-pill">Nova</div>
        </div>
      </div>
    </section>

    <section class="section-pad" id="testimonial">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
          <div>
            <h2 class="section-title">Testimonial</h2>
            <p class="section-subtitle">Cerita dari pengguna Eventa.</p>
          </div>
          <a class="btn btn-outline-light" href="<?php echo base_url('register.php'); ?>">Gabung sekarang</a>
        </div>
        <div class="row g-4">
          <?php foreach ($testimonials as $testi): ?>
            <div class="col-lg-3 col-md-6">
              <div class="testimonial-card">
                <div class="d-flex gap-2 text-warning mb-2">
                  <?php
                    $rating = (int)($testi['rating'] ?? 5);
                    for ($i = 0; $i < $rating; $i++):
                  ?>
                    <i class="bi bi-star-fill"></i>
                  <?php endfor; ?>
                </div>
                <p class="mb-3">"<?php echo e($testi['isi_testimonial']); ?>"</p>
                <div class="fw-semibold"><?php echo e($testi['nama'] ?? 'Eventa Member'); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section-pad">
      <div class="container">
        <div class="feature-panel text-center">
          <h3 class="fw-semibold">Siap booking event favoritmu?</h3>
          <p class="text-muted">Gabung sekarang dan nikmati dashboard pemesanan premium.</p>
          <a class="btn btn-brand btn-lg" href="<?php echo $ctaLink; ?>">Mulai Sekarang</a>
        </div>
      </div>
    </section>

    <?php require_once __DIR__ . '/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
  </body>
</html>
