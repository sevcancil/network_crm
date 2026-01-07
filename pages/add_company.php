<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Oturum kontrolü
checkAuth();

// Form Gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $tax_no = trim($_POST['tax_no']);
    $address = trim($_POST['address']);
    
    // Logo Yükleme İşlemi
    $logo_name = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $upload_dir = '../uploads/';
        // Klasör yoksa oluştur
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed)) {
            // Benzersiz isim oluştur: logo_65a4bc... .jpg
            $logo_name = 'logo_' . uniqid() . '.' . $file_ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name);
        }
    }

    if (!empty($name)) {
        try {
            // YENİ: Oturum açan kullanıcının ID'sini alıyoruz
            $creator_id = $_SESSION['user_id']; 

            // SQL GÜNCELLENDİ: created_by_user_id eklendi
            $sql = "INSERT INTO companies (name, phone, email, tax_no, address, logo, created_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            // PARAMETRELER GÜNCELLENDİ: $creator_id eklendi
            if ($stmt->execute([$name, $phone, $email, $tax_no, $address, $logo_name, $creator_id])) {
                echo "<script>
                        alert('Şirket başarıyla eklendi!'); 
                        window.location.href = 'companies.php';
                      </script>";
                exit;
            }
        } catch (PDOException $e) {
            // EKSİK OLAN CATCH BLOĞU EKLENDİ
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $error = "Lütfen şirket adını giriniz.";
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Yeni Şirket Ekle</h5>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Şirket Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Örn: ABC Turizm Ltd. Şti.">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="0212 123 45 67">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="info@abc.com">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tax_no" class="form-label">Vergi No / Dairesi</label>
                                <input type="text" class="form-control" id="tax_no" name="tax_no">
                            </div>
                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo Yükle</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adres</label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Açık adres..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="companies.php" class="btn btn-secondary me-md-2">İptal</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>