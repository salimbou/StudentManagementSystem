<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('رقم الطالب غير صالح.'); }
$stmt = $pdo->prepare("SELECT id,name,email,age,speciality,created_at FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { http_response_code(404); exit('الطالب غير موجود.'); }
$pageTitle = 'تفاصيل الطالب';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
<div><div class="eyebrow">STUDENT PROFILE</div><h1 class="h2 fw-bold mb-0">تفاصيل الطالب</h1></div>
<div><a href="index.php" class="btn btn-outline-secondary">العودة للقائمة</a><?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?> <a href="edit.php?id=<?= (int)$student['id'] ?>" class="btn btn-primary">تعديل البيانات</a> <a href="certificate.php?id=<?= (int)$student['id'] ?>" class="btn btn-outline-primary">شهادة PDF</a><?php endif; ?></div>
</div>
<div class="row justify-content-center"><div class="col-xl-8"><div class="profile-card">
<div class="profile-top"><div class="avatar-circle"><?= e(function_exists('mb_substr') ? mb_substr($student['name'], 0, 1, 'UTF-8') : substr($student['name'], 0, 1)) ?></div><div><h2 class="h4 mb-1"><?= e($student['name']) ?></h2><span class="text-white-50">رقم الطالب: #<?= (int)$student['id'] ?></span></div></div>
<div class="row g-0 profile-details">
<div class="col-md-6 detail-cell"><small>البريد الإلكتروني</small><strong><?= e($student['email']) ?></strong></div>
<div class="col-md-6 detail-cell"><small>العمر</small><strong><?= $student['age'] !== null ? e($student['age']) . ' سنة' : 'غير محدد' ?></strong></div>
<div class="col-md-6 detail-cell"><small>التخصص</small><strong><?= e($student['speciality'] ?: 'غير محدد') ?></strong></div>
<div class="col-md-6 detail-cell"><small>تاريخ التسجيل</small><strong><?= e($student['created_at']) ?></strong></div>
</div></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
