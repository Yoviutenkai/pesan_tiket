<?php
$active = 'admin_vouchers';
require_once __DIR__ . '/../components/admin_header.php';

$vouchers = $pdo->query("SELECT * FROM voucher ORDER BY created_at DESC")->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Data Voucher</h5>
    <a class="btn btn-primary" href="<?php echo base_url('admin/voucher_form.php'); ?>">Tambah Voucher</a>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Diskon</th>
          <th>Kuota</th>
          <th>Terpakai</th>
          <th>Sisa</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$vouchers): ?>
          <tr><td colspan="5" class="text-muted">Belum ada voucher.</td></tr>
        <?php endif; ?>
        <?php foreach ($vouchers as $voucher): ?>
          <tr>
            <td><?php echo e($voucher['kode_voucher']); ?></td>
            <td><?php echo e($voucher['jenis_diskon']); ?> - <?php echo e($voucher['nilai_diskon']); ?></td>
            <?php
              $used = (int)$voucher['voucher_digunakan'];
              $quota = (int)$voucher['kuota_voucher'];
              $remaining = max(0, $quota - $used);
              $expired = $voucher['tanggal_expired'] && strtotime($voucher['tanggal_expired']) < strtotime(date('Y-m-d'));
              $statusLabel = $expired ? 'expired' : $voucher['status_voucher'];
            ?>
            <td><?php echo $quota; ?></td>
            <td><?php echo $used; ?></td>
            <td><?php echo $remaining; ?></td>
            <td><?php echo e($statusLabel); ?></td>
            <td>
              <a class="btn btn-sm btn-outline-warning" href="<?php echo base_url('admin/voucher_form.php?id=' . $voucher['id_voucher']); ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('admin/voucher_delete.php?id=' . $voucher['id_voucher']); ?>" onclick="return confirm('Hapus voucher ini?');">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
