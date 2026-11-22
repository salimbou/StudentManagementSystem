<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php'; require_admin();
$plain=$_SESSION['new_api_token']??null;
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$plain=bin2hex(random_bytes(32));$hash=hash('sha256',$plain);$st=$pdo->prepare("UPDATE users SET api_token_hash=? WHERE id=?");$st->execute([$hash,$_SESSION['user_id']]);$_SESSION['new_api_token']=$plain;}
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="card"><h1>إدارة رمز REST API</h1><p>أنشئ رمزاً جديداً لحساب المدير. يتم تخزين بصمة SHA-256 فقط في قاعدة البيانات، ويظهر الرمز الكامل مرة واحدة بعد إنشائه.</p>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn">إنشاء / تدوير API Token</button></form>
<?php if($plain): ?><p><b>انسخ الرمز الآن واحفظه في مكان آمن:</b></p><textarea style="width:100%;height:90px" readonly><?= e($plain) ?></textarea><?php unset($_SESSION['new_api_token']); endif; ?>
<p class="muted">لا تضع الرمز داخل JavaScript عام أو مستودع Git. استخدم HTTPS في بيئة الإنتاج.</p></section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
