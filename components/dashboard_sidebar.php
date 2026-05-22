<?php
require_once __DIR__ . '/../config/bootstrap.php';
$active = $active ?? '';
$role = current_user()['role'] ?? 'user';
?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <span class="brand-dot"></span>
    <div>
      <div class="brand-title">Eventa</div>
      <small class="text-muted">Dashboard</small>
    </div>
  </div>
  <nav class="sidebar-nav">
    <?php if ($role === 'admin'): ?>
      <a class="nav-link <?php echo $active === 'admin_dashboard' ? 'active' : ''; ?>" href="<?php echo base_url('admin/dashboard.php'); ?>"><i class="bi bi-speedometer2"></i>Dashboard</a>
      <a class="nav-link <?php echo $active === 'admin_venues' ? 'active' : ''; ?>" href="<?php echo base_url('admin/venues.php'); ?>"><i class="bi bi-geo-alt"></i>Venue</a>
      <a class="nav-link <?php echo $active === 'admin_events' ? 'active' : ''; ?>" href="<?php echo base_url('admin/events.php'); ?>"><i class="bi bi-calendar-event"></i>Event</a>
      <a class="nav-link <?php echo $active === 'admin_tickets' ? 'active' : ''; ?>" href="<?php echo base_url('admin/tickets.php'); ?>"><i class="bi bi-ticket-perforated"></i>Tiket</a>
      <a class="nav-link <?php echo $active === 'admin_vouchers' ? 'active' : ''; ?>" href="<?php echo base_url('admin/vouchers.php'); ?>"><i class="bi bi-gift"></i>Voucher</a>
      <a class="nav-link <?php echo $active === 'admin_orders' ? 'active' : ''; ?>" href="<?php echo base_url('admin/orders.php'); ?>"><i class="bi bi-receipt"></i>Transaksi</a>
      <a class="nav-link <?php echo $active === 'admin_users' ? 'active' : ''; ?>" href="<?php echo base_url('admin/users/index.php'); ?>"><i class="bi bi-people"></i>Users</a>
    <?php elseif ($role === 'petugas'): ?>
      <a class="nav-link <?php echo $active === 'petugas_dashboard' ? 'active' : ''; ?>" href="<?php echo base_url('petugas/dashboard.php'); ?>"><i class="bi bi-speedometer2"></i>Dashboard</a>
      <a class="nav-link <?php echo $active === 'petugas_checkin' ? 'active' : ''; ?>" href="<?php echo base_url('petugas/checkin.php'); ?>"><i class="bi bi-qr-code-scan"></i>Check-in Tiket</a>
      <a class="nav-link <?php echo $active === 'petugas_attendee' ? 'active' : ''; ?>" href="<?php echo base_url('petugas/attendee.php'); ?>"><i class="bi bi-people"></i>Data Attendee</a>
    <?php else: ?>
      <a class="nav-link <?php echo $active === 'user_dashboard' ? 'active' : ''; ?>" href="<?php echo base_url('user/dashboard.php'); ?>"><i class="bi bi-speedometer2"></i>Dashboard</a>
      <a class="nav-link <?php echo $active === 'user_orders' ? 'active' : ''; ?>" href="<?php echo base_url('user/orders.php'); ?>"><i class="bi bi-receipt"></i>Riwayat</a>
      <a class="nav-link <?php echo $active === 'user_tickets' ? 'active' : ''; ?>" href="<?php echo base_url('user/tickets.php'); ?>"><i class="bi bi-ticket-perforated"></i>Tiket Saya</a>
      <a class="nav-link <?php echo $active === 'user_profile' ? 'active' : ''; ?>" href="<?php echo base_url('profile.php'); ?>"><i class="bi bi-person"></i>Profil</a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-footer">
    <a class="btn btn-outline-light w-100" href="<?php echo base_url('logout.php'); ?>">Keluar</a>
  </div>
</aside>
