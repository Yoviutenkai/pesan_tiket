<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_admin();

$id = (int)($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? 'user');
$status = trim($_POST['status_akun'] ?? 'aktif');

$validRoles = ['admin', 'petugas', 'user'];
$validStatus = ['aktif', 'nonaktif'];

if ($id <= 0 || $nama === '' || $email === '') {
    flash_set('message', 'Data wajib diisi.');
    redirect('admin/users/index.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('message', 'Format email tidak valid.');
    redirect('admin/users/edit.php?id=' . $id);
}

if (!in_array($role, $validRoles, true) || !in_array($status, $validStatus, true)) {
    flash_set('message', 'Role atau status tidak valid.');
    redirect('admin/users/edit.php?id=' . $id);
}

$checkStmt = $pdo->prepare('SELECT id_user FROM users WHERE email = :email AND id_user <> :id LIMIT 1');
$checkStmt->execute(['email' => $email, 'id' => $id]);
if ($checkStmt->fetch()) {
    flash_set('message', 'Email sudah digunakan.');
    redirect('admin/users/edit.php?id=' . $id);
}

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $update = $pdo->prepare('UPDATE users SET nama = :nama, email = :email, password = :password, role = :role, status_akun = :status WHERE id_user = :id');
    $update->execute([
        'nama' => $nama,
        'email' => $email,
        'password' => $hash,
        'role' => $role,
        'status' => $status,
        'id' => $id
    ]);
} else {
    $update = $pdo->prepare('UPDATE users SET nama = :nama, email = :email, role = :role, status_akun = :status WHERE id_user = :id');
    $update->execute([
        'nama' => $nama,
        'email' => $email,
        'role' => $role,
        'status' => $status,
        'id' => $id
    ]);
}

flash_set('message', 'Akun berhasil diperbarui.');
redirect('admin/users/index.php');
