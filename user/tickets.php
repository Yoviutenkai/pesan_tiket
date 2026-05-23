<?php
$active = 'user_tickets';
require_once __DIR__ . '/../components/user_header.php';

$userId = current_user()['id'];
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM attendee a JOIN orders o ON a.id_order = o.id_order WHERE o.id_user = :id");
$countStmt->execute(['id' => $userId]);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$stmt = $pdo->prepare("SELECT a.*, o.kode_order FROM attendee a JOIN orders o ON a.id_order = o.id_order WHERE o.id_user = :id ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
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
  <div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Total: <?php echo $totalRows; ?> tiket</small>
    <nav>
      <ul class="pagination mb-0">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo base_url('user/tickets.php?page=' . $i); ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>
</div>

<?php require_once __DIR__ . '/../components/user_footer.php'; ?>
