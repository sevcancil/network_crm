<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

// Sadece Adminler Girebilir
if (!isAdmin()) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Bu sayfaya erişim yetkiniz yok.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// --- KULLANICI SİLME İŞLEMİ ---
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    
    // Kişi kendini silemesin
    if ($del_id == $_SESSION['user_id']) {
        $error = "Hata: Kendi hesabınızı silemezsiniz!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$del_id]);
        echo "<script>alert('Kullanıcı silindi!'); window.location.href='add_user.php';</script>";
        exit;
    }
}

// --- KULLANICI EKLEME İŞLEMİ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $dept = $_POST['department'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, department) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $role, $dept]);
        $success = "Kullanıcı başarıyla oluşturuldu.";
    } catch(PDOException $e) {
        $error = "Hata: Bu kullanıcı adı zaten kullanılıyor.";
    }
}

// --- MEVCUT KULLANICILARI LİSTELE ---
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="container mt-4">
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus"></i> Yeni Kullanıcı Ekle</h5>
        </div>
        <div class="card-body">
            
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Kullanıcı Adı</label>
                        <input type="text" name="username" class="form-control" required placeholder="kullanici123">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Şifre</label>
                        <input type="text" name="password" class="form-control" required placeholder="******">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Rol</label>
                        <select name="role" class="form-select">
                            <option value="personel">Personel</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Bölüm</label>
                        <select name="department" class="form-select">
                            <option value="Mice">Mice</option>
                            <option value="Turizm">Turizm</option>
                            <option value="Muhasebe">Muhasebe</option>
                            <option value="Genel">Genel</option>
                            <option value="Yönetici">Yönetici</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Oluştur</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-users"></i> Mevcut Kullanıcılar</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>Rol</th>
                            <th>Bölüm</th>
                            <th>Kayıt Tarihi</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>**<?php echo htmlspecialchars($user['username']); ?>**</td>
                            <td>
                                <?php if($user['role'] == 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Personel</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['department']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                            <td class="text-end">
                                <a href="?delete_id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                    <i class="fas fa-trash"></i> Sil
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>