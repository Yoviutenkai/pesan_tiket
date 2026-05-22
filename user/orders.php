<?php
$active = 'user_orders';
require_once __DIR__ . '/../components/user_header.php';

$userId = current_user()['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id_user = :id ORDER BY created_at DESC");
$stmt->execute(['id' => $userId]);
$orders = $stmt->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Riwayat Transaksi</h5>
    <a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('events.php'); ?>">Pesan tiket</a>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Total</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$orders): ?>
          <tr><td colspan="4" class="text-muted">Belum ada transaksi.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><?php echo e($order['kode_order']); ?></td>
            <td><?php echo rupiah($order['total_bayar']); ?></td>
            <td><span class="badge-status <?php echo $order['status_pembayaran'] === 'pending' ? 'pending' : ''; ?>"><?php echo e($order['status_pembayaran']); ?></span></td>
            <td><a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('user/order_detail.php?id=' . $order['id_order']); ?>">Detail</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/user_footer.php'; ?>
