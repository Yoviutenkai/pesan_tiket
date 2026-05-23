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

$paymentStmt = $pdo->prepare("SELECT p.*, u.nama AS verifier FROM payments p LEFT JOIN users u ON u.id_user = p.verified_by WHERE p.id_order = :id LIMIT 1");
$paymentStmt->execute(['id' => $orderId]);
$payment = $paymentStmt->fetch();

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
    <div class="col-md-6">
      <div class="text-muted">Status Verifikasi</div>
      <?php
        $verifStatus = $payment['status_verifikasi'] ?? 'pending';
        $verifBadge = match ($verifStatus) {
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-warning'
        };
      ?>
      <div><span class="badge <?php echo $verifBadge; ?>"><?php echo e($verifStatus); ?></span></div>
    </div>
  </div>
  <div class="row g-3 mt-2">
    <div class="col-md-6">
      <div class="card-glass p-3">
        <h6 class="fw-semibold">Bukti Transfer</h6>
        <?php if ($payment && $payment['bukti_transfer']): ?>
          <a href="<?php echo base_url('uploads/payment/' . e($payment['bukti_transfer'])); ?>" target="_blank">
            <img class="img-fluid rounded" src="<?php echo base_url('uploads/payment/' . e($payment['bukti_transfer'])); ?>" alt="Bukti Transfer">
          </a>
        <?php else: ?>
          <div class="text-muted">Belum ada bukti transfer.</div>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card-glass p-3">
        <h6 class="fw-semibold">Verifikasi Pembayaran</h6>
        <form method="post" action="<?php echo base_url('admin/approve_payment.php'); ?>" class="d-flex gap-2 mb-2">
          <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
          <button class="btn btn-success" type="submit" <?php echo ($verifStatus === 'approved' || !$payment || !$payment['bukti_transfer']) ? 'disabled' : ''; ?>>Approve</button>
        </form>
        <form method="post" action="<?php echo base_url('admin/reject_payment.php'); ?>">
          <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
          <textarea class="form-control mb-2" name="catatan_admin" rows="2" placeholder="Catatan penolakan" <?php echo (!$payment || !$payment['bukti_transfer']) ? 'disabled' : ''; ?>><?php echo e($payment['catatan_admin'] ?? ''); ?></textarea>
          <button class="btn btn-danger" type="submit" <?php echo ($verifStatus === 'rejected' || !$payment || !$payment['bukti_transfer']) ? 'disabled' : ''; ?>>Reject</button>
        </form>
        <?php if ($payment && $payment['verified_by']): ?>
          <small class="text-muted d-block mt-2">Diverifikasi oleh <?php echo e($payment['verifier'] ?? 'Admin'); ?> pada <?php echo $payment['verified_at'] ? date('d M Y H:i', strtotime($payment['verified_at'])) : '-'; ?></small>
        <?php endif; ?>
      </div>
    </div>
  </div>

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
