<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
$pageTitle = $pageTitle ?? 'نظام إدارة الطلاب';
$notice = getFlash();
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | ALPHA_TECH</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="/PHP_MASTERCLASS_ALPHATECH/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
<div class="container-fluid px-lg-4">
<a class="navbar-brand fw-bold" href="/PHP_MASTERCLASS_ALPHATECH/"><span class="brand-mark">A</span> ALPHA_TECH <span class="brand-divider">|</span> PHP MASTERCLASS</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="mainNav"><div class="navbar-nav me-auto align-items-lg-center gap-lg-2">
<?php if (isLoggedIn()): ?>
<a class="nav-link" href="/PHP_MASTERCLASS_ALPHATECH/">لوحة التحكم</a>
<a class="nav-link" href="/PHP_MASTERCLASS_ALPHATECH/students/index.php">الطلاب</a>
<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><a class="nav-link" href="/PHP_MASTERCLASS_ALPHATECH/users/index.php">المستخدمون</a><a class="nav-link" href="/PHP_MASTERCLASS_ALPHATECH/attendance/index.php">الحضور والغياب</a><?php endif; ?>
<span class="navbar-text text-white-50 me-lg-2"><?= e($_SESSION['user_name'] ?? '') ?></span>
<form method="post" action="/PHP_MASTERCLASS_ALPHATECH/auth/logout.php" class="d-inline">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><button class="btn btn-outline-light btn-sm">تسجيل الخروج</button>
</form>
<?php else: ?><a class="nav-link" href="/PHP_MASTERCLASS_ALPHATECH/auth/login.php">تسجيل الدخول</a><?php endif; ?>
</div></div></div></nav>
<main class="container-fluid px-3 px-lg-4 py-4 app-main">
<?php if ($notice): ?><div class="alert alert-<?= e($notice['type']) ?> alert-dismissible fade show"><?= e($notice['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
