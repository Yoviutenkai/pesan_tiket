<?php
require_once __DIR__ . '/config/bootstrap.php';

if (is_logged_in()) {
    $user = current_user();
  if ($user['role'] === 'admin') {
    redirect('admin/dashboard.php');
  }
  if ($user['role'] === 'petugas') {
    redirect('petugas/dashboard.php');
  }
  redirect('user/dashboard.php');
}

$flash = flash_get('message');
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Eventa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
  </head>
  <body>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>

    <main class="auth-page">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6">
            <div class="card auth-card border-0 shadow-soft">
              <div class="card-body p-4 p-lg-5">
                <h1 class="h3 fw-semibold mb-2">Daftar akun Eventa</h1>
                <p class="text-muted">Buat akun untuk memesan tiket premium.</p>
                <?php if ($flash): ?>
                  <div class="alert alert-warning border-0 shadow-sm" role="alert">
                    <?php echo e($flash); ?>
                  </div>
                <?php endif; ?>
                <form method="post" action="<?php echo base_url('actions/register_action.php'); ?>">
                  <div class="mb-3">
                    <label class="form-label">Nama lengkap</label>
                    <input type="text" class="form-control" name="nama" required>
                  </div>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Username</label>
                      <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" name="email" required>
                    </div>
                  </div>
                  <div class="row g-3 mt-1">
                    <div class="col-md-6">
                      <label class="form-label">Password</label>
                      <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Konfirmasi Password</label>
                      <input type="password" class="form-control" name="password_confirm" required>
                    </div>
                  </div>
                  <button class="btn btn-brand w-100 mt-4" type="submit">Daftar</button>
                </form>
                <div class="text-center mt-4">
                  <span class="text-muted">Sudah punya akun?</span>
                  <a class="fw-semibold text-decoration-none" href="<?php echo base_url('login.php'); ?>">Masuk sekarang</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <?php require_once __DIR__ . '/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
  </body>
</html>
