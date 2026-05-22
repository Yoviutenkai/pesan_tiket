<?php
$active = 'petugas_checkin';
require_once __DIR__ . '/../components/petugas_header.php';

$message = null;
$type = 'info';
$ticketInfo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_tiket'] ?? '');
    if ($kode !== '') {
        $stmt = $pdo->prepare("SELECT * FROM attendee WHERE kode_tiket = :kode LIMIT 1");
        $stmt->execute(['kode' => $kode]);
        $ticketInfo = $stmt->fetch();

        if (!$ticketInfo) {
            $message = 'Kode tiket tidak ditemukan.';
            $type = 'danger';
        } elseif ($ticketInfo['status_checkin'] === 'sudah_hadir') {
            $message = 'Tiket sudah digunakan.';
            $type = 'warning';
        } else {
            $update = $pdo->prepare("UPDATE attendee SET status_checkin = 'sudah_hadir', waktu_checkin = NOW() WHERE id_attendee = :id");
            $update->execute(['id' => $ticketInfo['id_attendee']]);
            $message = 'Check-in berhasil.';
            $type = 'success';
            $ticketInfo['status_checkin'] = 'sudah_hadir';
            $ticketInfo['waktu_checkin'] = date('Y-m-d H:i:s');
        }
    }
}
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3">Validasi Check-in</h5>
  <form method="post" class="row g-3 align-items-end">
    <div class="col-md-6">
      <label class="form-label">Kode Tiket</label>
      <input type="text" class="form-control" name="kode_tiket" placeholder="TKTXXXXXXXX" required>
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary" type="submit">Validasi</button>
    </div>
  </form>

  <?php if ($message): ?>
    <div class="alert alert-<?php echo $type; ?> border-0 mt-4"><?php echo e($message); ?></div>
  <?php endif; ?>

  <?php if ($ticketInfo): ?>
    <div class="card-glass mt-3">
      <div class="text-muted">Nama Pengunjung</div>
      <div class="fw-semibold"><?php echo e($ticketInfo['nama_pengunjung']); ?></div>
      <div class="text-muted mt-2">Status</div>
      <div class="fw-semibold"><?php echo e($ticketInfo['status_checkin']); ?></div>
      <div class="text-muted mt-2">Waktu Check-in</div>
      <div class="fw-semibold"><?php echo $ticketInfo['waktu_checkin'] ? date('d M Y H:i', strtotime($ticketInfo['waktu_checkin'])) : '-'; ?></div>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../components/petugas_footer.php'; ?>
