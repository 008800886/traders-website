<?php
require __DIR__.'/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $stmt=$pdo->query('SELECT id,name,category,price,old_price,stock,image_url,description FROM products WHERE active=1 ORDER BY id DESC');
  echo json_encode($stmt->fetchAll()); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data=json_decode(file_get_contents('php://input'),true) ?: [];
  $name=trim($data['name']??''); $category=trim($data['category']??'');
  $price=(float)($data['price']??0); $old=(float)($data['old_price']??0); $stock=(int)($data['stock']??0);
  $image=trim($data['image_url']??''); $description=trim($data['description']??'');
  if(!$name||!$category||$price<=0){http_response_code(400);echo json_encode(['success'=>false,'error'=>'Name, category and valid price are required.']);exit;}
  $s=$pdo->prepare('INSERT INTO products(name,category,price,old_price,stock,image_url,description,active) VALUES(?,?,?,?,?,?,?,1)');
  $s->execute([$name,$category,$price,$old?:null,$stock,$image,$description]);
  echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]); exit;
}
http_response_code(405); echo json_encode(['success'=>false,'error'=>'Method not allowed']);
?>
