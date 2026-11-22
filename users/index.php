<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$users = $pdo->query("SELECT id,name,email,role,created_at FROM users ORDER BY id DESC")->fetchAll();
$pageTitle = 'إدارة المستخدمين';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
<h1 class="h3">إدارة المستخدمين</h1>
<a class="btn btn-primary" href="create.php">إضافة مستخدم</a>
</div>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table table-striped mb-0">
<thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>تاريخ الإنشاء</th></tr></thead>
<tbody>
<?php foreach ($users as $user): ?>
<tr><td><?= (int)$user['id'] ?></td><td><?= e($user['name']) ?></td><td><?= e($user['email']) ?></td><td><?= e($user['role']) ?></td><td><?= e($user['created_at']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
