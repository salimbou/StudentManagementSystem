<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (!empty($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/index.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $username=trim($_POST['username'] ?? ''); $password=$_POST['password'] ?? '';
  $stmt=$pdo->prepare("SELECT id,username,password_hash,role FROM users WHERE username=?");
  $stmt->execute([$username]); $u=$stmt->fetch();
  if ($u && password_verify($password,$u['password_hash'])) {
    session_regenerate_id(true); $_SESSION['user_id']=$u['id']; $_SESSION['username']=$u['username']; $_SESSION['role']=$u['role'];
    header('Location: ' . BASE_URL . '/index.php'); exit;
  } else $error='اسم المستخدم أو كلمة المرور غير صحيحة.';
}
?>
<!doctype html><html lang="ar" dir="rtl"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>تسجيل الدخول</title><body style="font-family:Tahoma;background:#f3f5f9">
<div style="max-width:420px;margin:70px auto;background:white;padding:28px;border-radius:12px">
<h2>تسجيل الدخول</h2><?php if($error): ?><p style="color:#b83232"><?= e($error) ?></p><?php endif; ?>
<form method="post"><label>اسم المستخدم</label><input name="username" required>
<label>كلمة المرور</label><input type="password" name="password" required>
<button style="padding:10px 18px;background:#1769aa;color:white;border:0;border-radius:6px">دخول</button></form></div></body></html>
