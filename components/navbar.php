<?php
require_once __DIR__ . '/../config/bootstrap.php';
$navUser = current_user();
$ctaLink = is_logged_in() ? base_url('events.php') : base_url('login.php?return=events.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top nav-glass" id="mainNav">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?php echo base_url('index.php'); ?>">Eventa</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
        <li class="nav-item"><a class="nav-link" data-scroll href="<?php echo base_url('index.php#featured'); ?>">Featured</a></li>
        <li class="nav-item"><a class="nav-link" data-scroll href="<?php echo base_url('index.php#upcoming'); ?>">Upcoming</a></li>
        <li class="nav-item"><a class="nav-link" data-scroll href="<?php echo base_url('index.php#experience'); ?>">Experience</a></li>
        <li class="nav-item"><a class="nav-link" data-scroll href="<?php echo base_url('index.php#testimonial'); ?>">Testimonial</a></li>
      </ul>
      <div class="d-flex ms-lg-4 gap-2 mt-3 mt-lg-0">
        <?php if ($navUser): ?>
          <?php
            $dashboardLink = 'user/dashboard.php';
            if ($navUser['role'] === 'admin') {
                $dashboardLink = 'admin/dashboard.php';
            } elseif ($navUser['role'] === 'petugas') {
                $dashboardLink = 'petugas/dashboard.php';
            }
          ?>
          <a class="btn btn-outline-light btn-sm" href="<?php echo base_url($dashboardLink); ?>">Dashboard</a>
          <a class="btn btn-brand btn-sm" href="<?php echo base_url('logout.php'); ?>">Keluar</a>
        <?php else: ?>
          <a class="btn btn-outline-light btn-sm" href="<?php echo base_url('login.php'); ?>">Masuk</a>
          <a class="btn btn-brand btn-sm" href="<?php echo $ctaLink; ?>">Pesan Tiket</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
