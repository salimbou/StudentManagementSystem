<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$errors = [];
$name = $email = '';
$role = 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($name === '') $errors[] = 'الاسم مطلوب.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح.';
    if (strlen($password) < 10) $errors[] = 'كلمة المرور يجب ألا تقل عن 10 أحرف.';
    if (!in_array($role, ['admin', 'user'], true)) $errors[] = 'الدور المحدد غير صالح.';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            flash('success', 'تم إنشاء المستخدم.');
            redirect('/PHP_MASTERCLASS_ALPHATECH/users/index.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $errors[] = 'البريد الإلكتروني مستخدم مسبقاً.';
            else { error_log($e->getMessage()); $errors[] = 'تعذر إنشاء المستخدم.'; }
        }
    }
}
$pageTitle = 'إضافة مستخدم';
require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-7">
<h1 class="h3 mb-3">إضافة مستخدم</h1>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="card card-body shadow-sm">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<div class="mb-3"><label class="form-label">الاسم</label><input class="form-control" name="name" value="<?= e($name) ?>" required></div>
<div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input class="form-control" type="email" name="email" value="<?= e($email) ?>" required></div>
<div class="mb-3"><label class="form-label">كلمة المرور</label><input class="form-control" type="password" name="password" minlength="10" required></div>
<div class="mb-3"><label class="form-label">الصلاحية</label><select class="form-select" name="role"><option value="user" <?= $role==='user'?'selected':'' ?>>مستخدم</option><option value="admin" <?= $role==='admin'?'selected':'' ?>>مسؤول</option></select></div>
<button class="btn btn-primary">إنشاء المستخدم</button>
</form></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
