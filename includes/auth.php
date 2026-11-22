<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('warning', 'يرجى تسجيل الدخول أولاً.');
        redirect('/PHP_MASTERCLASS_ALPHATECH/auth/login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('ليس لديك صلاحية لتنفيذ هذه العملية.');
    }
}
