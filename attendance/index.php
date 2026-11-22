<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$date = $_GET['date'] ?? date('Y-m-d');
$parsed = DateTime::createFromFormat('Y-m-d', $date);
if (!$parsed || $parsed->format('Y-m-d') !== $date) {
    $date = date('Y-m-d');
}
$stmt = $pdo->prepare("
 SELECT s.id,s.name,s.email,s.speciality,a.status,a.note
 FROM students s
 LEFT JOIN attendance a ON a.student_id=s.id AND a.attendance_date=?
 ORDER BY s.name
");
$stmt->execute([$date]);
$students = $stmt->fetchAll();

$pageTitle = 'الحضور والغياب';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
 <div><div class="eyebrow">ATTENDANCE MANAGEMENT</div><h1 class="h3 mb-0">الحضور والغياب</h1></div>
 <a class="btn btn-outline-primary" href="export.php?date=<?= e($date) ?>">تصدير CSV لبرنامج Excel</a>
</div>
<form method="get" class="panel-card p-3 mb-3">
 <label class="form-label" for="attendance-date">اختر التاريخ</label>
 <div class="input-group"><input class="form-control" id="attendance-date" type="date" name="date" value="<?= e($date) ?>" required><button class="btn btn-primary">عرض</button></div>
</form>
<div class="panel-card p-3">
 <div class="d-flex justify-content-between align-items-center mb-3"><strong>قائمة الطلاب – <?= e($date) ?></strong><span class="text-muted small">عدد الطلاب: <?= count($students) ?></span></div>
 <?php if (!$students): ?><div class="empty-state py-4">لا يوجد طلاب. أضف الطلاب أولاً.</div>
 <?php else: ?>
 <form method="post" action="save.php">
  <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
  <input type="hidden" name="attendance_date" value="<?= e($date) ?>">
  <div class="table-responsive"><table class="table table-hover align-middle">
   <thead><tr><th>الطالب</th><th>التخصص</th><th>الحالة</th><th>ملاحظة</th></tr></thead>
   <tbody>
   <?php foreach ($students as $student): ?>
   <tr>
    <td><strong><?= e($student['name']) ?></strong><div class="small text-muted"><?= e($student['email']) ?></div></td>
    <td><?= e($student['speciality']) ?></td>
    <td><select class="form-select" name="status[<?= (int)$student['id'] ?>]">
     <option value="">لم يسجل</option>
     <option value="present" <?= $student['status']==='present'?'selected':'' ?>>حاضر</option>
     <option value="absent" <?= $student['status']==='absent'?'selected':'' ?>>غائب</option>
     <option value="late" <?= $student['status']==='late'?'selected':'' ?>>متأخر</option>
     <option value="excused" <?= $student['status']==='excused'?'selected':'' ?>>غياب مبرر</option>
    </select></td>
    <td><input class="form-control" name="note[<?= (int)$student['id'] ?>]" maxlength="500" value="<?= e($student['note']) ?>" placeholder="اختياري"></td>
   </tr>
   <?php endforeach; ?>
   </tbody>
  </table></div>
  <button class="btn btn-primary">حفظ سجل الحضور</button>
 </form>
 <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
