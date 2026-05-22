<?php
$active = 'admin_tickets';
require_once __DIR__ . '/../components/admin_header.php';

$tickets = $pdo->query("SELECT t.*, e.nama_event FROM tiket t JOIN event e ON t.id_event = e.id_event ORDER BY t.created_at DESC")->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Data Tiket</h5>
    <a class="btn btn-primary" href="<?php echo base_url('admin/ticket_form.php'); ?>">Tambah Tiket</a>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Event</th>
          <th>Nama</th>
          <th>Harga</th>
          <th>Kuota</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$tickets): ?>
          <tr><td colspan="5" class="text-muted">Belum ada tiket.</td></tr>
        <?php endif; ?>
        <?php foreach ($tickets as $ticket): ?>
          <tr>
            <td><?php echo e($ticket['nama_event']); ?></td>
            <td><?php echo e($ticket['nama_tiket']); ?></td>
            <td><?php echo rupiah($ticket['harga']); ?></td>
            <td><?php echo (int)$ticket['kuota']; ?></td>
            <td>
              <a class="btn btn-sm btn-outline-warning" href="<?php echo base_url('admin/ticket_form.php?id=' . $ticket['id_tiket']); ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('admin/ticket_delete.php?id=' . $ticket['id_tiket']); ?>" onclick="return confirm('Hapus tiket ini?');">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
