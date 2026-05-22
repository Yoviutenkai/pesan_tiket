<?php
require_once __DIR__ . '/config/bootstrap.php';

$ticketId = (int)($_GET['ticket_id'] ?? 0);
$qty = max(1, (int)($_GET['qty'] ?? 1));

if ($ticketId <= 0) {
    redirect('events.php');
}

require_login('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);

$stmt = $pdo->prepare("SELECT t.*, e.nama_event, e.kategori, e.tanggal_mulai FROM tiket t JOIN event e ON t.id_event = e.id_event WHERE t.id_tiket = :id");
$stmt->execute(['id' => $ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    flash_set('message', 'Tiket tidak ditemukan.');
    redirect('events.php');
}

$available = (int)$ticket['kuota'] - (int)$ticket['tiket_terjual'];
if ($qty > $available) {
    $qty = max(1, $available);
}

$subtotal = (float)$ticket['harga'] * $qty;
$user = current_user();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Eventa</title>
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
            <div class="card-glow p-4">
              <h4 class="fw-semibold mb-3">Informasi Pemesan</h4>
              <form method="post" action="<?php echo base_url('actions/checkout_action.php'); ?>">
                <input type="hidden" name="ticket_id" value="<?php echo (int)$ticket['id_tiket']; ?>">
                <input type="hidden" name="qty" value="<?php echo $qty; ?>">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nama Pengunjung</label>
                    <input type="text" class="form-control" name="attendee_name" value="<?php echo e($user['nama'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="attendee_email" value="<?php echo e($user['email'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">No HP</label>
                    <input type="text" class="form-control" name="attendee_phone" value="<?php echo e($user['no_hp'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Voucher (opsional)</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="voucherCode" name="voucher_code" placeholder="EVENTA10">
                      <button class="btn btn-outline-light" type="button" id="voucherApply" data-ticket-id="<?php echo (int)$ticket['id_tiket']; ?>" data-qty="<?php echo $qty; ?>">Apply</button>
                    </div>
                    <div class="voucher-status" id="voucherStatus"></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Metode Pembayaran</label>
                    <select class="form-select" name="payment_method">
                      <option value="transfer">Transfer Bank</option>
                      <option value="ewallet">E-Wallet</option>
                      <option value="virtual">Virtual Account</option>
                    </select>
                  </div>
                </div>
                <input type="hidden" name="voucher_discount" id="voucherDiscountInput" value="0">
                <button class="btn btn-brand w-100 mt-4" type="submit">Buat Pesanan</button>
              </form>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card-glow p-4">
              <h5 class="fw-semibold">Ringkasan Pesanan</h5>
              <div class="d-flex justify-content-between mb-2">
                <span><?php echo e($ticket['nama_event']); ?></span>
                <span><?php echo e($ticket['nama_tiket']); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Qty</span>
                <span><?php echo $qty; ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Subtotal</span>
                <span><?php echo rupiah($subtotal); ?></span>
              </div>
              <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Diskon Voucher</span>
                <span id="discountPreview">Rp 0</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between fw-semibold">
                <span>Total Bayar</span>
                <span id="totalPreview"><?php echo rupiah($subtotal); ?></span>
              </div>
              <small class="text-muted d-block mt-2">Diskon voucher dihitung otomatis setelah apply.</small>
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
