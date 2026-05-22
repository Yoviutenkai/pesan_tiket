<?php
$active = 'petugas_attendee';
require_once __DIR__ . '/../components/petugas_header.php';

$attendees = $pdo->query("SELECT a.*, MIN(e.nama_event) AS nama_event
  FROM attendee a
  JOIN orders o ON o.id_order = a.id_order
  JOIN order_detail od ON od.id_order = o.id_order
  JOIN tiket t ON t.id_tiket = od.id_tiket
  JOIN event e ON e.id_event = t.id_event
  GROUP BY a.id_attendee
  ORDER BY a.created_at DESC")->fetchAll();
?>

<div class="card-glass">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold">Data Attendee</h5>
  </div>
  <div class="table-modern">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode Tiket</th>
          <th>Nama</th>
          <th>Event</th>
          <th>Status</th>
          <th>Check-in</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$attendees): ?>
          <tr><td colspan="5" class="text-muted">Belum ada attendee.</td></tr>
        <?php endif; ?>
        <?php foreach ($attendees as $row): ?>
          <tr>
            <td><?php echo e($row['kode_tiket']); ?></td>
            <td><?php echo e($row['nama_pengunjung']); ?></td>
            <td><?php echo e($row['nama_event']); ?></td>
            <td><?php echo e($row['status_checkin']); ?></td>
            <td><?php echo $row['waktu_checkin'] ? date('d M Y H:i', strtotime($row['waktu_checkin'])) : '-'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/petugas_footer.php'; ?>
