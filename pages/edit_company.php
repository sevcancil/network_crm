<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Mevcut veriyi çek
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$comp = $stmt->fetch();

if (!$comp) {
    echo "<script>alert('Şirket bulunamadı!'); window.location.href='companies.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $tax_no = $_POST['tax_no'];
    $address = $_POST['address'];
    
    // Logo Mantığı: Varsayılan olarak eskisini kullan
    $logo_name = $comp['logo']; 

    // Yeni dosya yüklendi mi?
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed)) {
            // Eski dosyayı silmek istersen (Opsiyonel):
            if($comp['logo'] && file_exists($upload_dir . $comp['logo'])) {
                unlink($upload_dir . $comp['logo']);
            }

            $logo_name = 'logo_' . uniqid() . '.' . $file_ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name);
        }
    }

    $sql = "UPDATE companies SET name=?, phone=?, email=?, tax_no=?, address=?, logo=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $phone, $email, $tax_no, $address, $logo_name, $id]);
    
    echo "<script>alert('Güncelleme Başarılı!'); window.location.href='companies.php';</script>";
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Şirket Düzenle</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label">Şirket Adı</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($comp['name']); ?>" class="form-control" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($comp['phone']); ?>" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($comp['email']); ?>" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Vergi No</label>
                                <input type="text" name="tax_no" value="<?php echo htmlspecialchars($comp['tax_no']); ?>" class="form-control">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" class="form-control mb-2">
                                <?php if($comp['logo']): ?>
                                    <div class="p-2 border rounded bg-light text-center">
                                        <small class="d-block text-muted mb-1">Mevcut Logo:</small>
                                        <img src="../uploads/<?php echo $comp['logo']; ?>" style="max-height: 50px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adres</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($comp['address']); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="companies.php" class="btn btn-secondary">İptal</a>
                            <button class="btn btn-primary">Değişiklikleri Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>