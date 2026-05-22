<?php
$active = 'user_dashboard';
require_once __DIR__ . '/../components/user_header.php';

$userId = current_user()['id'];
$ticketStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM attendee a JOIN orders o ON a.id_order = o.id_order WHERE o.id_user = :id");
$ticketStmt->execute(['id' => $userId]);
$ticketCount = $ticketStmt->fetch();

$orderStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM orders WHERE id_user = :id");
$orderStmt->execute(['id' => $userId]);
$orderCount = $orderStmt->fetch();

$recentStmt = $pdo->prepare("SELECT * FROM orders WHERE id_user = :id ORDER BY created_at DESC LIMIT 5");
$recentStmt->execute(['id' => $userId]);
$recentOrders = $recentStmt->fetchAll();

$recommendations = $pdo->query("SELECT id_event, nama_event, kategori, tanggal_mulai FROM event ORDER BY tanggal_mulai ASC LIMIT 3")->fetchAll();
$nextEvent = $pdo->query("SELECT id_event, nama_event, kategori, tanggal_mulai FROM event ORDER BY tanggal_mulai ASC LIMIT 1")->fetch();

$checkedStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM attendee a JOIN orders o ON a.id_order = o.id_order WHERE o.id_user = :id AND a.status_checkin = 'sudah_hadir'");
$checkedStmt->execute(['id' => $userId]);
$checkedCount = $checkedStmt->fetch();

$pendingStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM orders WHERE id_user = :id AND status_pembayaran = 'pending'");
$pendingStmt->execute(['id' => $userId]);
$pendingCount = $pendingStmt->fetch();

$voucher = $pdo->query("SELECT kode_voucher, jenis_diskon, nilai_diskon FROM voucher WHERE status_voucher = 'aktif' ORDER BY created_at DESC LIMIT 1")->fetch();
$totalTickets = (int)($ticketCount['total'] ?? 0);
$checkedTickets = (int)($checkedCount['total'] ?? 0);
$checkedPercent = $totalTickets > 0 ? (int)round(($checkedTickets / $totalTickets) * 100) : 0;
?>

<div class="hero-card mb-4">
  <div class="hero-grid">
    <div>
      <span class="badge-soft"><i class="bi bi-stars"></i> User Dashboard</span>
      <h3 class="mt-2 fw-semibold">Halo, <?php echo e(current_user()['nama'] ?? 'Eventa Member'); ?>.</h3>
      <p class="text-muted">Pantau status tiket, transaksi, dan rekomendasi event terbaru dalam satu tempat.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-sm" href="<?php echo base_url('events.php'); ?>">Pesan Tiket</a>
        <a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('user/orders.php'); ?>">Riwayat</a>
      </div>
    </div>
    <div class="card-glass" style="min-width: 220px;">
      <div class="mini-label">Next Event</div>
      <div class="fw-semibold"><?php echo e($nextEvent['nama_event'] ?? 'Belum ada event'); ?></div>
      <small class="text-muted"><?php echo $nextEvent ? date('d M Y', strtotime($nextEvent['tanggal_mulai'])) : 'Segera hadir'; ?></small>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-ticket-perforated"></i></div>
      <div>
        <div class="stat-meta">Total tiket</div>
        <div class="stat-value"><?php echo (int)($ticketCount['total'] ?? 0); ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-receipt"></i></div>
      <div>
        <div class="stat-meta">Total transaksi</div>
        <div class="stat-value"><?php echo (int)($orderCount['total'] ?? 0); ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
      <div>
        <div class="stat-meta">Pending payment</div>
        <div class="stat-value"><?php echo (int)($pendingCount['total'] ?? 0); ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-3">
  <div class="col-lg-4">
    <div class="card-glass">
      <h5 class="fw-semibold">Ticket Summary</h5>
      <div class="mt-3">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Checked-in</span>
          <span><?php echo $checkedTickets; ?>/<?php echo $totalTickets; ?></span>
        </div>
        <div class="progress-line"><span style="width: <?php echo $checkedPercent; ?>%;"></span></div>
        <small class="text-muted d-block mt-2">Progress hadir di event.</small>
      </div>
      <div class="mt-4">
        <div class="mini-label">Voucher spotlight</div>
        <div class="fw-semibold"><?php echo e($voucher['kode_voucher'] ?? 'Belum ada'); ?></div>
        <small class="text-muted"><?php echo $voucher ? strtoupper($voucher['jenis_diskon']) . ' ' . $voucher['nilai_diskon'] : 'Cek promo terbaru'; ?></small>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card-glass">
      <h5 class="fw-semibold">Recent Transaction</h5>
      <div class="table-modern mt-3">
        <table class="table table-borderless align-middle mb-0">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$recentOrders): ?>
              <tr><td colspan="3" class="text-muted">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($recentOrders as $order): ?>
              <tr>
                <td><?php echo e($order['kode_order']); ?></td>
                <td><?php echo rupiah($order['total_bayar']); ?></td>
                <td><span class="badge-status <?php echo $order['status_pembayaran'] === 'pending' ? 'pending' : ''; ?>"><?php echo e($order['status_pembayaran']); ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-3">
  <div class="col-lg-6">
    <div class="card-glass">
      <h5 class="fw-semibold">Event Recommendation</h5>
      <div class="mt-3">
        <?php if (!$recommendations): ?>
          <div class="text-muted">Belum ada event yang tersedia.</div>
        <?php endif; ?>
        <?php foreach ($recommendations as $event): ?>
          <div class="list-item">
            <div>
              <div class="fw-semibold"><?php echo e($event['nama_event']); ?></div>
              <small class="text-muted"><?php echo e($event['kategori']); ?> - <?php echo date('d M', strtotime($event['tanggal_mulai'])); ?></small>
            </div>
            <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('event_detail.php?id=' . $event['id_event']); ?>">Lihat</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card-glass">
      <h5 class="fw-semibold">Tips cepat</h5>
      <div class="mt-3">
        <div class="list-item">
          <div>
            <div class="fw-semibold">Aktifkan voucher</div>
            <small class="text-muted">Masukkan kode promo di checkout.</small>
          </div>
          <span class="badge-soft"><i class="bi bi-gift"></i> Promo</span>
        </div>
        <div class="list-item">
          <div>
            <div class="fw-semibold">Cek jadwal event</div>
            <small class="text-muted">Atur reminder H-1 event.</small>
          </div>
          <span class="badge-soft"><i class="bi bi-calendar-event"></i> Schedule</span>
        </div>
        <div class="list-item">
          <div>
            <div class="fw-semibold">Download tiket</div>
            <small class="text-muted">Bawa QR saat check-in.</small>
          </div>
          <span class="badge-soft"><i class="bi bi-qr-code"></i> QR</span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../components/user_footer.php'; ?>
