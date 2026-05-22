<?php
$active = 'admin_venues';
require_once __DIR__ . '/../components/admin_header.php';

$venues = $pdo->query("SELECT * FROM venue ORDER BY created_at DESC")->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Data Venue</h5>
    <a class="btn btn-primary" href="<?php echo base_url('admin/venue_form.php'); ?>">Tambah Venue</a>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Kota</th>
          <th>Kapasitas</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$venues): ?>
          <tr><td colspan="4" class="text-muted">Belum ada venue.</td></tr>
        <?php endif; ?>
        <?php foreach ($venues as $venue): ?>
          <tr>
            <td><?php echo e($venue['nama_venue']); ?></td>
            <td><?php echo e($venue['kota']); ?></td>
            <td><?php echo (int)$venue['kapasitas']; ?></td>
            <td>
              <a class="btn btn-sm btn-outline-warning" href="<?php echo base_url('admin/venue_form.php?id=' . $venue['id_venue']); ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('admin/venue_delete.php?id=' . $venue['id_venue']); ?>" onclick="return confirm('Hapus venue ini?');">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
