<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$date = $_GET['date'] ?? date('Y-m-d');
$parsed = DateTime::createFromFormat('Y-m-d', $date);
if (!$parsed || $parsed->format('Y-m-d') !== $date) {
 http_response_code(400);
 exit('تاريخ غير صالح.');
}
$stmt = $pdo->prepare("
 SELECT s.id,s.name,s.email,s.speciality,a.status,a.note
 FROM students s LEFT JOIN attendance a
 ON a.student_id=s.id AND a.attendance_date=?
 ORDER BY s.name
");
$stmt->execute([$date]);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="attendance-' . $date . '.csv"');
header('X-Content-Type-Options: nosniff');
$out = fopen('php://output','wb');
fwrite($out,"\xEF\xBB\xBF");
fputcsv($out,['رقم الطالب','اسم الطالب','البريد الإلكتروني','التخصص','التاريخ','الحالة','ملاحظة']);
$labels = ['present'=>'حاضر','absent'=>'غائب','late'=>'متأخر','excused'=>'غياب مبرر'];
$safe = static function ($value): string {
 $value = (string)($value ?? '');
 return preg_match('/^[\s]*[=+\-@]/u',$value) ? "'" . $value : $value;
};
while ($row = $stmt->fetch()) {
 fputcsv($out,[(int)$row['id'],$safe($row['name']),$safe($row['email']),$safe($row['speciality']),$date,$labels[$row['status']] ?? 'لم يسجل',$safe($row['note'])]);
}
fclose($out);
exit;
