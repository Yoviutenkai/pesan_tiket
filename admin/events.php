<?php
$active = 'admin_events';
require_once __DIR__ . '/../components/admin_header.php';

$statusFilter = $_GET['status'] ?? '';
$validStatuses = ['upcoming', 'ongoing', 'finished', 'cancelled'];
$whereSql = '';
$params = [];

if ($statusFilter !== '' && in_array($statusFilter, $validStatuses, true)) {
  $whereSql = ' WHERE e.status_event = :status';
  $params['status'] = $statusFilter;
} else {
  $statusFilter = '';
}

$stmt = $pdo->prepare("SELECT e.*, v.nama_venue FROM event e LEFT JOIN venue v ON e.id_venue = v.id_venue" . $whereSql . " ORDER BY e.created_at DESC");
foreach ($params as $key => $value) {
  $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$stmt->execute();
$events = $stmt->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <h5 class="fw-semibold mb-0">Data Event</h5>
    <div class="d-flex flex-wrap gap-2">
      <form method="get" class="d-flex gap-2">
        <select class="form-select" name="status" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <?php foreach ($validStatuses as $status): ?>
            <option value="<?php echo $status; ?>" <?php echo $statusFilter === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a class="btn btn-primary" href="<?php echo base_url('admin/event_form.php'); ?>">Tambah Event</a>
    </div>
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
          <?php
            $statusClass = match ($event['status_event']) {
                'ongoing' => 'bg-success',
                'finished' => 'bg-secondary',
                'cancelled' => 'bg-danger',
                default => 'bg-primary'
            };
          ?>
          <tr>
            <td><?php echo e($event['nama_event']); ?></td>
            <td><?php echo e($event['nama_venue']); ?></td>
            <td><span class="badge <?php echo $statusClass; ?>"><?php echo e($event['status_event']); ?></span></td>
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
