<?php
$active = 'admin_tickets';
require_once __DIR__ . '/../components/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$ticket = null;
$events = $pdo->query("SELECT id_event, nama_event FROM event ORDER BY nama_event ASC")->fetchAll();

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM tiket WHERE id_tiket = :id");
    $stmt->execute(['id' => $id]);
    $ticket = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEvent = (int)($_POST['id_event'] ?? 0);
    $nama = trim($_POST['nama_tiket'] ?? '');
    $deskripsi = trim($_POST['deskripsi_tiket'] ?? '');
    $harga = (float)($_POST['harga'] ?? 0);
    $kuota = (int)($_POST['kuota'] ?? 0);
    $benefit = trim($_POST['benefit'] ?? '');
    $status = $_POST['status_tiket'] ?? 'available';

    if ($id > 0) {
        $update = $pdo->prepare("UPDATE tiket SET id_event = :id_event, nama_tiket = :nama, deskripsi_tiket = :deskripsi, harga = :harga, kuota = :kuota, benefit = :benefit, status_tiket = :status WHERE id_tiket = :id");
        $update->execute([
            'id_event' => $idEvent,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'kuota' => $kuota,
            'benefit' => $benefit,
            'status' => $status,
            'id' => $id
        ]);
    } else {
        $insert = $pdo->prepare("INSERT INTO tiket (id_event, nama_tiket, deskripsi_tiket, harga, kuota, benefit, status_tiket) VALUES (:id_event, :nama, :deskripsi, :harga, :kuota, :benefit, :status)");
        $insert->execute([
            'id_event' => $idEvent,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'kuota' => $kuota,
            'benefit' => $benefit,
            'status' => $status
        ]);
    }

    redirect('admin/tickets.php');
}
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3"><?php echo $id > 0 ? 'Edit Tiket' : 'Tambah Tiket'; ?></h5>
  <form method="post">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Event</label>
        <select class="form-select" name="id_event">
          <?php foreach ($events as $event): ?>
            <option value="<?php echo $event['id_event']; ?>" <?php echo ($ticket && $ticket['id_event'] == $event['id_event']) ? 'selected' : ''; ?>><?php echo e($event['nama_event']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nama Tiket</label>
        <input type="text" class="form-control" name="nama_tiket" value="<?php echo e($ticket['nama_tiket'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Harga</label>
        <input type="number" class="form-control" name="harga" value="<?php echo e($ticket['harga'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kuota</label>
        <input type="number" class="form-control" name="kuota" value="<?php echo e($ticket['kuota'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select class="form-select" name="status_tiket">
          <?php
            $statusList = ['available', 'sold_out'];
            $current = $ticket['status_tiket'] ?? 'available';
            foreach ($statusList as $statusOption):
          ?>
            <option value="<?php echo $statusOption; ?>" <?php echo $current === $statusOption ? 'selected' : ''; ?>><?php echo ucfirst($statusOption); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Benefit</label>
        <input type="text" class="form-control" name="benefit" value="<?php echo e($ticket['benefit'] ?? ''); ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi_tiket" rows="3"><?php echo e($ticket['deskripsi_tiket'] ?? ''); ?></textarea>
      </div>
    </div>
    <div class="mt-4 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-outline-light" href="<?php echo base_url('admin/tickets.php'); ?>">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
