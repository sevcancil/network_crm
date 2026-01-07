<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

// Sayfalama
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Yetki Sorgusu
$whereClause = "WHERE p.is_active = 1";
$params = [];
if (!isAdmin()) {
    $whereClause .= " AND p.department = :dept";
    $params[':dept'] = $_SESSION['department'];
}

// Verileri Çek
$sql = "SELECT p.*, c.name as company_name 
        FROM persons p 
        LEFT JOIN companies c ON p.company_id = c.id 
        $whereClause 
        ORDER BY p.id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$persons = $stmt->fetchAll();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Şahıs Listesi</h2>
        <div>
            <a href="add_person.php" class="btn btn-success"><i class="fas fa-plus"></i> Yeni Şahıs</a>
            <a href="export.php" class="btn btn-secondary"><i class="fas fa-file-excel"></i> Excel</a>
        </div>
    </div>

    <div class="input-group mb-3">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text" id="live_search" class="form-control" placeholder="İsim, şirket veya telefon ara...">
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>İsim Soyisim</th>
                        <th>Şirket</th>
                        <th>Telefon</th>
                        <th>Bölüm</th>
                        <th>Tam Adres</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="search_results">
                    <?php foreach ($persons as $person): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($person['name'] . ' ' . $person['surname']); ?></td>
                        <td><?php echo htmlspecialchars($person['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($person['phone']); ?></td>
                        <td><span class="badge bg-info"><?php echo $person['department']; ?></span></td>
                        <td><?php echo htmlspecialchars($person['full_address']); ?></td>
                        <td>
                            <a href="edit_person.php?id=<?php echo $person['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <?php if(isAdmin() || $person['department'] == $_SESSION['department']): ?>
                                <a href="delete_person.php?id=<?php echo $person['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Pasife almak istediğine emin misin?')"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        <?php if($page > 1): ?><a href="?page=<?php echo $page-1; ?>" class="btn btn-outline-primary">Önceki</a><?php endif; ?>
        <a href="?page=<?php echo $page+1; ?>" class="btn btn-outline-primary float-end">Sonraki</a>
    </div>
</div>



<?php require_once '../includes/footer.php'; ?>