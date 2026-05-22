<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM voucher WHERE id_voucher = :id");
    $stmt->execute(['id' => $id]);
}

redirect('admin/vouchers.php');
