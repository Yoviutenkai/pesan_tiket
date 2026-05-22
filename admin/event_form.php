<?php
$active = 'admin_events';
require_once __DIR__ . '/../components/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$event = null;
$venues = $pdo->query("SELECT id_venue, nama_venue FROM venue ORDER BY nama_venue ASC")->fetchAll();
$flash = flash_get('message');

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM event WHERE id_event = :id");
    $stmt->execute(['id' => $id]);
    $event = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVenue = (int)($_POST['id_venue'] ?? 0);
    $nama = trim($_POST['nama_event'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $tglMulai = $_POST['tanggal_mulai'] ?? null;
    $tglSelesai = $_POST['tanggal_selesai'] ?? null;
    $status = $_POST['status_event'] ?? 'upcoming';

  $filename = null;
  $hasFile = isset($_FILES['banner_event']) && $_FILES['banner_event']['error'] !== UPLOAD_ERR_NO_FILE;
  if ($hasFile) {
    $filename = upload_file('banner_event', __DIR__ . '/../uploads/events', ['jpg', 'jpeg', 'png', 'webp'], 5000000);
    if ($filename === null) {
      flash_set('message', 'Upload banner gagal. Pastikan file JPG/PNG/WEBP dan ukuran <= 5MB.');
      $target = $id > 0 ? ('admin/event_form.php?id=' . $id) : 'admin/event_form.php';
      redirect($target);
    }
  }

    if ($id > 0) {
        $current = $event['banner_event'] ?? null;
        $filename = $filename ?: $current;
        $update = $pdo->prepare("UPDATE event SET id_venue = :id_venue, nama_event = :nama, kategori = :kategori, deskripsi = :deskripsi, organizer = :organizer, tanggal_mulai = :mulai, tanggal_selesai = :selesai, banner_event = :banner, status_event = :status WHERE id_event = :id");
        $update->execute([
            'id_venue' => $idVenue,
            'nama' => $nama,
            'kategori' => $kategori,
            'deskripsi' => $deskripsi,
            'organizer' => $organizer,
            'mulai' => $tglMulai,
            'selesai' => $tglSelesai,
            'banner' => $filename,
            'status' => $status,
            'id' => $id
        ]);
    } else {
        $insert = $pdo->prepare("INSERT INTO event (id_venue, nama_event, kategori, deskripsi, organizer, tanggal_mulai, tanggal_selesai, banner_event, status_event) VALUES (:id_venue, :nama, :kategori, :deskripsi, :organizer, :mulai, :selesai, :banner, :status)");
        $insert->execute([
            'id_venue' => $idVenue,
            'nama' => $nama,
            'kategori' => $kategori,
            'deskripsi' => $deskripsi,
            'organizer' => $organizer,
            'mulai' => $tglMulai,
            'selesai' => $tglSelesai,
            'banner' => $filename,
            'status' => $status
        ]);
    }

    redirect('admin/events.php');
}
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3"><?php echo $id > 0 ? 'Edit Event' : 'Tambah Event'; ?></h5>
  <?php if ($flash): ?>
    <div class="alert alert-warning border-0" role="alert"><?php echo e($flash); ?></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Venue</label>
        <select class="form-select" name="id_venue">
          <?php foreach ($venues as $venue): ?>
            <option value="<?php echo $venue['id_venue']; ?>" <?php echo ($event && $event['id_venue'] == $venue['id_venue']) ? 'selected' : ''; ?>><?php echo e($venue['nama_venue']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nama Event</label>
        <input type="text" class="form-control" name="nama_event" value="<?php echo e($event['nama_event'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <input type="text" class="form-control" name="kategori" value="<?php echo e($event['kategori'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Organizer</label>
        <input type="text" class="form-control" name="organizer" value="<?php echo e($event['organizer'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Mulai</label>
        <input type="datetime-local" class="form-control" name="tanggal_mulai" value="<?php echo e(isset($event['tanggal_mulai']) ? date('Y-m-d\TH:i', strtotime($event['tanggal_mulai'])) : ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal Selesai</label>
        <input type="datetime-local" class="form-control" name="tanggal_selesai" value="<?php echo e(isset($event['tanggal_selesai']) ? date('Y-m-d\TH:i', strtotime($event['tanggal_selesai'])) : ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select class="form-select" name="status_event">
          <?php
            $statusList = ['upcoming','ongoing','finished','cancelled'];
            $current = $event['status_event'] ?? 'upcoming';
            foreach ($statusList as $statusOption):
          ?>
            <option value="<?php echo $statusOption; ?>" <?php echo $current === $statusOption ? 'selected' : ''; ?>><?php echo ucfirst($statusOption); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Banner</label>
        <input type="file" class="form-control" name="banner_event" accept="image/*">
      </div>
      <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi" rows="3"><?php echo e($event['deskripsi'] ?? ''); ?></textarea>
      </div>
    </div>
    <div class="mt-4 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-outline-light" href="<?php echo base_url('admin/events.php'); ?>">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
