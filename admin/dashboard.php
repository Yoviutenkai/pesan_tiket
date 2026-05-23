<?php
$active = 'admin_dashboard';
require_once __DIR__ . '/../components/admin_header.php';

$eventCount = (int)$pdo->query("SELECT COUNT(*) FROM event")->fetchColumn();
$ticketSold = (int)$pdo->query("SELECT IFNULL(SUM(tiket_terjual),0) FROM tiket")->fetchColumn();
$revenue = (float)$pdo->query("SELECT IFNULL(SUM(total_bayar),0) FROM orders WHERE status_pembayaran = 'paid'")->fetchColumn();
$pending = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status_verifikasi = 'pending'")->fetchColumn();
$recentOrders = $pdo->query("SELECT o.*, u.nama FROM orders o JOIN users u ON o.id_user = u.id_user ORDER BY o.created_at DESC LIMIT 6")->fetchAll();
$topEvents = $pdo->query("SELECT e.id_event, e.nama_event, IFNULL(SUM(t.tiket_terjual),0) AS sold FROM event e LEFT JOIN tiket t ON t.id_event = e.id_event GROUP BY e.id_event ORDER BY sold DESC LIMIT 4")->fetchAll();
$topEvent = $topEvents ? $topEvents[0] : null;
$paidCount = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status_verifikasi = 'approved'")->fetchColumn();
$cancelCount = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status_verifikasi = 'rejected'")->fetchColumn();
$totalOrders = max(1, $paidCount + $pending + $cancelCount);
$pendingPercent = (int)round(($pending / $totalOrders) * 100);
$paidPercent = (int)round(($paidCount / $totalOrders) * 100);
$cancelPercent = (int)round(($cancelCount / $totalOrders) * 100);
?>

<?php require_once __DIR__ . '/../components/flash.php'; ?>

<div class="hero-card mb-4">
  <div class="hero-grid">
    <div>
      <span class="badge-soft"><i class="bi bi-shield-check"></i> Admin Control</span>
      <h3 class="mt-2 fw-semibold">Dashboard Admin Eventa</h3>
      <p class="text-muted">Kelola event, tiket, dan transaksi dari satu panel kontrol premium.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-sm" href="<?php echo base_url('admin/event_form.php'); ?>">Tambah Event</a>
        <a class="btn btn-outline-light btn-sm" href="<?php echo base_url('admin/orders.php'); ?>">Pantau Transaksi</a>
      </div>
    </div>
    <div class="card-glass" style="min-width: 240px;">
      <div class="mini-label">Quick Actions</div>
      <div class="d-grid gap-2 mt-2">
        <a class="btn btn-outline-light btn-sm" href="<?php echo base_url('admin/tickets.php'); ?>">Kelola Tiket</a>
        <a class="btn btn-outline-light btn-sm" href="<?php echo base_url('admin/vouchers.php'); ?>">Kelola Voucher</a>
        <form method="post" action="<?php echo base_url('actions/seed_demo.php'); ?>">
          <button class="btn btn-outline-primary btn-sm w-100" type="submit">Generate Demo Data</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
      <div>
        <div class="stat-meta">Total Event</div>
        <div class="stat-value"><?php echo $eventCount; ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-ticket-perforated"></i></div>
      <div>
        <div class="stat-meta">Tiket Terjual</div>
        <div class="stat-value"><?php echo $ticketSold; ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
      <div>
        <div class="stat-meta">Revenue</div>
        <div class="stat-value"><?php echo rupiah($revenue); ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
      <div>
        <div class="stat-meta">Pending</div>
        <div class="stat-value"><?php echo $pending; ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-3">
  <div class="col-lg-7">
    <div class="card-glass">
      <h5 class="fw-semibold">Recent Transaction</h5>
      <div class="table-modern mt-3">
        <table class="table table-borderless align-middle mb-0">
          <thead>
            <tr>
              <th>Order</th>
              <th>User</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$recentOrders): ?>
              <tr><td colspan="4" class="text-muted">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php foreach ($recentOrders as $order): ?>
              <tr>
                <td><?php echo e($order['kode_order']); ?></td>
                <td><?php echo e($order['nama']); ?></td>
                <td><?php echo rupiah($order['total_bayar']); ?></td>
                <td><span class="badge-status <?php echo $order['status_pembayaran'] === 'pending' ? 'pending' : ''; ?>"><?php echo e($order['status_pembayaran']); ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card-glass">
      <h5 class="fw-semibold">Event Paling Ramai</h5>
      <?php if ($topEvent): ?>
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
          <div>
            <div class="fw-semibold"><?php echo e($topEvent['nama_event']); ?></div>
            <small class="text-muted">Tiket terjual: <?php echo (int)$topEvent['sold']; ?></small>
          </div>
          <span class="badge-soft"><i class="bi bi-lightning-charge"></i> HOT</span>
        </div>
      <?php else: ?>
        <div class="text-muted mt-3">Belum ada data event.</div>
      <?php endif; ?>
      <div class="mt-3">
        <?php if (!$topEvents): ?>
          <div class="text-muted">Belum ada data event.</div>
        <?php endif; ?>
        <?php foreach ($topEvents as $event): ?>
          <div class="list-item">
            <div>
              <div class="fw-semibold"><?php echo e($event['nama_event']); ?></div>
              <small class="text-muted">Tiket terjual: <?php echo (int)$event['sold']; ?></small>
            </div>
            <a class="btn btn-sm btn-outline-light" href="<?php echo base_url('admin/events.php'); ?>">Manage</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-3">
  <div class="col-lg-6">
    <div class="card-glass">
      <h5 class="fw-semibold">Payment Pipeline</h5>
      <div class="mt-3">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Pending</span>
          <span><?php echo $pending; ?></span>
        </div>
        <div class="progress-line"><span style="width: <?php echo $pendingPercent; ?>%;"></span></div>
        <div class="d-flex justify-content-between mb-2 mt-3">
          <span class="text-muted">Paid</span>
          <span><?php echo $paidCount; ?></span>
        </div>
        <div class="progress-line"><span style="width: <?php echo $paidPercent; ?>%;"></span></div>
        <div class="d-flex justify-content-between mb-2 mt-3">
          <span class="text-muted">Rejected</span>
          <span><?php echo $cancelCount; ?></span>
        </div>
        <div class="progress-line"><span style="width: <?php echo $cancelPercent; ?>%;"></span></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card-glass">
      <h5 class="fw-semibold">Checklist Harian</h5>
      <div class="mt-3">
        <div class="list-item">
          <div>
            <div class="fw-semibold">Validasi pembayaran baru</div>
            <small class="text-muted">Cek bukti transfer dan update status.</small>
          </div>
          <span class="badge-soft"><i class="bi bi-receipt"></i> Ops</span>
        </div>
        <div class="list-item">
          <div>
            <div class="fw-semibold">Update promo voucher</div>
            <small class="text-muted">Pastikan promo masih aktif.</small>
          </div>
          <span class="badge-soft"><i class="bi bi-gift"></i> Promo</span>
        </div>
        <div class="list-item">
          <div>
            <div class="fw-semibold">Jadwalkan event baru</div>
            <small class="text-muted">Tambahkan event untuk minggu depan.</small>
          </div>
          <span class="badge-soft"><i class="bi bi-calendar-event"></i> Plan</span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
