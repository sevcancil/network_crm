<?php
// Dosya Yolu: companies/company_detail.php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

// 1. URL'den ID kontrolü
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Geçersiz Şirket ID!'); window.location.href='index.php';</script>";
    exit;
}

$company_id = $_GET['id'];

// 2. Şirket Bilgilerini Çekme
$stmtCompany = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmtCompany->execute([$company_id]);
$company = $stmtCompany->fetch();

if (!$company) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Şirket bulunamadı!</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// 3. Şirkete Bağlı Kişileri Çekme
$stmtPersons = $pdo->prepare("SELECT * FROM persons WHERE company_id = ? ORDER BY name ASC");
$stmtPersons->execute([$company_id]);
$persons = $stmtPersons->fetchAll();

?>

<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-building text-primary"></i> <?php echo htmlspecialchars($company['name']); ?>
        </h2>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Listeye Dön
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Şirket Bilgileri</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    <?php if(!empty($company['logo'])): ?>
                        <img src="../uploads/<?php echo $company['logo']; ?>" class="img-fluid rounded" style="max-height: 100px;">
                    <?php else: ?>
                        <i class="fas fa-image fa-4x text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="col-md-5">
                    <p><strong>E-posta:</strong> <?php echo htmlspecialchars($company['email'] ?? '-'); ?></p>
                    <p><strong>Telefon:</strong> <?php echo htmlspecialchars($company['phone'] ?? '-'); ?></p>
                    <p><strong>Durum:</strong> 
                        <?php if($company['is_active']): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Pasif</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-5">
                    <p><strong>Adres:</strong> <?php echo htmlspecialchars($company['address'] ?? '-'); ?></p>
                    <p><strong>Vergi No:</strong> <?php echo htmlspecialchars($company['tax_no'] ?? '-'); ?></p>
                    <small class="text-muted">Kayıt Tarihi: <?php echo date('d.m.Y', strtotime($company['created_at'])); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kayıtlı Kişiler (<?php echo count($persons); ?>)</h5>
             </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ad Soyad</th>
                            <th>Departman</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Durum</th> <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($persons) > 0): ?>
                            <?php foreach ($persons as $index => $person): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($person['name'] . ' ' . $person['surname']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($person['department'] ?? 'Belirtilmedi'); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($person['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($person['email']); ?></td>
                                    
                                    <td>
                                        <?php if ($person['is_active'] == 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pasif</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <a href="edit_person.php?id=<?php echo $person['id']; ?>" class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_person.php?id=<?php echo $person['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kişiyi silmek istediğine emin misin?')" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                                    Bu şirkete kayıtlı henüz kimse yok.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>