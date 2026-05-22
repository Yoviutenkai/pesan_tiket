<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

$ticketId = (int)($_POST['ticket_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$voucherCode = trim($_POST['voucher_code'] ?? '');
$attendeeName = trim($_POST['attendee_name'] ?? '');
$attendeeEmail = trim($_POST['attendee_email'] ?? '');
$attendeePhone = trim($_POST['attendee_phone'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? 'transfer');

if ($ticketId <= 0 || $attendeeName === '' || $attendeeEmail === '' || $attendeePhone === '') {
    flash_set('message', 'Lengkapi data pemesan.');
    redirect('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);
}

$ticket = null;
$subtotal = 0;
$discount = 0;
$voucherId = null;

if ($voucherCode !== '') {
    $voucherStmt = $pdo->prepare("SELECT * FROM voucher WHERE kode_voucher = :code AND status_voucher = 'aktif' LIMIT 1");
    $voucherStmt->execute(['code' => $voucherCode]);
    $voucher = $voucherStmt->fetch();

    if (!$voucher) {
        flash_set('message', 'Voucher tidak valid.');
        redirect('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);
    }

    $today = date('Y-m-d');
    if ($today < $voucher['tanggal_mulai'] || $today > $voucher['tanggal_expired']) {
        flash_set('message', 'Voucher sudah expired atau belum berlaku.');
        redirect('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);
    }

    if ($subtotal < (float)$voucher['minimum_transaksi']) {
        flash_set('message', 'Minimum transaksi belum terpenuhi.');
        redirect('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);
    }

    if ((int)$voucher['voucher_digunakan'] >= (int)$voucher['kuota_voucher']) {
        flash_set('message', 'Voucher sudah habis.');
        redirect('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);
    }

    if ($voucher['jenis_diskon'] === 'persen') {
        $discount = $subtotal * ((float)$voucher['nilai_diskon'] / 100);
        if ((float)$voucher['maksimal_diskon'] > 0) {
            $discount = min($discount, (float)$voucher['maksimal_diskon']);
        }
    } else {
        $discount = (float)$voucher['nilai_diskon'];
    }

    $discount = min($discount, $subtotal);
    $voucherId = $voucher['id_voucher'];
}

    $adminFee = 0;
    $total = $subtotal - $discount;

try {
    $pdo->beginTransaction();

    $ticketStmt = $pdo->prepare("SELECT * FROM tiket WHERE id_tiket = :id FOR UPDATE");
    $ticketStmt->execute(['id' => $ticketId]);
    $ticket = $ticketStmt->fetch();

    if (!$ticket) {
        $pdo->rollBack();
        flash_set('message', 'Tiket tidak ditemukan.');
        redirect('events.php');
    }

    $available = (int)$ticket['kuota'] - (int)$ticket['tiket_terjual'];
    if ($available <= 0) {
        $pdo->rollBack();
        flash_set('message', 'Tiket sudah habis.');
        redirect('event_detail.php?id=' . $ticket['id_event']);
    }
    if ($qty > $available) {
        $pdo->rollBack();
        flash_set('message', 'Jumlah pembelian melebihi stok tiket.');
        redirect('event_detail.php?id=' . $ticket['id_event']);
    }

    $subtotal = (float)$ticket['harga'] * $qty;

    $kodeOrder = random_code('ORD', 10);
    $orderStmt = $pdo->prepare("INSERT INTO orders (id_user, id_voucher, kode_order, subtotal, diskon, biaya_admin, total_bayar, metode_pembayaran) VALUES (:id_user, :id_voucher, :kode, :subtotal, :diskon, :admin, :total, :metode)");
    $orderStmt->execute([
        'id_user' => current_user()['id'],
        'id_voucher' => $voucherId,
        'kode' => $kodeOrder,
        'subtotal' => $subtotal,
        'diskon' => $discount,
        'admin' => $adminFee,
        'total' => $total,
        'metode' => $paymentMethod
    ]);

    $orderId = (int)$pdo->lastInsertId();

    $detailStmt = $pdo->prepare("INSERT INTO order_detail (id_order, id_tiket, qty, harga, subtotal) VALUES (:id_order, :id_tiket, :qty, :harga, :subtotal)");
    $detailStmt->execute([
        'id_order' => $orderId,
        'id_tiket' => $ticketId,
        'qty' => $qty,
        'harga' => $ticket['harga'],
        'subtotal' => $subtotal
    ]);

    $newSold = (int)$ticket['tiket_terjual'] + $qty;
    $status = $newSold >= (int)$ticket['kuota'] ? 'sold_out' : 'available';
    $updateTicket = $pdo->prepare("UPDATE tiket SET tiket_terjual = :sold, status_tiket = :status WHERE id_tiket = :id");
    $updateTicket->execute(['sold' => $newSold, 'status' => $status, 'id' => $ticketId]);

    if ($voucherId) {
        $voucherUpdate = $pdo->prepare("UPDATE voucher SET voucher_digunakan = voucher_digunakan + 1 WHERE id_voucher = :id");
        $voucherUpdate->execute(['id' => $voucherId]);
    }

    $attendeeStmt = $pdo->prepare("INSERT INTO attendee (id_order, kode_tiket, nama_pengunjung, email_pengunjung, no_hp) VALUES (:id_order, :kode_tiket, :nama, :email, :no_hp)");
    for ($i = 0; $i < $qty; $i++) {
        $kodeTiket = random_code('TKT', 10);
        $attendeeStmt->execute([
            'id_order' => $orderId,
            'kode_tiket' => $kodeTiket,
            'nama' => $attendeeName,
            'email' => $attendeeEmail,
            'no_hp' => $attendeePhone
        ]);
    }

    $paymentStmt = $pdo->prepare("INSERT INTO payment (id_order, payment_method) VALUES (:id_order, :method)");
    $paymentStmt->execute(['id_order' => $orderId, 'method' => $paymentMethod]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    flash_set('message', 'Terjadi kesalahan saat membuat pesanan.');
    redirect('checkout.php?ticket_id=' . $ticketId . '&qty=' . $qty);
}

$_SESSION['last_order_id'] = $orderId;
redirect('order_success.php?id=' . $orderId);
