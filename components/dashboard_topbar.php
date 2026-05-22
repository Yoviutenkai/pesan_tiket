<?php
require_once __DIR__ . '/../config/bootstrap.php';
$user = current_user();
?>
<header class="topbar">
  <a class="btn btn-primary" href="<?php echo base_url('/index.php'); ?>"
       class="btn btn-icon">
        <i class="bi bi-arrow-left"></i>
      </button>
    </a>
  </button>
  <div class="topbar-actions">
    <button class="btn btn-icon position-relative">
      <i class="bi bi-bell"></i>
      <span class="dot-pulse"></span>
    </button>
    <div class="dropdown">
      <button class="btn btn-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="<?php echo base_url('uploads/profiles/' . e($user['foto_profile'] ?? 'default.png')); ?>" alt="Profile" onerror="this.src='<?php echo base_url('assets/img/profile-placeholder.svg'); ?>'">
        <span><?php echo e($user['nama'] ?? 'User'); ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="<?php echo base_url('profile.php'); ?>">Profil</a></li>
        <li><a class="dropdown-item" href="<?php echo base_url('logout.php'); ?>">Keluar</a></li>
      </ul>
    </div>
  </div>
</header>
