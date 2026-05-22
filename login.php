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

$return = $_GET['return'] ?? '';
$flash = flash_get('message');
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Eventa</title>
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
          <div class="col-lg-5">
            <div class="card auth-card border-0 shadow-soft">
              <div class="card-body p-4 p-lg-5">
                <h1 class="h3 fw-semibold mb-2">Masuk ke Eventa</h1>
                <p class="text-muted">Kelola tiket Anda dengan aman dan cepat.</p>
                <?php if ($flash): ?>
                  <div class="alert alert-warning border-0 shadow-sm" role="alert">
                    <?php echo e($flash); ?>
                  </div>
                <?php endif; ?>
                <form method="post" action="<?php echo base_url('actions/login_action.php'); ?>">
                  <input type="hidden" name="return" value="<?php echo e($return); ?>">
                  <div class="mb-3">
                    <label class="form-label">Email atau Username</label>
                    <input type="text" class="form-control" name="email_or_username" placeholder="nama@email.com" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Minimal 8 karakter" required>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="rememberMe">
                      <label class="form-check-label" for="rememberMe">Ingat saya</label>
                    </div>
                    <a class="small text-decoration-none" href="#">Lupa password?</a>
                  </div>
                  <button class="btn btn-brand w-100" type="submit">Masuk</button>
                </form>
                <div class="text-center mt-4">
                  <span class="text-muted">Belum punya akun?</span>
                  <a class="fw-semibold text-decoration-none" href="<?php echo base_url('register.php'); ?>">Daftar sekarang</a>
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
