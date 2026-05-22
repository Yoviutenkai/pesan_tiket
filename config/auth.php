<?php
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function flash_set(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $msg = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function require_login(string $returnTo = ''): void
{
    if (!is_logged_in()) {
        $target = 'login.php';
        if ($returnTo !== '') {
            $target .= '?return=' . urlencode($returnTo);
        }
        redirect($target);
    }
}

function require_admin(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }

    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        if ($user && $user['role'] === 'petugas') {
            redirect('petugas/dashboard.php');
        }
        redirect('user/dashboard.php');
    }
}

function require_petugas(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }

    $user = current_user();
    if (!$user || $user['role'] !== 'petugas') {
        if ($user && $user['role'] === 'admin') {
            redirect('admin/dashboard.php');
        }
        redirect('user/dashboard.php');
    }
}

function require_user(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }

    $user = current_user();
    if (!$user || $user['role'] !== 'user') {
        if ($user && $user['role'] === 'admin') {
            redirect('admin/dashboard.php');
        }
        if ($user && $user['role'] === 'petugas') {
            redirect('petugas/dashboard.php');
        }
        redirect('login.php');
    }
}
