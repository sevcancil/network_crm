<?php
// Dosya: persons/export.php

require_once '../config/db.php';

// Dosya ismini belirleyelim (Örn: kisiler_listesi_20251216.xls)
$filename = "kisiler_listesi_" . date('Ymd') . ".xls";

// Excel Header'larını Gönderiyoruz
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Verileri Çekiyoruz (Şirket adını da JOIN ile alıyoruz)
// Eğer admin değilse sadece kendi departmanını çekmesi gibi filtreler eklenebilir.
// Şimdilik admin mantığıyla hepsini çekiyorum.
$sql = "SELECT p.*, c.name as company_name 
        FROM persons p 
        LEFT JOIN companies c ON p.company_id = c.id 
        ORDER BY p.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- EXCEL İÇERİĞİ (HTML TABLOSU OLARAK) ---
?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        /* Excel'de görünecek basit stiller */
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #f8f9fa; color: #000; font-weight: bold; border: 1px solid #ddd; }
        td { border: 1px solid #ddd; vertical-align: top; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Şirket</th>
                <th>Departman</th>
                <th>Telefon</th>
                <th>E-posta</th>
                <th>Ülke</th>
                <th>İl</th>
                <th>İlçe</th>
                <th>Açık Adres</th>
                <th>Durum</th>
                <th>Kayıt Tarihi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($persons as $row): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['surname']); ?></td>
                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                <td><?php echo htmlspecialchars($row['department']); ?></td>
                <td style="mso-number-format:'\@'"><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['country']); ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><?php echo htmlspecialchars($row['district']); ?></td>
                <td><?php echo htmlspecialchars($row['full_address']); ?></td>
                <td><?php echo $row['is_active'] ? 'Aktif' : 'Pasif'; ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>