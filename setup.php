<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

$tablesReady = false;
$adminExists = false;
$error = '';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        age TINYINT UNSIGNED NULL,
        speciality VARCHAR(150) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");


    $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        student_id INT UNSIGNED NOT NULL,
        attendance_date DATE NOT NULL,
        status ENUM('present','absent','late','excused') NOT NULL,
        note VARCHAR(500) NULL,
        created_by INT UNSIGNED NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_student_date (student_id, attendance_date),
        CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        CONSTRAINT fk_attendance_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_attendance_date (attendance_date)
    ) ENGINE=InnoDB");

    $tablesReady = true;
    $adminExists = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn() > 0;
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'تعذر تجهيز الجداول. تأكد من إنشاء قاعدة البيانات وإعدادات الاتصال.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tablesReady && !$adminExists) {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'أدخل اسماً وبريداً إلكترونياً صحيحاً.';
    } elseif (strlen($password) < 10) {
        $error = 'يجب ألا تقل كلمة المرور عن 10 أحرف.';
    } elseif ($password !== $confirm) {
        $error = 'تأكيد كلمة المرور غير مطابق.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'admin')");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'تم إنشاء حساب المسؤول. احذف setup.php الآن ثم سجّل الدخول.');
            redirect('/PHP_MASTERCLASS_ALPHATECH/auth/login.php');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'تعذر إنشاء الحساب. ربما البريد الإلكتروني مستخدم مسبقاً.';
        }
    }
}

$pageTitle = 'إعداد المشروع';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card shadow-sm">
<div class="card-body p-4">
<h1 class="h3 mb-3">إعداد Student Management System</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (!$tablesReady): ?>
<p>لم تكتمل تهيئة قاعدة البيانات. راجع ملف config/database.php.</p>
<?php elseif ($adminExists): ?>
<div class="alert alert-success">يوجد حساب مسؤول بالفعل. احذف setup.php من المشروع.</div>
<a class="btn btn-primary" href="/PHP_MASTERCLASS_ALPHATECH/auth/login.php">الانتقال لتسجيل الدخول</a>
<?php else: ?>
<p>أنشئت الجداول بنجاح. أنشئ حساب المسؤول الأول.</p>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<div class="mb-3"><label class="form-label">اسم المسؤول</label><input class="form-control" name="name" required></div>
<div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input class="form-control" type="email" name="email" required></div>
<div class="mb-3"><label class="form-label">كلمة المرور (10 أحرف على الأقل)</label><input class="form-control" type="password" name="password" minlength="10" required></div>
<div class="mb-3"><label class="form-label">تأكيد كلمة المرور</label><input class="form-control" type="password" name="confirm_password" minlength="10" required></div>
<button class="btn btn-primary">إنشاء المسؤول</button>
</form>
<?php endif; ?>
</div></div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
