<?php
$active = 'user_orders';
require_once __DIR__ . '/../components/user_header.php';

$orderId = (int)($_GET['id'] ?? 0);
$userId = current_user()['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id_order = :id AND id_user = :user");
$stmt->execute(['id' => $orderId, 'user' => $userId]);
$order = $stmt->fetch();

if (!$order) {
    redirect('user/orders.php');
}

$detailStmt = $pdo->prepare("SELECT od.*, t.nama_tiket FROM order_detail od JOIN tiket t ON od.id_tiket = t.id_tiket WHERE od.id_order = :id");
$detailStmt->execute(['id' => $orderId]);
$details = $detailStmt->fetchAll();

$paymentStmt = $pdo->prepare("SELECT * FROM payments WHERE id_order = :id LIMIT 1");
$paymentStmt->execute(['id' => $orderId]);
$payment = $paymentStmt->fetch();

$flash = flash_get('message');
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3">Detail Order</h5>
  <?php if ($flash): ?>
    <div class="alert alert-warning border-0" role="alert"><?php echo e($flash); ?></div>
  <?php endif; ?>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="text-muted">Kode order</div>
      <div class="fw-semibold"><?php echo e($order['kode_order']); ?></div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Total</div>
      <div class="fw-semibold"><?php echo rupiah($order['total_bayar']); ?></div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Status Pembayaran</div>
      <?php
        $statusPay = $order['status_pembayaran'] ?? 'pending';
        $statusBadge = match ($statusPay) {
            'paid' => 'bg-success',
            'rejected' => 'bg-danger',
            'expired' => 'bg-secondary',
            default => 'bg-warning'
        };
      ?>
      <div><span class="badge <?php echo $statusBadge; ?>"><?php echo e($statusPay); ?></span></div>
    </div>
  </div>

  <div class="table-modern mt-4">
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

  <div class="mt-4">
    <h6 class="fw-semibold">Bukti Transfer</h6>
    <?php if ($payment && $payment['bukti_transfer']): ?>
      <div class="card-glass p-3 mb-3">
        <a href="<?php echo base_url('uploads/payment/' . e($payment['bukti_transfer'])); ?>" target="_blank">
          <img class="img-fluid rounded" src="<?php echo base_url('uploads/payment/' . e($payment['bukti_transfer'])); ?>" alt="Bukti Transfer">
        </a>
      </div>
    <?php endif; ?>
    <?php
      $canUpload = !$payment || in_array($payment['status_verifikasi'] ?? 'pending', ['pending', 'rejected'], true);
    ?>
    <?php if ($canUpload): ?>
      <form method="post" action="<?php echo base_url('actions/upload_payment.php'); ?>" enctype="multipart/form-data" class="d-flex gap-3 align-items-center">
        <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
        <input type="file" name="payment_proof" class="form-control" accept="image/png, image/jpeg" required>
        <button class="btn btn-primary" type="submit">Upload</button>
      </form>
    <?php else: ?>
      <div class="text-muted">Pembayaran sudah diverifikasi.</div>
    <?php endif; ?>
    <?php if ($payment && $payment['catatan_admin']): ?>
      <div class="alert alert-danger border-0 mt-3"><?php echo e($payment['catatan_admin']); ?></div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../components/user_footer.php'; ?>
