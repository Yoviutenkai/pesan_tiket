<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) {
    redirect('admin/orders.php');
}

try {
    $pdo->beginTransaction();

    $paymentStmt = $pdo->prepare("SELECT p.*, o.id_user, o.status_pembayaran, o.status_order FROM payments p JOIN orders o ON o.id_order = p.id_order WHERE p.id_order = :id FOR UPDATE");
    $paymentStmt->execute(['id' => $orderId]);
    $payment = $paymentStmt->fetch();

    if (!$payment || !$payment['bukti_transfer']) {
        $pdo->rollBack();
        flash_set('message', 'Bukti transfer belum tersedia.');
        redirect('admin/order_detail.php?id=' . $orderId);
    }

    if (in_array($payment['status_pembayaran'], ['rejected', 'expired', 'cancelled'], true)) {
        $pdo->rollBack();
        flash_set('message', 'Order tidak dapat diverifikasi.');
        redirect('admin/order_detail.php?id=' . $orderId);
    }

    $verifUpdate = $pdo->prepare("UPDATE payments SET status_verifikasi = 'approved', verified_by = :admin, verified_at = NOW() WHERE id_payment = :id");
    $verifUpdate->execute(['admin' => current_user()['id'], 'id' => $payment['id_payment']]);

    $orderUpdate = $pdo->prepare("UPDATE orders SET status_pembayaran = 'paid', status_order = 'berhasil' WHERE id_order = :id");
    $orderUpdate->execute(['id' => $orderId]);

    $detailStmt = $pdo->prepare("SELECT id_tiket, qty FROM order_detail WHERE id_order = :id");
    $detailStmt->execute(['id' => $orderId]);
    $details = $detailStmt->fetchAll();

    $existsStmt = $pdo->prepare("SELECT COUNT(*) FROM attendee WHERE id_order = :id");
    $existsStmt->execute(['id' => $orderId]);
    $hasAttendee = (int)$existsStmt->fetchColumn() > 0;

    if (!$hasAttendee) {
        $attendeeStmt = $pdo->prepare("INSERT INTO attendee (id_order, kode_tiket, nama_pengunjung, email_pengunjung, no_hp) VALUES (:id_order, :kode_tiket, :nama, :email, :no_hp)");

        $userStmt = $pdo->prepare("SELECT nama, email, no_hp FROM users WHERE id_user = :id");
        $userStmt->execute(['id' => $payment['id_user']]);
        $user = $userStmt->fetch();

        foreach ($details as $detail) {
            for ($i = 0; $i < (int)$detail['qty']; $i++) {
                $attendeeStmt->execute([
                    'id_order' => $orderId,
                    'kode_tiket' => random_code('TKT', 10),
                    'nama' => $user['nama'] ?? 'Pengunjung',
                    'email' => $user['email'] ?? '',
                    'no_hp' => $user['no_hp'] ?? ''
                ]);
            }
        }
    }

    $pdo->commit();
    flash_set('message', 'Pembayaran disetujui dan tiket dibuat.');
} catch (Exception $e) {
    $pdo->rollBack();
    flash_set('message', 'Gagal memverifikasi pembayaran.');
}

redirect('admin/order_detail.php?id=' . $orderId);
