<?php
require __DIR__.'/config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false]); exit; }
$data=json_decode(file_get_contents('php://input'),true) ?: [];
$name=trim($data['name']??''); $phone=trim($data['phone']??''); $city=trim($data['city']??''); $address=trim($data['address']??''); $notes=trim($data['notes']??''); $items=$data['items']??[];
if(!$name||!$phone||!$city||!$address||!is_array($items)||!count($items)){http_response_code(400);echo json_encode(['success'=>false,'error'=>'Complete checkout details are required.']);exit;}
try {
 $pdo->beginTransaction(); $total=0; $clean=[];
 foreach($items as $item){$id=(int)($item['id']??0);$qty=max(1,(int)($item['qty']??1));$s=$pdo->prepare('SELECT id,name,price,stock FROM products WHERE id=? AND active=1 FOR UPDATE');$s->execute([$id]);$p=$s->fetch();if(!$p||$p['stock']<$qty)throw new Exception('A product is out of stock.');$total+=(float)$p['price']*$qty;$clean[]=['id'=>$id,'name'=>$p['name'],'price'=>(float)$p['price'],'qty'=>$qty];}
 $s=$pdo->prepare('INSERT INTO orders(customer_name,phone,city,address,notes,total,status) VALUES(?,?,?,?,?,?,?)');$s->execute([$name,$phone,$city,$address,$notes,$total,'Pending']);$oid=$pdo->lastInsertId();
 foreach($clean as $i){$s=$pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,price,qty) VALUES(?,?,?,?,?)');$s->execute([$oid,$i['id'],$i['name'],$i['price'],$i['qty']]);$pdo->prepare('UPDATE products SET stock=stock-? WHERE id=?')->execute([$i['qty'],$i['id']]);}
 $pdo->commit(); echo json_encode(['success'=>true,'order_id'=>(int)$oid,'total'=>$total]);
} catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();http_response_code(400);echo json_encode(['success'=>false,'error'=>$e->getMessage()]);}
?>
