<?php
declare(strict_types=1);
require_once dirname(__DIR__,2) . '/config/database.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function api_response(int $status,array $data): never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
$header=$_SERVER['HTTP_AUTHORIZATION'] ?? '';
if(!preg_match('/Bearer\s+([A-Za-z0-9._-]{32,})/',$header,$m)) api_response(401,['error'=>'Bearer token required']);
$hash=hash('sha256',$m[1]);
$stmt=$pdo->prepare("SELECT id,role FROM users WHERE api_token_hash=?");$stmt->execute([$hash]);$apiUser=$stmt->fetch();
if(!$apiUser || $apiUser['role']!=='admin') api_response(401,['error'=>'Invalid API token']);
