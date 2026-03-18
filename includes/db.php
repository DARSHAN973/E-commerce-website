<?php
// Supabase PostgreSQL pooler connection
$host    = 'aws-1-ap-northeast-1.pooler.supabase.com';
$port    = '5432';
$dbname  = 'postgres';
$db_user = 'postgres.fhmxurqlubqdiglccejx';
$db_pass = getenv('SUPABASE_DB_PASSWORD') ?: 'darshanNichite4217';

try {
  $conn = new PDO(
    "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
    $db_user,
    $db_pass,
    [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
?>
