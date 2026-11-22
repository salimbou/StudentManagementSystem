<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE :term OR email LIKE :term OR speciality LIKE :term ORDER BY id DESC");
    $stmt->execute(['term' => "%{$search}%"]);
    $students = $stmt->fetchAll();
} else {
    $students = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();
}
$pageTitle = 'قائمة الطلاب';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><h1 class="h3 mb-0">قائمة الطلاب</h1><?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><a class="btn btn-primary" href="create.php">إضافة طالب</a><?php endif; ?></div>
<form method="get" class="card card-body mb-3"><div class="input-group"><input class="form-control" type="search" name="search" value="<?= e($search) ?>" placeholder="ابحث بالاسم أو البريد أو التخصص"><button class="btn btn-outline-primary">بحث</button><?php if ($search !== ''): ?><a class="btn btn-outline-secondary" href="index.php">مسح</a><?php endif; ?></div></form>
<div class="panel-card"><div class="table-responsive"><table class="table table-striped table-hover align-middle mb-0">
<thead><tr><th>#</th><th>الاسم</th><th>البريد الإلكتروني</th><th>العمر</th><th>التخصص</th><th>تاريخ الإضافة</th><th>التفاصيل</th><?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><th>الإجراءات</th><?php endif; ?></tr></thead><tbody>
<?php foreach ($students as $student): ?><tr>
<td><?= (int)$student['id'] ?></td><td><?= e($student['name']) ?></td><td><?= e($student['email']) ?></td><td><?= e($student['age']) ?></td><td><?= e($student['speciality']) ?></td><td><?= e($student['created_at']) ?></td>
<td><a class="btn btn-sm btn-light" href="show.php?id=<?= (int)$student['id'] ?>">عرض</a></td>
<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><td class="text-nowrap"><a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$student['id'] ?>">تعديل</a>
<form method="post" action="delete.php" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف الطالب؟');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id" value="<?= (int)$student['id'] ?>"><button class="btn btn-sm btn-danger">حذف</button></form></td><?php endif; ?>
</tr><?php endforeach; ?>
<?php if (!$students): ?><tr><td colspan="8" class="text-center text-muted py-4">لا توجد نتائج مطابقة.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
