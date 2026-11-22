<?php
require_once __DIR__ . '/auth.php';
$method=$_SERVER['REQUEST_METHOD'];
if($method==='GET'){
 $limit=min(100,max(1,(int)($_GET['limit']??20)));$offset=max(0,(int)($_GET['offset']??0));
 $st=$pdo->prepare("SELECT id,first_name,last_name,email,specialty,photo,created_at FROM students ORDER BY id DESC LIMIT ? OFFSET ?");
 $st->bindValue(1,$limit,PDO::PARAM_INT);$st->bindValue(2,$offset,PDO::PARAM_INT);$st->execute();
 api_response(200,['data'=>$st->fetchAll(),'limit'=>$limit,'offset'=>$offset]);
}
if($method==='POST'){
 $raw=file_get_contents('php://input');$data=json_decode($raw,true);
 if(!is_array($data))api_response(400,['error'=>'Valid JSON body required']);
 $first=trim((string)($data['first_name']??''));$last=trim((string)($data['last_name']??''));$email=trim((string)($data['email']??''));$specialty=trim((string)($data['specialty']??''));
 if($first===''||$last===''||$specialty===''||!filter_var($email,FILTER_VALIDATE_EMAIL))api_response(422,['error'=>'first_name, last_name, specialty and valid email are required']);
 try{$st=$pdo->prepare("INSERT INTO students(first_name,last_name,email,specialty) VALUES(?,?,?,?)");$st->execute([$first,$last,$email,$specialty]);api_response(201,['message'=>'Student created','id'=>(int)$pdo->lastInsertId()]);}
 catch(PDOException $e){api_response(409,['error'=>'Could not create student; email may already exist']);}
}
header('Allow: GET, POST');api_response(405,['error'=>'Method not allowed']);
