<?php
$active = 'admin_users';
require_once __DIR__ . '/../../components/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('admin/users/index.php');
}

$stmt = $pdo->prepare('SELECT id_user, nama, email, role, status_akun FROM users WHERE id_user = :id');
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();
if (!$user) {
    redirect('admin/users/index.php');
}

$flash = flash_get('message');
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3">Edit Akun</h5>
  <?php if ($flash): ?>
    <div class="alert alert-warning border-0" role="alert"><?php echo e($flash); ?></div>
  <?php endif; ?>
  <form method="post" action="<?php echo base_url('admin/users/update.php'); ?>">
    <input type="hidden" name="id" value="<?php echo (int)$user['id_user']; ?>">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" name="nama" value="<?php echo e($user['nama']); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="<?php echo e($user['email']); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <select class="form-select" name="role" required>
          <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
          <option value="petugas" <?php echo $user['role'] === 'petugas' ? 'selected' : ''; ?>>Petugas</option>
          <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status Akun</label>
        <select class="form-select" name="status_akun" required>
          <option value="aktif" <?php echo $user['status_akun'] === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
          <option value="nonaktif" <?php echo $user['status_akun'] === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Password Baru (opsional)</label>
        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diganti">
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
      <a class="btn btn-light" href="<?php echo base_url('admin/users/index.php'); ?>">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../../components/admin_footer.php'; ?>
