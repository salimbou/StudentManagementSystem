<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('رقم الطالب غير صالح.'); }
$stmt = $pdo->prepare("SELECT id,name,speciality,created_at FROM students WHERE id=?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { http_response_code(404); exit('الطالب غير موجود.'); }
$issueDate = date('Y-m-d');
$certificateNo = 'AT-' . date('Y') . '-' . str_pad((string)$student['id'],5,'0',STR_PAD_LEFT);
?>
<!doctype html><html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>شهادة طالب - <?= e($student['name']) ?></title>
<style>
@page{size:A4 landscape;margin:12mm}
*{box-sizing:border-box}body{margin:0;background:#eef0f7;color:#1b2542;font-family:Tahoma,Arial,sans-serif}
.toolbar{max-width:1100px;margin:18px auto;text-align:center}
.toolbar button,.toolbar a{display:inline-block;padding:10px 18px;border:0;border-radius:8px;background:#6557d9;color:#fff;text-decoration:none;cursor:pointer;margin:0 4px}
.certificate{width:100%;max-width:1100px;min-height:690px;margin:20px auto;padding:42px;border:12px double #6557d9;background:#fff;text-align:center;display:flex;flex-direction:column;justify-content:space-between}
.brand{font-size:17px;font-weight:bold;letter-spacing:2px;color:#6557d9}h1{font-size:42px;margin:24px 0 12px;color:#172344}
.subtitle{font-size:21px;color:#66708a}.student{font-size:36px;font-weight:bold;color:#6557d9;margin:28px 0}
.details{font-size:18px;line-height:2.1}.footer{display:flex;justify-content:space-between;align-items:end;margin-top:38px;font-size:14px}
.line{border-top:1px solid #6f7890;padding-top:8px;min-width:190px}.meta{color:#7c8497;font-size:12px}
@media print{body{background:#fff}.toolbar{display:none}.certificate{margin:0 auto;max-width:none;min-height:180mm;break-inside:avoid}}
</style></head><body>
<div class="toolbar"><button onclick="window.print()">طباعة / حفظ PDF</button><a href="show.php?id=<?= (int)$student['id'] ?>">العودة لملف الطالب</a></div>
<main class="certificate">
<header><div class="brand">ALPHA_TECH</div><div class="meta">STUDENT MANAGEMENT SYSTEM • CERTIFICATE</div></header>
<section><h1>شهادة تسجيل طالب</h1><div class="subtitle">تشهد إدارة النظام بأن الطالب / الطالبة</div>
<div class="student"><?= e($student['name']) ?></div>
<div class="details">قد تم تسجيله/تسجيلها في النظام ضمن تخصص<br><strong><?= e($student['speciality'] ?: 'غير محدد') ?></strong></div></section>
<footer class="footer"><div><div class="line">تاريخ الإصدار: <?= e($issueDate) ?></div><div class="meta">رقم الشهادة: <?= e($certificateNo) ?></div></div><div class="line">الإدارة – ALPHA_TECH</div></footer>
</main></body></html>
