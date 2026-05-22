<?php
require_once __DIR__ . '/../config/bootstrap.php';

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if ($nama === '' || $username === '' || $email === '' || $password === '') {
    flash_set('message', 'Semua field wajib diisi.');
    redirect('register.php');
}

if ($password !== $passwordConfirm) {
    flash_set('message', 'Konfirmasi password tidak cocok.');
    redirect('register.php');
}

$checkStmt = $pdo->prepare("SELECT id_user FROM users WHERE email = :email OR username = :username LIMIT 1");
$checkStmt->execute(['email' => $email, 'username' => $username]);
if ($checkStmt->fetch()) {
    flash_set('message', 'Email atau username sudah terdaftar.');
    redirect('register.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $pdo->prepare("INSERT INTO users (nama, username, email, password, role) VALUES (:nama, :username, :email, :password, 'user')");
$insert->execute([
    'nama' => $nama,
    'username' => $username,
    'email' => $email,
    'password' => $hash
]);

$id = $pdo->lastInsertId();
$_SESSION['user'] = [
    'id' => $id,
    'nama' => $nama,
    'email' => $email,
    'role' => 'user',
    'foto_profile' => 'default.png',
    'no_hp' => null,
    'alamat' => null
];

redirect('user/dashboard.php');
