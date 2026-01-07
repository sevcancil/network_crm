<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Kaydı Çek
$stmt = $pdo->prepare("SELECT * FROM persons WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch();

if (!$person) {
    echo "<script>alert('Kayıt bulunamadı!'); window.location.href='index.php';</script>";
    exit;
}

// 2. Telefon Numarasını Ayrıştır
$phone_parts = explode(" ", $person['phone'], 2);
$db_phone_code = isset($phone_parts[0]) ? $phone_parts[0] : '';
$db_phone_number = isset($phone_parts[1]) ? $phone_parts[1] : $person['phone']; 

// 3. Yetki Kontrolü
if (!isAdmin() && $person['department'] != $_SESSION['department']) {
    die("Bu kaydı düzenleme yetkiniz yok.");
}

$companies = $pdo->query("SELECT * FROM companies WHERE is_active = 1")->fetchAll();

// 4. Form Gönderimi (Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Telefonu tekrar birleştir
    $final_phone = $_POST['phone_code'] . " " . $_POST['phone'];
    
    // Ülke Turkey değilse il/ilçe null olsun
    $country = $_POST['country'];
    if ($country === 'Turkey') {
        $city = $_POST['city'];
        $district = $_POST['district'];
    } else {
        $city = null;
        $district = null;
    }

    // SQL GÜNCELLENDİ: is_active eklendi
    $sql = "UPDATE persons SET company_id=?, name=?, surname=?, phone=?, email=?, country=?, city=?, district=?, full_address=?, is_active=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $_POST['company_id'], 
        $_POST['name'], 
        $_POST['surname'], 
        $final_phone, 
        $_POST['email'], 
        $country, 
        $city, 
        $district, 
        $_POST['full_address'],
        $_POST['is_active'], // Yeni alan
        $id
    ]);

    if ($result) {
        echo "<script>alert('Güncelleme Başarılı!'); window.location.href='index.php';</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit"></i> Kayıt Düzenle: <?php echo htmlspecialchars($person['name']); ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>İsim</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($person['name']); ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Soyisim</label>
                        <input type="text" name="surname" value="<?php echo htmlspecialchars($person['surname']); ?>" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label>Şirket</label>
                        <select name="company_id" class="form-select">
                            <?php foreach($companies as $comp): ?>
                                <option value="<?php echo $comp['id']; ?>" <?php echo $comp['id'] == $person['company_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($comp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Durum</label>
                        <select name="is_active" class="form-select">
                            <option value="1" <?php echo $person['is_active'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?php echo $person['is_active'] == 0 ? 'selected' : ''; ?>>Pasif</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Telefon</label>
                        <div class="input-group">
                            <select class="form-select" name="phone_code" id="addr_phone_code" style="max-width: 140px;"></select>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($db_phone_number); ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>E-posta</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($person['email']); ?>" class="form-control">
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Ülke</label>
                        <select name="country" id="addr_country" class="form-select">
                        </select>
                    </div>
                    
                    <div class="col-md-8 row" id="addr_tr_area" style="display: none;">
                        <div class="col-md-6">
                            <label>İl</label>
                            <select name="city" id="addr_city" class="form-select">
                                <option value="">İl Seçiniz</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>İlçe</label>
                            <select name="district" id="addr_district" class="form-select">
                                <option value="">Önce İl Seçiniz</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Açık Adres</label>
                    <textarea name="full_address" class="form-control" rows="2"><?php echo htmlspecialchars($person['full_address']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning">Güncelle</button>
                <a href="index.php" class="btn btn-secondary">İptal</a>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/turkey_data.js?v=<?php echo time(); ?>"></script>

<script>
const savedData = {
    country: "<?php echo $person['country']; ?>",
    city: "<?php echo $person['city']; ?>",       
    district: "<?php echo $person['district']; ?>", 
    phoneCode: "<?php echo $db_phone_code; ?>"    
};

document.addEventListener('DOMContentLoaded', function() {
    const elCountry = document.getElementById('addr_country');
    const elCity = document.getElementById('addr_city');
    const elDistrict = document.getElementById('addr_district');
    const elArea = document.getElementById('addr_tr_area');
    const elPhone = document.getElementById('addr_phone_code');

    function loadDistricts(cityName) {
        elDistrict.innerHTML = '<option value="">İlçe Seçiniz</option>';
        if (typeof turkeyData !== 'undefined' && cityName) {
            const cityData = turkeyData.find(item => item.name === cityName);
            if (cityData && cityData.counties) {
                cityData.counties.forEach(county => {
                    let opt = document.createElement('option');
                    opt.value = county;
                    opt.text = county;
                    if (county === savedData.district) {
                        opt.selected = true;
                    }
                    elDistrict.appendChild(opt);
                });
            }
        }
    }

    function loadCities() {
        elCity.innerHTML = '<option value="">İl Seçiniz</option>';
        elDistrict.innerHTML = '<option value="">Önce İl Seçiniz</option>';

        if (typeof turkeyData !== 'undefined') {
            turkeyData.forEach(item => {
                let opt = document.createElement('option');
                opt.value = item.name;
                opt.text = item.name;
                if (item.name === savedData.city) {
                    opt.selected = true;
                }
                elCity.appendChild(opt);
            });

            if (savedData.city) {
                loadDistricts(savedData.city);
            }
        }
    }

    fetch('https://restcountries.com/v3.1/all?fields=name,idd,cca2')
        .then(res => res.json())
        .then(countries => {
            countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
            elCountry.innerHTML = ''; 
            
            countries.forEach(country => {
                let opt = new Option(country.name.common, country.name.common);
                if (country.name.common === savedData.country) {
                    opt.selected = true;
                }
                elCountry.add(opt);

                if(country.idd.root) {
                    let code = country.idd.root + (country.idd.suffixes ? country.idd.suffixes[0] : "");
                    let phoneOpt = new Option(`${country.cca2} (${code})`, code);
                    
                    if (code === savedData.phoneCode) {
                        phoneOpt.selected = true;
                    } 
                    else if (!savedData.phoneCode && country.name.common === 'Turkey') {
                        phoneOpt.selected = true;
                    }

                    elPhone.add(phoneOpt);
                }
            });

            checkCountryDisplay();
        });

    function checkCountryDisplay() {
        if (elCountry.value === 'Turkey') {
            elArea.style.display = 'flex';
            if (elCity.options.length <= 1) { 
                loadCities();
            }
        } else {
            elArea.style.display = 'none';
        }
    }

    elCountry.addEventListener('change', function() {
        checkCountryDisplay();
        if (this.value !== 'Turkey') {
            elCity.value = '';
            elDistrict.value = '';
        }
    });

    elCity.addEventListener('change', function() {
        loadDistricts(this.value);
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>