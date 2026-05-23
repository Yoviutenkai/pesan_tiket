<?php
require_once __DIR__ . '/config/bootstrap.php';

$keyword = trim($_GET['q'] ?? '');

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

if ($keyword !== '') {
  $stmt = $pdo->prepare($baseSql . " WHERE e.nama_event LIKE :q1 OR e.kategori LIKE :q2 ORDER BY e.tanggal_mulai ASC");
  $stmt->execute([
    'q1' => '%' . $keyword . '%',
    'q2' => '%' . $keyword . '%'
  ]);
  $events = $stmt->fetchAll();
} else {
  $events = $pdo->query($baseSql . " ORDER BY e.tanggal_mulai ASC")->fetchAll();
}

if (!$events) {
    $events = [
        [
            'id_event' => 1,
            'nama_event' => 'Eventa Live Concert',
            'kategori' => 'Music',
            'deskripsi' => 'Konser premium dengan tata cahaya sinematik.',
            'tanggal_mulai' => '2026-06-20 19:00:00',
            'nama_venue' => 'Skyline Arena',
            'kota' => 'Jakarta',
            'banner_event' => ''
        ]
    ];
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event - Eventa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
  </head>
  <body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <main class="section-pad" style="padding-top: 140px;">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
          <div>
            <h2 class="section-title">Semua Event</h2>
            <p class="section-subtitle">Cari event terbaik dan pesan tiketnya.</p>
          </div>
          <form class="d-flex gap-2" method="get">
            <input class="form-control" type="search" name="q" value="<?php echo e($keyword); ?>" placeholder="Cari event">
            <button class="btn btn-brand" type="submit">Cari</button>
          </form>
        </div>

        <div class="row g-4">
          <?php foreach ($events as $index => $event): ?>
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
                  <?php
                    $statusEvent = $event['status_event'] ?? 'upcoming';
                    $statusClass = match ($statusEvent) {
                        'ongoing' => 'bg-success',
                        'finished' => 'bg-secondary',
                        'cancelled' => 'bg-danger',
                        default => 'bg-primary'
                    };
                  ?>
                  <span class="badge <?php echo $statusClass; ?> mb-2"><?php echo e($statusEvent); ?></span>
                  <h5 class="fw-semibold mb-2"><?php echo e($event['nama_event']); ?></h5>
                  <div class="event-meta mb-3">
                    <i class="bi bi-calendar-event"></i>
                    <?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?>
                  </div>
                  <?php
                    $totalKuota = (int)($event['total_kuota'] ?? 0);
                    $totalTerjual = (int)($event['total_terjual'] ?? 0);
                    $remaining = $totalKuota - $totalTerjual;
                    $isSoldOut = $totalKuota > 0 && $remaining <= 0;
                    $isUnavailable = in_array($statusEvent, ['finished', 'cancelled'], true);
                    $detailLink = is_logged_in() ? base_url('event_detail.php?id=' . $event['id_event']) : base_url('login.php?return=event_detail.php?id=' . $event['id_event']);
                    $buttonLabel = is_logged_in() ? 'Pesan Tiket' : 'Masuk untuk beli';
                    $buttonClass = is_logged_in() ? 'btn btn-brand' : 'btn btn-outline-light';
                  ?>
                  <?php if ($statusEvent === 'cancelled'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="badge bg-danger">Dibatalkan</span>
                      <span class="event-meta">Event tidak tersedia</span>
                    </div>
                    <button class="btn btn-disabled w-100" type="button" disabled>Event Tidak Tersedia</button>
                  <?php elseif ($statusEvent === 'finished'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="badge bg-secondary">Selesai</span>
                      <span class="event-meta">Event tidak tersedia</span>
                    </div>
                    <button class="btn btn-disabled w-100" type="button" disabled>Event Tidak Tersedia</button>
                  <?php elseif ($isSoldOut): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="badge-soldout">SOLD OUT</span>
                      <span class="event-meta">Sisa: 0</span>
                    </div>
                    <button class="btn btn-disabled w-100" type="button" disabled>Sold Out</button>
                  <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="event-meta">Sisa: <?php echo max(0, $remaining); ?></span>
                      <?php if ($totalKuota > 0): ?>
                        <span class="event-meta">Terjual: <?php echo $totalTerjual; ?></span>
                      <?php endif; ?>
                    </div>
                    <a class="<?php echo $buttonClass; ?> w-100" href="<?php echo $detailLink; ?>"><?php echo $buttonLabel; ?></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>

    <?php require_once __DIR__ . '/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
  </body>
</html>
