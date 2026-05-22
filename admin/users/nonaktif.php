<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_admin();

$id = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');

if ($id <= 0 || !in_array($status, ['aktif', 'nonaktif'], true)) {
    flash_set('message', 'Permintaan tidak valid.');
    redirect('admin/users/index.php');
}

if ($status === 'nonaktif' && current_user()['id'] === $id) {
    flash_set('message', 'Anda tidak bisa menonaktifkan akun sendiri.');
    redirect('admin/users/index.php');
}

$update = $pdo->prepare('UPDATE users SET status_akun = :status WHERE id_user = :id');
$update->execute(['status' => $status, 'id' => $id]);

flash_set('message', 'Status akun berhasil diperbarui.');
redirect('admin/users/index.php');
