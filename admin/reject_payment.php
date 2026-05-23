<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$orderId = (int)($_POST['order_id'] ?? 0);
$note = trim($_POST['catatan_admin'] ?? '');

if ($orderId <= 0) {
    redirect('admin/orders.php');
}

try {
    $pdo->beginTransaction();

    $paymentStmt = $pdo->prepare("SELECT p.* FROM payments p WHERE p.id_order = :id FOR UPDATE");
    $paymentStmt->execute(['id' => $orderId]);
    $payment = $paymentStmt->fetch();

    if (!$payment || !$payment['bukti_transfer']) {
        $pdo->rollBack();
        flash_set('message', 'Bukti transfer belum tersedia.');
        redirect('admin/order_detail.php?id=' . $orderId);
    }

    $verifUpdate = $pdo->prepare("UPDATE payments SET status_verifikasi = 'rejected', catatan_admin = :note, verified_by = :admin, verified_at = NOW() WHERE id_payment = :id");
    $verifUpdate->execute([
        'note' => $note,
        'admin' => current_user()['id'],
        'id' => $payment['id_payment']
    ]);

    $orderUpdate = $pdo->prepare("UPDATE orders SET status_pembayaran = 'rejected', status_order = 'dibatalkan' WHERE id_order = :id");
    $orderUpdate->execute(['id' => $orderId]);

    $pdo->commit();
    flash_set('message', 'Pembayaran ditolak.');
} catch (Exception $e) {
    $pdo->rollBack();
    flash_set('message', 'Gagal menolak pembayaran.');
}

redirect('admin/order_detail.php?id=' . $orderId);
