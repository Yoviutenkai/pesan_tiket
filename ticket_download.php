<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('user/tickets.php');
}

$stmt = $pdo->prepare("SELECT a.*, o.kode_order, e.nama_event, e.tanggal_mulai, v.nama_venue FROM attendee a JOIN orders o ON a.id_order = o.id_order JOIN order_detail od ON od.id_order = o.id_order JOIN tiket t ON t.id_tiket = od.id_tiket JOIN event e ON e.id_event = t.id_event LEFT JOIN venue v ON v.id_venue = e.id_venue WHERE a.id_attendee = :id AND o.id_user = :user LIMIT 1");
$stmt->execute(['id' => $id, 'user' => current_user()['id']]);
$ticket = $stmt->fetch();

if (!$ticket) {
    redirect('user/tickets.php');
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket - Eventa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { background: #0b0f1a; color: #fff; }
      .ticket-box { max-width: 640px; margin: 40px auto; border: 1px dashed rgba(255,255,255,0.2); border-radius: 18px; padding: 32px; background: #111827; }
      .code { font-size: 1.4rem; letter-spacing: 2px; }
    </style>
  </head>
  <body>
    <div class="ticket-box">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <div class="text-muted">Event</div>
          <h4 class="fw-semibold mb-0"><?php echo e($ticket['nama_event']); ?></h4>
        </div>
        <div class="text-end">
          <div class="text-muted">Kode tiket</div>
          <div class="code fw-semibold"><?php echo e($ticket['kode_tiket']); ?></div>
        </div>
      </div>
      <hr>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="text-muted">Nama</div>
          <div class="fw-semibold"><?php echo e($ticket['nama_pengunjung']); ?></div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">Venue</div>
          <div class="fw-semibold"><?php echo e($ticket['nama_venue']); ?></div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">Tanggal</div>
          <div class="fw-semibold"><?php echo date('d M Y', strtotime($ticket['tanggal_mulai'])); ?></div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">Order</div>
          <div class="fw-semibold"><?php echo e($ticket['kode_order']); ?></div>
        </div>
      </div>
      <div class="mt-4">
        <button class="btn btn-light" onclick="window.print()">Print tiket</button>
      </div>
    </div>
  </body>
</html>
