<?php
$active = 'admin_vouchers';
require_once __DIR__ . '/../components/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$voucher = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM voucher WHERE id_voucher = :id");
    $stmt->execute(['id' => $id]);
    $voucher = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = strtoupper(trim($_POST['kode_voucher'] ?? ''));
    $jenis = $_POST['jenis_diskon'] ?? 'persen';
    $nilai = (float)($_POST['nilai_diskon'] ?? 0);
    $min = (float)($_POST['minimum_transaksi'] ?? 0);
    $maks = (float)($_POST['maksimal_diskon'] ?? 0);
    $mulai = $_POST['tanggal_mulai'] ?? null;
    $expired = $_POST['tanggal_expired'] ?? null;
    $kuota = (int)($_POST['kuota_voucher'] ?? 0);
    $status = $_POST['status_voucher'] ?? 'aktif';

    if ($id > 0) {
        $update = $pdo->prepare("UPDATE voucher SET kode_voucher = :kode, jenis_diskon = :jenis, nilai_diskon = :nilai, minimum_transaksi = :min, maksimal_diskon = :maks, tanggal_mulai = :mulai, tanggal_expired = :expired, kuota_voucher = :kuota, status_voucher = :status WHERE id_voucher = :id");
        $update->execute([
            'kode' => $kode,
            'jenis' => $jenis,
            'nilai' => $nilai,
            'min' => $min,
            'maks' => $maks,
            'mulai' => $mulai,
            'expired' => $expired,
            'kuota' => $kuota,
            'status' => $status,
            'id' => $id
        ]);
    } else {
        $insert = $pdo->prepare("INSERT INTO voucher (kode_voucher, jenis_diskon, nilai_diskon, minimum_transaksi, maksimal_diskon, tanggal_mulai, tanggal_expired, kuota_voucher, status_voucher) VALUES (:kode, :jenis, :nilai, :min, :maks, :mulai, :expired, :kuota, :status)");
        $insert->execute([
            'kode' => $kode,
            'jenis' => $jenis,
            'nilai' => $nilai,
            'min' => $min,
            'maks' => $maks,
            'mulai' => $mulai,
            'expired' => $expired,
            'kuota' => $kuota,
            'status' => $status
        ]);
    }

    redirect('admin/vouchers.php');
}
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3"><?php echo $id > 0 ? 'Edit Voucher' : 'Tambah Voucher'; ?></h5>
  <form method="post">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Kode Voucher</label>
        <input type="text" class="form-control" name="kode_voucher" value="<?php echo e($voucher['kode_voucher'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Jenis Diskon</label>
        <select class="form-select" name="jenis_diskon">
          <option value="persen" <?php echo ($voucher['jenis_diskon'] ?? '') === 'persen' ? 'selected' : ''; ?>>Persen</option>
          <option value="nominal" <?php echo ($voucher['jenis_diskon'] ?? '') === 'nominal' ? 'selected' : ''; ?>>Nominal</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nilai Diskon</label>
        <input type="number" class="form-control" name="nilai_diskon" value="<?php echo e($voucher['nilai_diskon'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Minimum Transaksi</label>
        <input type="number" class="form-control" name="minimum_transaksi" value="<?php echo e($voucher['minimum_transaksi'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Maksimal Diskon</label>
        <input type="number" class="form-control" name="maksimal_diskon" value="<?php echo e($voucher['maksimal_diskon'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Kuota</label>
        <input type="number" class="form-control" name="kuota_voucher" value="<?php echo e($voucher['kuota_voucher'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" class="form-control" name="tanggal_mulai" value="<?php echo e($voucher['tanggal_mulai'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Expired</label>
        <input type="date" class="form-control" name="tanggal_expired" value="<?php echo e($voucher['tanggal_expired'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select class="form-select" name="status_voucher">
          <option value="aktif" <?php echo ($voucher['status_voucher'] ?? '') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
          <option value="nonaktif" <?php echo ($voucher['status_voucher'] ?? '') === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
        </select>
      </div>
    </div>
    <div class="mt-4 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-outline-light" href="<?php echo base_url('admin/vouchers.php'); ?>">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
