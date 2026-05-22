<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$user = current_user();
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

if ($nama === '' || $email === '') {
    flash_set('message', 'Nama dan email wajib diisi.');
    redirect('profile.php');
}

$check = $pdo->prepare("SELECT id_user FROM users WHERE email = :email AND id_user != :id LIMIT 1");
$check->execute(['email' => $email, 'id' => $user['id']]);
if ($check->fetch()) {
    flash_set('message', 'Email sudah digunakan akun lain.');
    redirect('profile.php');
}

$filename = upload_file('foto_profile', __DIR__ . '/../uploads/profiles', ['jpg', 'jpeg', 'png', 'webp']);
if ($filename === null) {
    $filename = $user['foto_profile'] ?? 'default.png';
}

$update = $pdo->prepare("UPDATE users SET nama = :nama, email = :email, no_hp = :no_hp, alamat = :alamat, foto_profile = :foto WHERE id_user = :id");
$update->execute([
    'nama' => $nama,
    'email' => $email,
    'no_hp' => $noHp,
    'alamat' => $alamat,
    'foto' => $filename,
    'id' => $user['id']
]);

$_SESSION['user']['nama'] = $nama;
$_SESSION['user']['email'] = $email;
$_SESSION['user']['no_hp'] = $noHp;
$_SESSION['user']['alamat'] = $alamat;
$_SESSION['user']['foto_profile'] = $filename;

flash_set('message', 'Profil berhasil diperbarui.');
redirect('profile.php');
