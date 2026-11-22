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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('رقم الطالب غير صالح.'); }

$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

flash($stmt->rowCount() ? 'success' : 'warning',
      $stmt->rowCount() ? 'تم حذف الطالب.' : 'لم يتم العثور على الطالب.');
redirect('/PHP_MASTERCLASS_ALPHATECH/students/index.php');
