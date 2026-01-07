<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
checkAuth();

$companies = $pdo->query("SELECT * FROM companies WHERE is_active = 1")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $company_id = $_POST['company_id'];
    $phone = $_POST['phone_code'] . " " . $_POST['phone'];
    $email = $_POST['email'];
    $country = $_POST['country'];
    
    // Eğer Türkiye seçilirse il/ilçe al, yoksa boş gönder
    if ($country === 'Turkey') {
        $city = $_POST['city'];
        $district = $_POST['district'];
    } else {
        $city = null;
        $district = null;
    }

    $full_address = $_POST['full_address'];
    $department = $_SESSION['department'];
    $creator = $_SESSION['user_id'];

    $sql = "INSERT INTO persons (company_id, name, surname, phone, email, country, city, district, full_address, department, created_by_user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$company_id, $name, $surname, $phone, $email, $country, $city, $district, $full_address, $department, $creator]);

    if($result) {
        echo "<script>alert('Kayıt Başarılı!'); window.location.href='index.php';</script>";
    }
}
?>

<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Yeni Şahıs Ekle</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6"><label>İsim</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6"><label>Soyisim</label><input type="text" name="surname" class="form-control" required></div>
                </div>
                
                <div class="mb-3">
                    <label>Şirket</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">Seçiniz...</option>
                        <?php foreach($companies as $comp): ?>
                            <option value="<?php echo $comp['id']; ?>"><?php echo htmlspecialchars($comp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                     <small>Şirket listede yoksa önce <a href="add_company.php">buradan ekleyin</a>.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Telefon</label>
                        <div class="input-group">
                            <select class="form-select" name="phone_code" id="addr_phone_code" style="max-width: 140px;"></select>
                            <input type="text" name="phone" class="form-control" placeholder="555 123 45 67">
                        </div>
                    </div>
                    <div class="col-md-6"><label>E-posta</label><input type="email" name="email" class="form-control"></div>
                </div>

                <hr>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Ülke</label>
                        <select name="country" id="addr_country" class="form-select">
                            <option value="Turkey" selected>Türkiye</option>
                        </select>
                    </div>
                    
                    <div class="col-md-8 row" id="addr_tr_area">
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
                    <textarea name="full_address" class="form-control" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>

<script src="../assets/js/turkey_data.js?v=<?php echo time(); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seçiciler
    const elCountry = document.getElementById('addr_country');
    const elCity = document.getElementById('addr_city');
    const elDistrict = document.getElementById('addr_district');
    const elArea = document.getElementById('addr_tr_area');
    const elPhone = document.getElementById('addr_phone_code');

    // --- Fonksiyon: İlçeleri Doldur (Yardımcı Fonksiyon) ---
    function loadDistricts(cityName) {
        // İlçe kutusunu temizle
        elDistrict.innerHTML = '<option value="">İlçe Seçiniz</option>';

        if (typeof turkeyData !== 'undefined' && cityName) {
            const cityData = turkeyData.find(item => item.name === cityName);
            if (cityData && cityData.counties) {
                cityData.counties.forEach(county => {
                    let opt = document.createElement('option');
                    opt.value = county;
                    opt.text = county;
                    elDistrict.appendChild(opt);
                });
            }
        }
    }

    // --- Fonksiyon: İlleri Doldur ---
    function loadCities() {
        // Önce temizle
        elCity.innerHTML = '<option value="">İl Seçiniz</option>';
        elDistrict.innerHTML = '<option value="">Önce İl Seçiniz</option>';
        
        if (typeof turkeyData !== 'undefined') {
            turkeyData.forEach(item => {
                let opt = document.createElement('option');
                opt.value = item.name;
                opt.text = item.name;

                // **BURASI DEĞİŞTİ**: Eğer şehir İstanbul ise otomatik seç
                if (item.name === 'İstanbul') {
                    opt.selected = true;
                }

                elCity.appendChild(opt);
            });
            
            // Döngü bittiğinde İstanbul seçiliyse, ilçelerini de yükle
            if(elCity.value === 'İstanbul') {
                loadDistricts('İstanbul');
            }

        } else {
            console.error("HATA: turkeyData yüklenemedi.");
        }
    }

    // --- Olay 1: Ülke Değişince ---
    elCountry.addEventListener('change', function() {
        const val = this.value;
        
        if (val === 'Turkey') {
            elArea.style.display = 'flex';
            loadCities(); // Listeyi tazele (Bu işlem İstanbul'u yine default yapar)
        } else {
            elArea.style.display = 'none';
            elCity.innerHTML = '<option value="">İl Seçiniz</option>';
            elDistrict.innerHTML = '<option value="">Önce İl Seçiniz</option>';
        }
    });

    // --- Olay 2: İl Değişince ---
    elCity.addEventListener('change', function() {
        loadDistricts(this.value);
    });

    // --- Olay 3: API ile Ülkeler ---
    fetch('https://restcountries.com/v3.1/all?fields=name,idd,cca2')
        .then(res => res.json())
        .then(countries => {
            countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
            countries.forEach(country => {
                if (country.name.common !== 'Turkey') {
                    let opt = new Option(country.name.common, country.name.common);
                    elCountry.add(opt);
                }
                if(country.idd.root) {
                    let code = country.idd.root + (country.idd.suffixes ? country.idd.suffixes[0] : "");
                    let opt = new Option(`${country.cca2} (${code})`, code);
                    if(country.name.common === 'Turkey') opt.selected = true;
                    elPhone.add(opt);
                }
            });
        });

    // Başlangıçta çalıştır
    loadCities();
});
</script>

<?php require_once '../includes/footer.php'; ?>