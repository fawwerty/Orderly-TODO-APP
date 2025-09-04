<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';
header('Content-Type: application/json');

if(!current_user()){
  http_response_code(401);
  echo json_encode(['error'=>'Not authenticated']);
  exit;
}

$uid = current_user()['id'];
$action = $_GET['action'] ?? 'list';

function listTodos($pdo, $uid){
  $stmt = $pdo->prepare("SELECT id,title,description,due_date,priority,completed FROM todos WHERE user_id=? ORDER BY completed ASC, due_date IS NULL, due_date ASC, id DESC");
  $stmt->execute([$uid]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if($action==='list'){
  echo json_encode(['todos'=>listTodos($pdo, $uid)]);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

if($action==='create'){
  $title = trim($input['title'] ?? '');
  $priority = $input['priority'] ?? 'Medium';
  $due = $input['due_date'] ?? null;
  $desc = $input['description'] ?? null;
  if(!$title){
    http_response_code(400);
    echo json_encode(['error'=>'Title required']);
    exit;
  }
  $stmt = $pdo->prepare("INSERT INTO todos(user_id,title,description,due_date,priority) VALUES(?,?,?,?,?)");
  $stmt->execute([$uid,$title,$desc,$due,$priority]);
  echo json_encode(['ok'=>true,'todos'=>listTodos($pdo,$uid)]);
  exit;
}

if($action==='update'){
  $id = (int)($input['id'] ?? 0);
  if(!$id){ http_response_code(400); echo json_encode(['error'=>'Missing id']); exit; }
  $fields = ['title','description','due_date','priority','completed'];
  $set = []; $vals = [];
  foreach($fields as $f){
    if(array_key_exists($f,$input)){
      $set[] = "$f = ?";
      $vals[] = $input[$f];
    }
  }
  if(!$set){ echo json_encode(['ok'=>true,'todos'=>listTodos($pdo,$uid)]); exit; }
  $vals[] = $uid; $vals[] = $id;
  $sql = "UPDATE todos SET ".implode(',', $set)." WHERE user_id=? AND id=?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($vals);
  echo json_encode(['ok'=>true,'todos'=>listTodos($pdo,$uid)]);
  exit;
}

if($action==='delete'){
  $id = (int)($input['id'] ?? 0);
  if(!$id){ http_response_code(400); echo json_encode(['error'=>'Missing id']); exit; }
  $stmt = $pdo->prepare("DELETE FROM todos WHERE user_id=? AND id=?");
  $stmt->execute([$uid,$id]);
  echo json_encode(['ok'=>true,'todos'=>listTodos($pdo,$uid)]);
  exit;
}

http_response_code(400);
echo json_encode(['error'=>'Unknown action']);
