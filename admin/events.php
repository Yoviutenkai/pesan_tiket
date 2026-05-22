<?php
$active = 'admin_events';
require_once __DIR__ . '/../components/admin_header.php';

$events = $pdo->query("SELECT e.*, v.nama_venue FROM event e LEFT JOIN venue v ON e.id_venue = v.id_venue ORDER BY e.created_at DESC")->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Data Event</h5>
    <a class="btn btn-primary" href="<?php echo base_url('admin/event_form.php'); ?>">Tambah Event</a>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Venue</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$events): ?>
          <tr><td colspan="4" class="text-muted">Belum ada event.</td></tr>
        <?php endif; ?>
        <?php foreach ($events as $event): ?>
          <tr>
            <td><?php echo e($event['nama_event']); ?></td>
            <td><?php echo e($event['nama_venue']); ?></td>
            <td><?php echo e($event['status_event']); ?></td>
            <td>
              <a class="btn btn-sm btn-outline-warning" href="<?php echo base_url('admin/event_form.php?id=' . $event['id_event']); ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('admin/event_delete.php?id=' . $event['id_event']); ?>" onclick="return confirm('Hapus event ini?');">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
