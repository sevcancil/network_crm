# 🚀 Network CRM - Personel ve Şirket Yönetim Sistemi

![License: CC BY-NC 4.0](https://img.shields.io/badge/License-CC%20BY--NC%204.0-lightgrey.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)

**Network CRM**, şirketlerin verilerini, çalışan kayıtlarını ve iletişim bilgilerini güvenli bir şekilde yönetmelerini sağlayan web tabanlı bir CRM (Müşteri İlişkileri Yönetimi) projesidir. **Mice, Turizm ve Kurumsal** yönetim ihtiyaçları için geliştirilmiştir.

---

## 🌟 Özellikler

* **🔐 Güvenli Kimlik Doğrulama:**
    * Admin ve Personel giriş paneli.
    * Hashlenmiş şifreleme (Password Hashing).
    * Oturum (Session) yönetimi.
* **👥 Rol Bazlı Yetkilendirme (RBAC):**
    * **Admin:** Her şeye tam erişim (Ekleme, Silme, Düzenleme, Kullanıcı Yönetimi).
    * **Personel:** Sadece kendi departmanını görüntüleme ve düzenleme.
* **🏢 Şirket & Personel Yönetimi:**
    * Şirket profilleri oluşturma (Logo, Vergi No, İletişim bilgileri).
    * Şirketlere bağlı çalışanları listeleme ve yönetme.
    * Dinamik **İl/İlçe Seçimi** (JavaScript & JSON ile Türkiye verisi).
* **📊 Raporlama & Export:**
    * Tek tıkla **Excel (.xls)** formatında rapor alma.
    * Anlık (Live) Arama ve Filtreleme.
* **📱 Responsive Tasarım:**
    * Bootstrap 5 ile tüm cihazlarda (Mobil/Tablet/Masaüstü) uyumlu arayüz.

---

## 🛠️ Kullanılan Teknolojiler

* **Backend:** Native PHP (PDO Veritabanı Bağlantısı)
* **Veritabanı:** MySQL
* **Frontend:** HTML5, CSS3, Bootstrap 5
* **Scripting:** JavaScript (AJAX & DOM Manipülasyonu)

---

## ⚙️ Kurulum (Localhost)

Projeyi kendi bilgisayarınızda çalıştırmak için aşağıdaki adımları izleyin:

1.  **Projeyi Klonlayın:**
    ```bash
    git clone [https://github.com/sevcancil/network-crm.git](https://github.com/sevcancil/network-crm.git)
    ```

2.  **Veritabanını Oluşturun:**
    * `phpMyAdmin`'e gidin ve yeni bir veritabanı oluşturun (Örn: `crm_db`).
    * Ana dizindeki `example_db.sql` dosyasını içe aktarın (Import).

3.  **Veritabanı Ayarlarını Yapın:**
    * `config/` klasörü içindeki `db.example.php` dosyasının adını `db.php` olarak değiştirin.
    * Dosyayı açıp kendi veritabanı bilgilerinizi girin:
    ```php
    $host = 'localhost';
    $dbname = 'crm_db';
    $username = 'root';
    $password = '';
    ```

4.  **Çalıştırın:**
    * Tarayıcınızda `http://localhost/network-crm` adresine gidin.
    * **Varsayılan Admin Girişi:**
        * Kullanıcı Adı: `admin`
        * Şifre: `123456` (veya veritabanında belirlediğiniz şifre)

---

## 📂 Dosya Yapısı

```text
network_crm/
│
├── .gitignore              # Git'e gönderilmeyecek dosyaları belirler (db.php, uploads vb.)
├── .htaccess               # SSL yönlendirme ve dosya güvenliği ayarları
├── LICENSE                 # Projenin lisans dosyası (CC BY-NC 4.0)
├── README.md               # Proje dokümantasyonu (Kurulum ve bilgiler)
├── example_db.sql          # Veritabanı kurulum dosyası (SQL yedeği)
│
├── api/                    # AJAX istekleri için arka plan servisleri
│   └── search.php          # Canlı arama işlemini yapan API
│
├── assets/                 # Tasarım ve istemci taraflı dosyalar
│   ├── css/
│   │   └── style.css       # Özel stil dosyası
│   ├── js/
│   │   ├── main.js         # Genel JavaScript kodları
│   │   └── turkey_data.js  # İl/İlçe verisi (JS formatında)
│   └── json/
│       └── tr_il_ilce.json # Alternatif il/ilçe verisi
│
├── config/                 # Veritabanı ayarları
│   └──db.example.php      # Örnek bağlantı dosyası (GitHub'a giden)
│
├── includes/               # Tekrar eden sayfa parçaları
│   ├── footer.php          # Alt kısım (Telif hakkı, JS scriptleri)
│   ├── functions.php       # Yetki kontrolü, temizleme vb. yardımcı fonksiyonlar
│   ├── header.php          # Üst kısım (HTML head, CSS linkleri)
│   └── navbar.php          # Navigasyon menüsü
│
├── pages/                  # Sayfaların bulunduğu ana klasör
│   ├── add_company.php     # Şirket ekleme sayfası
│   ├── add_person.php      # Kişi ekleme sayfası
│   ├── add_user.php        # Kullanıcı ekleme sayfası (Sadece Admin)
│   ├── companies.php       # Şirketler listesi
│   ├── company_detail.php  # Şirket detay ve çalışanları sayfası
│   ├── company_export.php  # Şirketleri Excel'e aktarma
│   ├── delete_company.php  # Şirket silme işlemi
│   ├── delete_person.php   # Kişi silme işlemi
│   ├── edit_company.php    # Şirket düzenleme sayfası
│   ├── edit_person.php     # Kişi düzenleme sayfası
│   ├── export.php          # Kişileri Excel'e aktarma
│   ├── index.php           # Ana Sayfa (Dashboard / Kişi Listesi)
│   ├── login.php           # Giriş sayfası
│   └── logout.php          # Çıkış işlemi
│
└── uploads/                # Yüklenen şirket logolarının tutulduğu klasör

```
---

## 📄 Lisans (License)

Bu proje **Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)** ile lisanslanmıştır.

❌ **Ticari Kullanım Yasaktır:** Bu yazılımı satamaz veya ticari bir ürünün parçası olarak kullanamazsınız.
✅ **Kişisel ve Eğitim:** Kaynak göstermek şartıyla (Atıf) kişisel projelerinizde inceleyebilir ve geliştirebilirsiniz.

Detaylar için `LICENSE` dosyasına bakınız.

---

## 👩‍💻 Geliştirici

**Sevcan Çil** - *Bilgisayar Mühendisi*

* GitHub: [@sevcan-cil](https://github.com/sevcancil)
* LinkedIn: [Sevcan Çil](https://www.linkedin.com/in/sevcancil/)