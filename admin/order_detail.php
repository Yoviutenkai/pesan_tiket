<?php
$active = 'admin_orders';
require_once __DIR__ . '/../components/admin_header.php';

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
    redirect('admin/orders.php');
}

$stmt = $pdo->prepare("SELECT o.*, u.nama, u.email, v.kode_voucher FROM orders o JOIN users u ON o.id_user = u.id_user LEFT JOIN voucher v ON o.id_voucher = v.id_voucher WHERE o.id_order = :id");
$stmt->execute(['id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    redirect('admin/orders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statusPembayaran = $_POST['status_pembayaran'] ?? $order['status_pembayaran'];
    $statusOrder = $_POST['status_order'] ?? $order['status_order'];

    $update = $pdo->prepare("UPDATE orders SET status_pembayaran = :status_p, status_order = :status_o WHERE id_order = :id");
    $update->execute([
        'status_p' => $statusPembayaran,
        'status_o' => $statusOrder,
        'id' => $orderId
    ]);

    $order['status_pembayaran'] = $statusPembayaran;
    $order['status_order'] = $statusOrder;

    flash_set('message', 'Status berhasil diperbarui.');
    redirect('admin/order_detail.php?id=' . $orderId);
}

$details = $pdo->prepare("SELECT od.*, t.nama_tiket FROM order_detail od JOIN tiket t ON od.id_tiket = t.id_tiket WHERE od.id_order = :id");
$details->execute(['id' => $orderId]);
$detailRows = $details->fetchAll();

$attendees = $pdo->prepare("SELECT * FROM attendee WHERE id_order = :id");
$attendees->execute(['id' => $orderId]);
$attendeeRows = $attendees->fetchAll();
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3">Detail Transaksi</h5>
  <?php require __DIR__ . '/../components/flash.php'; ?>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="text-muted">Kode order</div>
      <div class="fw-semibold"><?php echo e($order['kode_order']); ?></div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">User</div>
      <div class="fw-semibold"><?php echo e($order['nama']); ?> - <?php echo e($order['email']); ?></div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Voucher</div>
      <div class="fw-semibold"><?php echo e($order['kode_voucher'] ?? '-'); ?></div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Diskon</div>
      <div class="fw-semibold"><?php echo rupiah($order['diskon']); ?></div>
    </div>
  </div>

  <form method="post" action="<?php echo base_url('admin/order_detail.php?id=' . $orderId); ?>" class="row g-3 mt-2">
    <div class="col-md-4">
      <label class="form-label">Status Pembayaran</label>
      <select class="form-select" name="status_pembayaran">
        <?php foreach (['pending','paid','cancelled','expired'] as $status): ?>
          <option value="<?php echo $status; ?>" <?php echo $order['status_pembayaran'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Status Order</label>
      <select class="form-select" name="status_order">
        <?php foreach (['menunggu','diproses','selesai'] as $status): ?>
          <option value="<?php echo $status; ?>" <?php echo $order['status_order'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button class="btn btn-primary" type="submit">Update Status</button>
    </div>
  </form>

  <div class="table-modern mt-4">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Tiket</th>
          <th>Qty</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($detailRows as $detail): ?>
          <tr>
            <td><?php echo e($detail['nama_tiket']); ?></td>
            <td><?php echo (int)$detail['qty']; ?></td>
            <td><?php echo rupiah($detail['subtotal']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="table-modern mt-4">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode Tiket</th>
          <th>Nama</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($attendeeRows as $att): ?>
          <tr>
            <td><?php echo e($att['kode_tiket']); ?></td>
            <td><?php echo e($att['nama_pengunjung']); ?></td>
            <td><?php echo e($att['status_checkin']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
