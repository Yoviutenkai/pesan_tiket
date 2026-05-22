<?php
$active = 'user_tickets';
require_once __DIR__ . '/../components/user_header.php';

$userId = current_user()['id'];
$stmt = $pdo->prepare("SELECT a.*, o.kode_order FROM attendee a JOIN orders o ON a.id_order = o.id_order WHERE o.id_user = :id ORDER BY a.created_at DESC");
$stmt->execute(['id' => $userId]);
$tickets = $stmt->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Tiket Saya</h5>
    <a class="btn btn-outline-primary btn-sm" href="<?php echo base_url('events.php'); ?>">Pesan tiket</a>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode Tiket</th>
          <th>Status</th>
          <th>Check-in</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$tickets): ?>
          <tr><td colspan="4" class="text-muted">Belum ada tiket.</td></tr>
        <?php endif; ?>
        <?php foreach ($tickets as $ticket): ?>
          <tr>
            <td><?php echo e($ticket['kode_tiket']); ?></td>
            <td><?php echo e($ticket['status_checkin']); ?></td>
            <td><?php echo $ticket['waktu_checkin'] ? date('d M Y H:i', strtotime($ticket['waktu_checkin'])) : '-'; ?></td>
            <td><a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('ticket_download.php?id=' . $ticket['id_attendee']); ?>">Download</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/user_footer.php'; ?>
