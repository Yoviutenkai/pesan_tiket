<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();

$user = current_user();
$flash = flash_get('message');
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil - Eventa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/dashboard.css'); ?>" rel="stylesheet">
  </head>
  <body class="dashboard-body">
    <div class="dashboard-shell">
      <?php $active = 'user_profile'; require_once __DIR__ . '/components/dashboard_sidebar.php'; ?>
      <div class="dashboard-main">
        <?php require_once __DIR__ . '/components/dashboard_topbar.php'; ?>
        <main class="dashboard-content">
          <div class="card-glass">
            <h4 class="fw-semibold mb-3">Profil Saya</h4>
            <?php if ($flash): ?>
              <div class="alert alert-warning border-0" role="alert"><?php echo e($flash); ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo base_url('actions/profile_update.php'); ?>" enctype="multipart/form-data">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nama</label>
                  <input type="text" class="form-control" name="nama" value="<?php echo e($user['nama'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">No HP</label>
                  <input type="text" class="form-control" name="no_hp" value="<?php echo e($user['no_hp'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" value="<?php echo e($user['email'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Foto Profile</label>
                  <input type="file" class="form-control" name="foto_profile" accept="image/*">
                </div>
                <div class="col-12">
                  <label class="form-label">Alamat</label>
                  <textarea class="form-control" name="alamat" rows="3"><?php echo e($user['alamat'] ?? ''); ?></textarea>
                </div>
              </div>
              <button class="btn btn-primary mt-3" type="submit">Simpan Perubahan</button>
            </form>
          </div>
        </main>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('assets/js/dashboard.js'); ?>"></script>
  </body>
</html>
