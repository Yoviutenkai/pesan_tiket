<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_admin();

$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? 'user');
$status = trim($_POST['status_akun'] ?? 'aktif');

$validRoles = ['admin', 'petugas', 'user'];
$validStatus = ['aktif', 'nonaktif'];

if ($nama === '' || $email === '' || $password === '') {
    flash_set('message', 'Nama, email, dan password wajib diisi.');
    redirect('admin/users/create.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('message', 'Format email tidak valid.');
    redirect('admin/users/create.php');
}

if (!in_array($role, $validRoles, true) || !in_array($status, $validStatus, true)) {
    flash_set('message', 'Role atau status tidak valid.');
    redirect('admin/users/create.php');
}

$checkStmt = $pdo->prepare('SELECT id_user FROM users WHERE email = :email LIMIT 1');
$checkStmt->execute(['email' => $email]);
if ($checkStmt->fetch()) {
    flash_set('message', 'Email sudah digunakan.');
    redirect('admin/users/create.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $pdo->prepare('INSERT INTO users (nama, email, password, role, status_akun) VALUES (:nama, :email, :password, :role, :status)');
$insert->execute([
    'nama' => $nama,
    'email' => $email,
    'password' => $hash,
    'role' => $role,
    'status' => $status
]);

flash_set('message', 'Akun berhasil dibuat.');
redirect('admin/users/index.php');
