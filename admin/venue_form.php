<?php
$active = 'admin_venues';
require_once __DIR__ . '/../components/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$venue = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM venue WHERE id_venue = :id");
    $stmt->execute(['id' => $id]);
    $venue = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_venue'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $kota = trim($_POST['kota'] ?? '');
    $kapasitas = (int)($_POST['kapasitas'] ?? 0);

    $filename = upload_file('gambar_venue', __DIR__ . '/../uploads/events', ['jpg', 'jpeg', 'png', 'webp']);

    if ($id > 0) {
        $currentImage = $venue['gambar_venue'] ?? null;
        $filename = $filename ?: $currentImage;
        $update = $pdo->prepare("UPDATE venue SET nama_venue = :nama, alamat = :alamat, kota = :kota, kapasitas = :kapasitas, gambar_venue = :gambar WHERE id_venue = :id");
        $update->execute([
            'nama' => $nama,
            'alamat' => $alamat,
            'kota' => $kota,
            'kapasitas' => $kapasitas,
            'gambar' => $filename,
            'id' => $id
        ]);
    } else {
        $insert = $pdo->prepare("INSERT INTO venue (nama_venue, alamat, kota, kapasitas, gambar_venue) VALUES (:nama, :alamat, :kota, :kapasitas, :gambar)");
        $insert->execute([
            'nama' => $nama,
            'alamat' => $alamat,
            'kota' => $kota,
            'kapasitas' => $kapasitas,
            'gambar' => $filename
        ]);
    }

    redirect('admin/venues.php');
}
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3"><?php echo $id > 0 ? 'Edit Venue' : 'Tambah Venue'; ?></h5>
  <form method="post" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama Venue</label>
        <input type="text" class="form-control" name="nama_venue" value="<?php echo e($venue['nama_venue'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kota</label>
        <input type="text" class="form-control" name="kota" value="<?php echo e($venue['kota'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kapasitas</label>
        <input type="number" class="form-control" name="kapasitas" value="<?php echo e($venue['kapasitas'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Gambar</label>
        <input type="file" class="form-control" name="gambar_venue" accept="image/*">
      </div>
      <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" name="alamat" rows="3"><?php echo e($venue['alamat'] ?? ''); ?></textarea>
      </div>
    </div>
    <div class="mt-4 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-outline-light" href="<?php echo base_url('admin/venues.php'); ?>">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../components/admin_footer.php'; ?>
