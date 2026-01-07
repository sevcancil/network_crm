<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

// SQL Sorgusu: Şirketleri ve onları ekleyen kullanıcıların adını (username) çekiyoruz
$sql = "SELECT c.*, u.username as creator_name 
        FROM companies c 
        LEFT JOIN users u ON c.created_by_user_id = u.id 
        WHERE c.is_active = 1 
        ORDER BY c.name ASC";

$companies = $pdo->query($sql)->fetchAll();
?>

<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-building"></i> Şirketler</h2>
        <div>
            <a href="company_export.php" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Excel İndir
            </a>
            <a href="add_company.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Şirket Ekle
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Şirket Adı</th>
                            <th>Telefon</th>
                            <th>Adres</th>
                            <th>Kayıt Açan</th> <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($companies) > 0): ?>
                            <?php foreach($companies as $comp): ?>
                            <tr>
                                <td>
                                    <?php if(isAdmin()): ?>
                                        <a href="company_detail.php?id=<?php echo $comp['id']; ?>" class="text-decoration-none fw-bold">
                                            <?php echo htmlspecialchars($comp['name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="fw-bold"><?php echo htmlspecialchars($comp['name']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($comp['phone']); ?></td>
                                <td><?php echo htmlspecialchars($comp['address']); ?></td>
                                
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-user-edit"></i> 
                                        <?php echo htmlspecialchars($comp['creator_name'] ?? '-'); ?>
                                    </small>
                                </td>

                                <td class="text-end">
                                    <a href="edit_company.php?id=<?php echo $comp['id']; ?>" class="btn btn-warning btn-sm" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if(isAdmin()): ?>
                                        <a href="delete_company.php?id=<?php echo $comp['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bu şirketi silmek/pasife almak istiyor musunuz?')"
                                           title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Kayıtlı şirket bulunamadı.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>