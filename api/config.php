<?php
// Hostinger: replace these values with your MySQL database details.
$dbHost = 'localhost';
$dbName = 'YOUR_DATABASE_NAME';
$dbUser = 'YOUR_DATABASE_USER';
$dbPass = 'YOUR_DATABASE_PASSWORD';

$adminUser = 'admin';
$adminPassword = 'CHANGE_THIS_PASSWORD';

try {
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['success'=>false,'error'=>'Database connection is not configured yet.']);
  exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
?>
