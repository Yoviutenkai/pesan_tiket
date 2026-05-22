<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();
flash_set('message', 'Fitur check-in khusus petugas.');
redirect('admin/dashboard.php');
