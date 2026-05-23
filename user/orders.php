<?php
$active = 'user_orders';
require_once __DIR__ . '/../components/user_header.php';

$userId = current_user()['id'];
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE id_user = :id");
$countStmt->execute(['id' => $userId]);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id_user = :id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
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
  <div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Total: <?php echo $totalRows; ?> transaksi</small>
    <nav>
      <ul class="pagination mb-0">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo base_url('user/orders.php?page=' . $i); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>
</div>

<?php require_once __DIR__ . '/../components/user_footer.php'; ?>
