<?php
// Bu dosya şablondur. Adını db.php olarak değiştirip bilgilerinizi girin.
$host = 'localhost';
$dbname = 'veritabani_adi';
$username = 'kullanici_adi';
$password = 'sifre';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>