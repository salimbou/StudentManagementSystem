<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
 http_response_code(405);
 exit('Method Not Allowed');
}
verifyCsrf();

$date = $_POST['attendance_date'] ?? '';
$parsed = DateTime::createFromFormat('Y-m-d', $date);
if (!$parsed || $parsed->format('Y-m-d') !== $date) {
 http_response_code(400);
 exit('تاريخ غير صالح.');
}
$allowed = ['present','absent','late','excused'];
$statuses = $_POST['status'] ?? [];
$notes = $_POST['note'] ?? [];
if (!is_array($statuses) || !is_array($notes)) {
 http_response_code(400);
 exit('بيانات غير صالحة.');
}

$check = $pdo->prepare("SELECT id FROM students WHERE id=?");
$save = $pdo->prepare("
 INSERT INTO attendance (student_id,attendance_date,status,note,created_by)
 VALUES (?,?,?,?,?)
 ON DUPLICATE KEY UPDATE status=VALUES(status),note=VALUES(note),created_by=VALUES(created_by)
");
$pdo->beginTransaction();
try {
 foreach ($statuses as $studentId => $status) {
  $studentId = filter_var($studentId, FILTER_VALIDATE_INT);
  if (!$studentId || !in_array($status, $allowed, true)) continue;
  $check->execute([$studentId]);
  if (!$check->fetch()) continue;
  $note = trim((string)($notes[$studentId] ?? ''));
  if (function_exists('mb_substr')) $note = mb_substr($note, 0, 500, 'UTF-8');
  else $note = substr($note, 0, 500);
  $save->execute([$studentId,$date,$status,$note === '' ? null : $note,(int)$_SESSION['user_id']]);
 }
 $pdo->commit();
 flash('success','تم حفظ سجل الحضور والغياب.');
} catch (Throwable $e) {
 $pdo->rollBack();
 error_log($e->getMessage());
 flash('danger','تعذر حفظ السجل. راجع سجل الأخطاء.');
}
redirect('/PHP_MASTERCLASS_ALPHATECH/attendance/index.php?date=' . urlencode($date));
