<?php
$active = 'admin_users';
require_once __DIR__ . '/../../components/admin_header.php';
$flash = flash_get('message');
?>

<div class="card-glass">
  <h5 class="fw-semibold mb-3">Tambah Akun</h5>
  <?php if ($flash): ?>
    <div class="alert alert-warning border-0" role="alert"><?php echo e($flash); ?></div>
  <?php endif; ?>
  <form method="post" action="<?php echo base_url('admin/users/store.php'); ?>">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" name="nama" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <select class="form-select" name="role" required>
          <option value="admin">Admin</option>
          <option value="petugas">Petugas</option>
          <option value="user" selected>User</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status Akun</label>
        <select class="form-select" name="status_akun" required>
          <option value="aktif" selected>Aktif</option>
          <option value="nonaktif">Nonaktif</option>
        </select>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-light" href="<?php echo base_url('admin/users/index.php'); ?>">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../../components/admin_footer.php'; ?>
