<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAttendanceToday = (int)$pdo->query("SELECT COUNT(*) FROM attendance WHERE attendance_date = CURDATE() AND status = 'present'")->fetchColumn();
$recent = $pdo->query("SELECT id,name,email,speciality,created_at FROM students ORDER BY id DESC LIMIT 6")->fetchAll();
$specialities = $pdo->query("SELECT COALESCE(NULLIF(speciality,''),'غير محدد') AS label, COUNT(*) AS total FROM students GROUP BY COALESCE(NULLIF(speciality,''),'غير محدد') ORDER BY total DESC LIMIT 8")->fetchAll();
$chartLabels = array_column($specialities, 'label');
$chartValues = array_map('intval', array_column($specialities, 'total'));
$pageTitle = 'لوحة التحكم';
require __DIR__ . '/includes/header.php';
?>
<div class="dashboard-heading d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
<div><div class="eyebrow">ALPHA_TECH / ADMINISTRATION</div><h1 class="h2 fw-bold mb-1">لوحة التحكم</h1><p class="text-muted mb-0">مرحباً <?= e($_SESSION['user_name'] ?? '') ?>، إليك ملخص النظام.</p></div>
<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><a class="btn btn-primary btn-lg" href="/PHP_MASTERCLASS_ALPHATECH/students/create.php">＋ إضافة طالب جديد</a><?php endif; ?>
</div>
<div class="row g-3 mb-4">
<div class="col-sm-6 col-xl-4"><div class="metric-card metric-purple"><div class="metric-icon">👨‍🎓</div><div class="metric-label">إجمالي الطلاب</div><div class="metric-value"><?= $totalStudents ?></div><div class="metric-foot">عدد السجلات المسجلة</div></div></div>
<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><div class="col-sm-6 col-xl-4"><div class="metric-card metric-blue"><div class="metric-icon">👥</div><div class="metric-label">المستخدمون</div><div class="metric-value"><?= $totalUsers ?></div><div class="metric-foot">حسابات النظام</div></div></div><?php endif; ?>
<div class="col-sm-6 col-xl-4"><div class="metric-card metric-blue"><div class="metric-icon">✅</div><div class="metric-label">الحاضرون اليوم</div><div class="metric-value"><?= $totalAttendanceToday ?></div><div class="metric-foot">حالات حضور مسجلة لليوم</div></div></div>
<div class="col-sm-6 col-xl-4"><div class="metric-card metric-green"><div class="metric-icon">📚</div><div class="metric-label">التخصصات</div><div class="metric-value"><?= count($specialities) ?></div><div class="metric-foot">تخصصات ظاهرة في الإحصاء</div></div></div>
</div>
<div class="row g-3 mb-4">
<div class="col-xl-7"><section class="panel-card h-100"><div class="panel-heading"><div><h2 class="h5 mb-1">توزيع الطلاب حسب التخصص</h2><p class="text-muted small mb-0">إحصائيات مباشرة من قاعدة البيانات</p></div></div><div class="chart-wrap"><?php if ($specialities): ?><canvas id="specialityChart" aria-label="توزيع الطلاب حسب التخصص"></canvas><?php else: ?><div class="empty-state">أضف بيانات الطلاب لعرض الإحصائيات.</div><?php endif; ?></div></section></div>
<div class="col-xl-5"><section class="panel-card h-100"><div class="panel-heading"><div><h2 class="h5 mb-1">روابط سريعة</h2><p class="text-muted small mb-0">الوظائف الرئيسية</p></div></div><div class="quick-links">
<a href="/PHP_MASTERCLASS_ALPHATECH/students/index.php"><span>📋</span><div><strong>قائمة الطلاب</strong><small>عرض وبحث في السجلات</small></div><b>←</b></a>
<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?><a href="/PHP_MASTERCLASS_ALPHATECH/students/create.php"><span>➕</span><div><strong>إضافة طالب</strong><small>تسجيل طالب جديد</small></div><b>←</b></a><a href="/PHP_MASTERCLASS_ALPHATECH/users/index.php"><span>⚙️</span><div><strong>إدارة المستخدمين</strong><small>حسابات النظام</small></div><b>←</b></a><?php endif; ?>
</div></section></div></div>
<section class="panel-card"><div class="panel-heading"><div><h2 class="h5 mb-1">أحدث الطلاب</h2><p class="text-muted small mb-0">آخر السجلات المضافة</p></div><a href="/PHP_MASTERCLASS_ALPHATECH/students/index.php" class="btn btn-sm btn-outline-primary">عرض الكل</a></div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>الطالب</th><th>البريد الإلكتروني</th><th>التخصص</th><th>تاريخ الإضافة</th><th>التفاصيل</th></tr></thead><tbody>
<?php foreach ($recent as $student): ?><tr><td class="fw-semibold"><?= e($student['name']) ?></td><td><?= e($student['email']) ?></td><td><span class="speciality-pill"><?= e($student['speciality'] ?: 'غير محدد') ?></span></td><td><?= e($student['created_at']) ?></td><td><a class="btn btn-sm btn-light" href="/PHP_MASTERCLASS_ALPHATECH/students/show.php?id=<?= (int)$student['id'] ?>">عرض</a></td></tr><?php endforeach; ?>
<?php if (!$recent): ?><tr><td colspan="5" class="text-center text-muted py-4">لا توجد بيانات بعد.</td></tr><?php endif; ?>
</tbody></table></div></section>
<script>
const specialityLabels = <?= json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const specialityValues = <?= json_encode($chartValues) ?>;
document.addEventListener('DOMContentLoaded', () => {
 const canvas = document.getElementById('specialityChart');
 if (canvas && window.Chart) new Chart(canvas, {type:'doughnut',data:{labels:specialityLabels,datasets:[{data:specialityValues,borderWidth:2,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',rtl:true}}}});
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
