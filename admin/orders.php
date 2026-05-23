<?php
$active = 'admin_orders';
require_once __DIR__ . '/../components/admin_header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$totalRows = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$listStmt = $pdo->prepare("SELECT o.*, u.nama, v.kode_voucher FROM orders o JOIN users u ON o.id_user = u.id_user LEFT JOIN voucher v ON o.id_voucher = v.id_voucher ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset");
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$orders = $listStmt->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Manajemen Transaksi</h5>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>User</th>
          <th>Total</th>
          <th>Voucher</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$orders): ?>
          <tr><td colspan="6" class="text-muted">Belum ada transaksi.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><?php echo e($order['kode_order']); ?></td>
            <td><?php echo e($order['nama']); ?></td>
            <td><?php echo rupiah($order['total_bayar']); ?></td>
            <td><?php echo e($order['kode_voucher'] ?? '-'); ?></td>
            <td><span class="badge-status <?php echo $order['status_pembayaran'] === 'pending' ? 'pending' : ''; ?>"><?php echo e($order['status_pembayaran']); ?></span></td>
            <td><a class="btn btn-sm btn-outline-dark" href="<?php echo base_url('admin/order_detail.php?id=' . $order['id_order']); ?>">Detail</a></td>
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
            <a class="page-link" href="<?php echo base_url('admin/orders.php?page=' . $i); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
