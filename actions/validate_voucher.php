<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_login();

header('Content-Type: application/json');

$code = trim($_POST['code'] ?? '');
$ticketId = (int)($_POST['ticket_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));

if ($code === '' || $ticketId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'Voucher tidak ditemukan.']);
    exit;
}

$ticketStmt = $pdo->prepare("SELECT harga FROM tiket WHERE id_tiket = :id LIMIT 1");
$ticketStmt->execute(['id' => $ticketId]);
$ticket = $ticketStmt->fetch();

if (!$ticket) {
    echo json_encode(['ok' => false, 'message' => 'Tiket tidak ditemukan.']);
    exit;
}

$subtotal = (float)$ticket['harga'] * $qty;

$voucherStmt = $pdo->prepare("SELECT * FROM voucher WHERE kode_voucher = :code AND status_voucher = 'aktif' LIMIT 1");
$voucherStmt->execute(['code' => $code]);
$voucher = $voucherStmt->fetch();

if (!$voucher) {
    echo json_encode(['ok' => false, 'message' => 'Voucher tidak valid.', 'discount' => 0, 'total' => $subtotal, 'total_label' => rupiah($subtotal)]);
    exit;
}

$today = date('Y-m-d');
if ($today < $voucher['tanggal_mulai'] || $today > $voucher['tanggal_expired']) {
    echo json_encode(['ok' => false, 'message' => 'Voucher expired atau belum berlaku.', 'discount' => 0, 'total' => $subtotal, 'total_label' => rupiah($subtotal)]);
    exit;
}

if ($subtotal < (float)$voucher['minimum_transaksi']) {
    echo json_encode(['ok' => false, 'message' => 'Minimum transaksi belum terpenuhi.', 'discount' => 0, 'total' => $subtotal, 'total_label' => rupiah($subtotal)]);
    exit;
}

if ((int)$voucher['voucher_digunakan'] >= (int)$voucher['kuota_voucher']) {
    echo json_encode(['ok' => false, 'message' => 'Voucher sudah habis.', 'discount' => 0, 'total' => $subtotal, 'total_label' => rupiah($subtotal)]);
    exit;
}

$discount = 0;
if ($voucher['jenis_diskon'] === 'persen') {
    $discount = $subtotal * ((float)$voucher['nilai_diskon'] / 100);
    if ((float)$voucher['maksimal_diskon'] > 0) {
        $discount = min($discount, (float)$voucher['maksimal_diskon']);
    }
} else {
    $discount = (float)$voucher['nilai_diskon'];
}

$discount = min($discount, $subtotal);
$total = $subtotal - $discount;

echo json_encode([
    'ok' => true,
    'message' => 'Voucher berhasil digunakan.',
    'discount' => $discount,
    'discount_label' => rupiah($discount),
    'total' => $total,
    'total_label' => rupiah($total)
]);
