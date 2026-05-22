<?php
require_once __DIR__ . '/config/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT e.*, v.nama_venue, v.kota, v.alamat FROM event e LEFT JOIN venue v ON e.id_venue = v.id_venue WHERE e.id_event = :id");
$stmt->execute(['id' => $id]);
$event = $stmt->fetch();

if (!$event) {
    flash_set('message', 'Event tidak ditemukan.');
    redirect('events.php');
}

$ticketStmt = $pdo->prepare("SELECT * FROM tiket WHERE id_event = :id ORDER BY harga ASC");
$ticketStmt->execute(['id' => $id]);
$tickets = $ticketStmt->fetchAll();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($event['nama_event']); ?> - Eventa</title>
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
        <?php require __DIR__ . '/components/flash.php'; ?>
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="event-card">
              <?php if (!empty($event['banner_event'])): ?>
                <img class="w-100" src="<?php echo base_url('uploads/events/' . e($event['banner_event'])); ?>" alt="<?php echo e($event['nama_event']); ?>">
              <?php else: ?>
                <div class="event-cover cover-1"></div>
              <?php endif; ?>
              <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="event-chip"><?php echo e($event['kategori']); ?></span>
                  <span class="event-meta"><i class="bi bi-geo"></i> <?php echo e($event['kota']); ?></span>
                </div>
                <h3 class="fw-semibold mb-2"><?php echo e($event['nama_event']); ?></h3>
                <p class="text-muted mb-3"><?php echo e($event['deskripsi']); ?></p>
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="card-glow p-3">
                      <div class="text-muted">Tanggal</div>
                      <div class="fw-semibold"><?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?></div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card-glow p-3">
                      <div class="text-muted">Venue</div>
                      <div class="fw-semibold"><?php echo e($event['nama_venue']); ?></div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="card-glow p-3">
                      <div class="text-muted">Alamat</div>
                      <div class="fw-semibold"><?php echo e($event['alamat']); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card-glow p-4">
              <h5 class="fw-semibold">Pilih Tiket</h5>
              <p class="text-muted">Tentukan kategori tiket terbaik untukmu.</p>
              <?php if (!$tickets): ?>
                <div class="text-muted">Belum ada tiket untuk event ini.</div>
              <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                  <?php
                    $available = (int)$ticket['kuota'] - (int)$ticket['tiket_terjual'];
                    $isSoldOut = $available <= 0 || $ticket['status_tiket'] === 'sold_out';
                  ?>
                  <div class="border rounded-3 p-3 mb-3" style="border-color: rgba(148,163,184,0.2);">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="fw-semibold"><?php echo e($ticket['nama_tiket']); ?></div>
                        <small class="text-muted"><?php echo e($ticket['deskripsi_tiket']); ?></small>
                      </div>
                      <div class="fw-semibold"><?php echo rupiah($ticket['harga']); ?></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Sisa: <?php echo max(0, $available); ?></small>
                        <?php if ($isSoldOut): ?>
                          <span class="badge-soldout">SOLD OUT</span>
                        <?php endif; ?>
                      </div>
                      <?php if (is_logged_in()): ?>
                        <form class="d-flex gap-2" method="get" action="<?php echo base_url('checkout.php'); ?>">
                          <input type="hidden" name="ticket_id" value="<?php echo (int)$ticket['id_tiket']; ?>">
                          <input type="number" name="qty" class="form-control form-control-sm" min="1" max="<?php echo $available; ?>" value="1" style="width: 90px;" <?php echo $isSoldOut ? 'disabled' : ''; ?>>
                          <button class="btn btn-brand btn-sm" type="submit" <?php echo $isSoldOut ? 'disabled' : ''; ?>><?php echo $isSoldOut ? 'Sold Out' : 'Checkout'; ?></button>
                        </form>
                      <?php else: ?>
                        <a class="btn btn-outline-light btn-sm" href="<?php echo base_url('login.php?return=event_detail.php?id=' . $event['id_event']); ?>">Masuk untuk beli</a>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </main>

    <?php require_once __DIR__ . '/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
  </body>
</html>
