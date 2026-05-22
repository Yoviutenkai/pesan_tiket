<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) {
    redirect('user/orders.php');
}

$stmt = $pdo->prepare("SELECT o.*, p.id_payment FROM orders o JOIN payment p ON o.id_order = p.id_order WHERE o.id_order = :id AND o.id_user = :user LIMIT 1");
$stmt->execute(['id' => $orderId, 'user' => current_user()['id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('user/orders.php');
}

$filename = upload_file('payment_proof', __DIR__ . '/../uploads/payments', ['jpg', 'jpeg', 'png', 'webp']);
if (!$filename) {
    flash_set('message', 'Upload gagal. Pastikan file gambar valid.');
    redirect('user/order_detail.php?id=' . $orderId);
}

$update = $pdo->prepare("UPDATE payment SET payment_proof = :proof, payment_status = 'pending', payment_date = NOW() WHERE id_payment = :id");
$update->execute(['proof' => $filename, 'id' => $order['id_payment']]);

flash_set('message', 'Bukti pembayaran berhasil diupload.');
redirect('user/order_detail.php?id=' . $orderId);
