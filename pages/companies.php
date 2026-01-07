<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

$companies = $pdo->query("SELECT * FROM companies WHERE is_active = 1")->fetchAll();
?>
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h2>Şirketler</h2>
        <div>
            <a href="add_company.php" class="btn btn-success"><i class="fas fa-plus"></i> Yeni Şirket</a>
            <a href="company_export.php" class="btn btn-secondary"><i class="fas fa-file-excel"></i> Excel</a>
        </div>
    </div>
    <table class="table table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>Şirket Adı</th>
                <th>Telefon</th>
                <th>Adres</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($companies as $comp): ?>
            <tr>
                <td>
                    <?php if(isAdmin()): ?>
                        <a href="company_detail.php?id=<?php echo $comp['id']; ?>"><?php echo htmlspecialchars($comp['name']); ?></a>
                    <?php else: ?>
                        <?php echo htmlspecialchars($comp['name']); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($comp['phone']); ?></td>
                <td><?php echo htmlspecialchars($comp['address']); ?></td>
                <td>
                    <a href="edit_company.php?id=<?php echo $comp['id']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                    <?php if(isAdmin()): ?>
                    <a href="delete_company.php?id=<?php echo $comp['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek/Pasife almak istiyor musunuz?')">Sil</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>