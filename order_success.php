<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();

$orderId = (int)($_GET['id'] ?? ($_SESSION['last_order_id'] ?? 0));
if ($orderId <= 0) {
    redirect('user/orders.php');
}

$stmt = $pdo->prepare("SELECT o.*, u.nama, u.email FROM orders o JOIN users u ON o.id_user = u.id_user WHERE o.id_order = :id AND o.id_user = :user");
$stmt->execute(['id' => $orderId, 'user' => current_user()['id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('user/orders.php');
}

$detailStmt = $pdo->prepare("SELECT od.*, t.nama_tiket FROM order_detail od JOIN tiket t ON od.id_tiket = t.id_tiket WHERE od.id_order = :id");
$detailStmt->execute(['id' => $orderId]);
$details = $detailStmt->fetchAll();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Berhasil - Eventa</title>
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
        <div class="card-glow p-4">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
              <i class="bi bi-check-lg text-white"></i>
            </div>
            <div>
              <h4 class="fw-semibold mb-0">Pesanan berhasil dibuat</h4>
              <small class="text-muted">Kode order: <?php echo e($order['kode_order']); ?></small>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="card-glow p-3">
                <div class="text-muted">Nama</div>
                <div class="fw-semibold"><?php echo e($order['nama']); ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card-glow p-3">
                <div class="text-muted">Email</div>
                <div class="fw-semibold"><?php echo e($order['email']); ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card-glow p-3">
                <div class="text-muted">Total bayar</div>
                <div class="fw-semibold"><?php echo rupiah($order['total_bayar']); ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card-glow p-3">
                <div class="text-muted">Status pembayaran</div>
                <div class="fw-semibold"><?php echo e($order['status_pembayaran']); ?></div>
              </div>
            </div>
          </div>

          <h5 class="fw-semibold mt-4">Detail Tiket</h5>
          <div class="table-modern mt-2">
            <table class="table table-borderless align-middle mb-0">
              <thead>
                <tr>
                  <th>Jenis Tiket</th>
                  <th>Qty</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($details as $detail): ?>
                  <tr>
                    <td><?php echo e($detail['nama_tiket']); ?></td>
                    <td><?php echo (int)$detail['qty']; ?></td>
                    <td><?php echo rupiah($detail['subtotal']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="d-flex gap-3 mt-4">
            <a class="btn btn-brand" href="<?php echo base_url('user/orders.php'); ?>">Lihat Riwayat</a>
            <a class="btn btn-outline-light" href="<?php echo base_url('events.php'); ?>">Pesan Lagi</a>
          </div>
        </div>
      </div>
    </main>

    <?php require_once __DIR__ . '/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
  </body>
</html>
