<?php
// Dosya: pages/company_export.php

require_once '../config/db.php';

// Dosya ismini belirleyelim
$filename = "sirket_listesi_" . date('Ymd') . ".xls";

// Excel Header'larını Gönderiyoruz
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Verileri Çekiyoruz
// GÜNCELLEME: users tablosuyla JOIN yapıldı ve creator_name (Kullanıcı Adı) çekildi
$sql = "SELECT c.*, u.username as creator_name 
        FROM companies c 
        LEFT JOIN users u ON c.created_by_user_id = u.id 
        WHERE c.is_active = 1 
        ORDER BY c.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #f8f9fa; color: #000; font-weight: bold; border: 1px solid #ddd; }
        td { border: 1px solid #ddd; vertical-align: top; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Şirket Adı</th>
                <th>Telefon</th>
                <th>E-posta</th>
                <th>Vergi No</th>
                <th>Adres</th>
                <th>Kayıt Tarihi</th>
                <th>Kayıt Açan</th> </tr>
        </thead>
        <tbody>
            <?php foreach($companies as $row): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td style="mso-number-format:'\@'"><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td style="mso-number-format:'\@'"><?php echo htmlspecialchars($row['tax_no']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo date('d.m.Y', strtotime($row['created_at'])); ?></td>
                
                <td><?php echo htmlspecialchars($row['creator_name'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>