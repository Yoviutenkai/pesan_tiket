<?php
require_once __DIR__ . '/app.php';

function base_url(string $path = ''): string
{
    $url = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    return str_replace(' ', '%20', $url);
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function rupiah($amount): string
{
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

function random_code(string $prefix, int $length = 8): string
{
    $bytes = bin2hex(random_bytes($length));
    $code = strtoupper(substr($bytes, 0, $length));
    return $prefix . $code;
}

function upload_file(string $field, string $destDir, array $allowedExts, int $maxSize = 2000000): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['size'] > $maxSize) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        return null;
    }

    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return null;
    }

    return $name;
}
