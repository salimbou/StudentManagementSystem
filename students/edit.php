<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('رقم الطالب غير صالح.'); }

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { http_response_code(404); exit('الطالب غير موجود.'); }

$errors = [];
$name = $student['name']; $email = $student['email'];
$age = (string)($student['age'] ?? ''); $speciality = (string)($student['speciality'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $speciality = trim($_POST['speciality'] ?? '');

    if ($name === '') $errors[] = 'الاسم مطلوب.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح.';
    if ($age !== '' && (!ctype_digit($age) || (int)$age < 1 || (int)$age > 120)) $errors[] = 'العمر يجب أن يكون بين 1 و120.';

    if (!$errors) {
        try {
            $update = $pdo->prepare("UPDATE students SET name=:name,email=:email,age=:age,speciality=:speciality WHERE id=:id");
            $update->execute([
                'name' => $name, 'email' => $email,
                'age' => $age === '' ? null : (int)$age,
                'speciality' => $speciality === '' ? null : $speciality,
                'id' => $id,
            ]);
            flash('success', 'تم تحديث بيانات الطالب.');
            redirect('/PHP_MASTERCLASS_ALPHATECH/students/index.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $errors[] = 'البريد الإلكتروني مستخدم لطالب آخر.';
            else { error_log($e->getMessage()); $errors[] = 'حدث خطأ أثناء التحديث.'; }
        }
    }
}
$pageTitle = 'تعديل طالب';
require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8">
<h1 class="h3 mb-3">تعديل بيانات الطالب</h1>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="card card-body shadow-sm">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<div class="mb-3"><label class="form-label">الاسم الكامل</label><input class="form-control" name="name" value="<?= e($name) ?>" required maxlength="150"></div>
<div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input class="form-control" type="email" name="email" value="<?= e($email) ?>" required maxlength="190"></div>
<div class="mb-3"><label class="form-label">العمر</label><input class="form-control" type="number" name="age" min="1" max="120" value="<?= e($age) ?>"></div>
<div class="mb-3"><label class="form-label">التخصص</label><input class="form-control" name="speciality" value="<?= e($speciality) ?>" maxlength="150"></div>
<div><button class="btn btn-primary">حفظ التعديلات</button> <a class="btn btn-secondary" href="index.php">إلغاء</a></div>
</form></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
