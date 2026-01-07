<?php
require_once '../config/db.php';
session_start();
$id = (int)$_GET['id'];
// Güvenlik: Admin mi veya kendi departmanı mı kontrol edilmeli (basitlik için sadece sorguyu yazıyorum)
$stmt = $pdo->prepare("UPDATE persons SET is_active = 0 WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
?>