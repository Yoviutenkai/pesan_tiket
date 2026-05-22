<?php
$active = 'petugas_dashboard';
require_once __DIR__ . '/../components/petugas_header.php';

$totalStmt = $pdo->query("SELECT COUNT(*) FROM attendee");
$totalAttendee = (int)$totalStmt->fetchColumn();

$checkedStmt = $pdo->query("SELECT COUNT(*) FROM attendee WHERE status_checkin = 'sudah_hadir'");
$checkedAttendee = (int)$checkedStmt->fetchColumn();

$todayStmt = $pdo->query("SELECT COUNT(*) FROM attendee WHERE DATE(waktu_checkin) = CURDATE()");
$todayCheckin = (int)$todayStmt->fetchColumn();

$recentStmt = $pdo->query("SELECT a.kode_tiket, a.nama_pengunjung, a.waktu_checkin, MIN(e.nama_event) AS nama_event
  FROM attendee a
  JOIN orders o ON o.id_order = a.id_order
  JOIN order_detail od ON od.id_order = o.id_order
  JOIN tiket t ON t.id_tiket = od.id_tiket
  JOIN event e ON e.id_event = t.id_event
  WHERE a.status_checkin = 'sudah_hadir'
  GROUP BY a.id_attendee, a.kode_tiket, a.nama_pengunjung, a.waktu_checkin
  ORDER BY a.waktu_checkin DESC
  LIMIT 5")->fetchAll();
?>

<div class="card-glass mb-4">
  <h5 class="fw-semibold">Dashboard Petugas</h5>
  <p class="text-muted">Pantau check-in attendee secara cepat dan akurat.</p>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-people"></i></div>
      <div>
        <div class="stat-meta">Total Attendee</div>
        <div class="stat-value"><?php echo $totalAttendee; ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
      <div>
        <div class="stat-meta">Sudah Check-in</div>
        <div class="stat-value"><?php echo $checkedAttendee; ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
      <div>
        <div class="stat-meta">Check-in Hari Ini</div>
        <div class="stat-value"><?php echo $todayCheckin; ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card-glass mt-4">
  <h5 class="fw-semibold">Check-in Terbaru</h5>
  <div class="table-modern mt-3">
    <table class="table table-borderless align-middle mb-0">
      <thead>
        <tr>
          <th>Kode Tiket</th>
          <th>Nama</th>
          <th>Event</th>
          <th>Waktu</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$recentStmt): ?>
          <tr><td colspan="4" class="text-muted">Belum ada check-in.</td></tr>
        <?php endif; ?>
        <?php foreach ($recentStmt as $row): ?>
          <tr>
            <td><?php echo e($row['kode_tiket']); ?></td>
            <td><?php echo e($row['nama_pengunjung']); ?></td>
            <td><?php echo e($row['nama_event']); ?></td>
            <td><?php echo $row['waktu_checkin'] ? date('d M Y H:i', strtotime($row['waktu_checkin'])) : '-'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../components/petugas_footer.php'; ?>
