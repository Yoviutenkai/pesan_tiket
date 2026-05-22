<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_petugas();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> - Petugas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/dashboard.css'); ?>" rel="stylesheet">
  </head>
  <body class="dashboard-body admin-theme">
    <div class="dashboard-shell">
      <?php require_once __DIR__ . '/dashboard_sidebar.php'; ?>
      <div class="dashboard-main">
        <?php require_once __DIR__ . '/dashboard_topbar.php'; ?>
        <main class="dashboard-content">
