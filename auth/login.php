<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

if (isset($_SESSION['user_id'])) {
    redirect('/PHP_MASTERCLASS_ALPHATECH/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id,name,email,password,role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        flash('success', 'مرحباً بك، تم تسجيل الدخول بنجاح.');
        redirect('/PHP_MASTERCLASS_ALPHATECH/');
    }
    $error = 'بيانات تسجيل الدخول غير صحيحة.';
}

$pageTitle = 'تسجيل الدخول';
require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-5">
<div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3 mb-3">تسجيل الدخول</h1>
<p class="text-muted">أدخل بيانات حسابك للوصول إلى النظام.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input class="form-control" type="email" name="email" autocomplete="username" required></div>
<div class="mb-3"><label class="form-label">كلمة المرور</label><input class="form-control" type="password" name="password" autocomplete="current-password" required></div>
<button class="btn btn-primary w-100">دخول</button>
</form>
</div></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
