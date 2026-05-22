<?php
require_once __DIR__ . '/../config/bootstrap.php';

$emailOrUsername = trim($_POST['email_or_username'] ?? '');
$password = $_POST['password'] ?? '';
$return = trim($_POST['return'] ?? '');

if ($emailOrUsername === '' || $password === '') {
    flash_set('message', 'Email dan password wajib diisi.');
    redirect('login.php');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username LIMIT 1");
$stmt->execute([
    'email' => $emailOrUsername,
    'username' => $emailOrUsername
]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    flash_set('message', 'Login gagal. Periksa email atau password.');
    redirect('login.php');
}

if ($user['status_akun'] !== 'aktif') {
    flash_set('message', 'Akun telah dinonaktifkan');
    redirect('login.php');
}

$_SESSION['user'] = [
    'id' => $user['id_user'],
    'nama' => $user['nama'],
    'email' => $user['email'],
    'role' => $user['role'],
    'foto_profile' => $user['foto_profile'],
    'no_hp' => $user['no_hp'],
    'alamat' => $user['alamat']
];

if ($return !== '') {
    redirect($return);
}

if ($user['role'] === 'admin') {
    redirect('admin/dashboard.php');
}

if ($user['role'] === 'petugas') {
    redirect('petugas/dashboard.php');
}

redirect('user/dashboard.php');
