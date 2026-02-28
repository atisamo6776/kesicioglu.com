# Kesicioğlu Portfolio Website

Modern ve dinamik portfolyo sitesi - Tam özellikli admin panel ile birlikte.

## 🎯 Özellikler

### 🎨 Frontend
- ✨ Modern ve minimalist tasarım
- 🌓 Dark/Light mode
- 📱 Tam responsive (mobil uyumlu)
- 🎭 Smooth animasyonlar ve efektler
- 💼 Projeler showcase bölümü
- 📧 İletişim formu
- 🎯 Hakkımda ve yetenekler bölümü

### 🔐 Admin Panel
- 📊 Dashboard (istatistikler ve özet bilgiler)
- ⚙️ Site Ayarları (başlıklar, renkler, iletişim bilgileri)
- 📱 Sosyal Medya Yönetimi (Instagram, GitHub, LinkedIn vb.)
- 🎯 Yetenekler Yönetimi (CRUD işlemleri)
- 📁 Projeler Yönetimi
  - Blog tipi projeler (görsel + açıklama)
  - Web App tipi projeler (dosya yükleme)
  - Kategori ve etiket yönetimi
- 📧 Mesajlar (iletişim formundan gelen mesajları görüntüleme)
- 📮 E-posta Ayarları (mesaj geldiğinde mail bildirimi)
- 🎨 Menü Yönetimi

## 📋 Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- cPanel hosting
- PHP ZipArchive extension (web app yüklemesi için)

## 🚀 Kurulum

### 1. Dosyaları Yükle

cPanel File Manager veya FTP ile tüm dosyaları public_html klasörüne yükle.

```
public_html/
├── admin/
├── api/
├── apps/
├── css/
├── js/
├── uploads/
├── config.php
├── database.sql
├── index.php
└── .htaccess
```

### 2. Veritabanı Oluştur

1. cPanel > phpMyAdmin'e git
2. Sol taraftan "New" butonuna tıkla
3. Veritabanı adı gir (örn: `kesicioglu_db`)
4. `database.sql` dosyasını içe aktar (Import)

### 3. Veritabanı Bağlantısını Ayarla

`config.php` dosyasını oluştur/düzenle:

- `config.example.php` dosyasını **kopyala**: `config.php`
- Sonra `config.php` içindeki DB bilgilerini doldur.

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'kesicioglu_db');     // Veritabanı adın
define('DB_USER', 'kesicioglu_user');   // Veritabanı kullanıcı adın
define('DB_PASS', 'your_password');     // Veritabanı şifren
define('SITE_URL', 'https://kesicioglu.com'); // Domain adresin
```

### 4. Klasör İzinleri

Aşağıdaki klasörlere yazma izni ver (777):

```bash
chmod 755 uploads/
chmod 755 apps/
```

cPanel File Manager'dan sağ tıklayıp "Change Permissions" > 777 seç.

### 5. Admin Paneline Giriş

1. Tarayıcıda `https://siteniz.com/admin/login.php` adresine git
2. Varsayılan giriş bilgileri:
   - **Kullanıcı Adı:** admin
   - **Şifre:** admin123

⚠️ **ÖNEMLİ:** İlk girişten sonra şifrenizi değiştirin!

## 🎨 Admin Panel Kullanımı

### Site Ayarları
- Ana başlık, alt başlık düzenleme
- Hakkımda metinlerini güncelleme
- İstatistik sayılarını değiştirme (proje, deneyim, müşteri)
- Site renklerini özelleştirme (primary, secondary, accent)
- İletişim bilgilerini güncelleme

### Sosyal Medya
- Instagram, GitHub, LinkedIn vb. hesap ekleme
- İkon ve URL düzenleme
- Sıralama yapma
- Aktif/Pasif yapma

### Yetenekler
- Yeni yetenek ekleme
- Başlık, açıklama, ikon düzenleme
- Etiketler ekleme (HTML, CSS, JavaScript vb.)
- Sıralama yapma

### Projeler
**Blog Tipi:**
- Görsel yükleme
- Açıklama yazma
- Demo ve GitHub URL ekleme
- Kategori seçme (Web, Mobil, Tasarım)
- Etiketler ekleme

**Web App Tipi:**
- ZIP dosyası yükleme (tüm web app dosyaları)
- Otomatik olarak `/apps/proje-adi/` klasörüne çıkartılır
- Projeye `kesicioglu.com/apps/proje-adi/` URLinden erişilir

### Mesajlar
- İletişim formundan gelen mesajları görüntüleme
- Okundu işaretleme
- Yanıtlama (mail ile)
- Silme

### E-posta Ayarları
- Mesaj geldiğinde mail bildirimi aktif/pasif
- Bildirim e-posta adresi belirleme

## 📧 E-posta Bildirimleri

İletişim formundan mesaj geldiğinde e-posta almak için:

1. Admin Panel > E-posta Ayarları
2. "E-posta Bildirimlerini Aç" kutucuğunu işaretle
3. Bildirim almak istediğin e-posta adresini gir
4. Kaydet

**Not:** E-posta gönderimi için sunucunuzda PHP mail() fonksiyonu aktif olmalı.

## 🔒 Güvenlik

### Önerilen Güvenlik Adımları:

1. **Admin Şifresini Değiştir:**
   - phpMyAdmin'den `admin_users` tablosuna git
   - Yeni şifre oluştur: [PHP Password Generator](https://phppasswordhash.com/)
   - `password` alanını güncelle

2. **Admin Klasörünü Koru:**
   `.htaccess` ekle (`admin/.htaccess`):
   ```apache
   AuthType Basic
   AuthName "Admin Area"
   AuthUserFile /home/kesiciog/.htpasswd
   Require valid-user
   ```

3. **SSL Sertifikası Kur:**
   - cPanel > SSL/TLS > Let's Encrypt
   - Domain için SSL aktif et

4. **Production Ortamında:**
   `config.php` dosyasında:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

## 🛠️ Sorun Giderme

### Veritabanı Bağlantı Hatası
- `config.php` dosyasındaki bilgileri kontrol et
- cPanel'den veritabanı kullanıcısının doğru yetkilerle eklendiğinden emin ol

### Dosya Yükleme Hatası
- `uploads/` ve `apps/` klasörlerine yazma izni ver (777)
- `php.ini` dosyasında `upload_max_filesize` değerini kontrol et

### Admin Paneline Girilemiyorsa
- URL'in doğru olduğundan emin ol: `https://siteniz.com/admin/login.php`
- Tarayıcı önbelleğini temizle
- Farklı tarayıcıda dene

### Resimler Görünmüyorsa
- Dosya yollarını kontrol et
- `uploads/` klasörü varlığını kontrol et
- Resim dosyalarının doğru yüklendiğinden emin ol

## 📱 Responsive Tasarım

Site tüm cihazlarda mükemmel görünür:
- 📱 Mobil (320px - 767px)
- 📱 Tablet (768px - 1023px)
- 💻 Desktop (1024px+)

## 🎨 Renk Özelleştirme

Admin Panel > Site Ayarları'ndan site renklerini değiştirebilirsin:
- **Primary Color:** Ana renk (butonlar, linkler)
- **Secondary Color:** İkincil renk (gradientler)
- **Accent Color:** Vurgu rengi (hover efektleri)

## 📞 Destek

Herhangi bir sorun yaşarsan:
1. README dosyasını tekrar oku
2. `config.php` ayarlarını kontrol et
3. Tarayıcı konsolunda hata mesajlarını kontrol et
4. cPanel hata loglarına bak

## 🚀 Güncellemeler

Yeni özellikler eklemek için admin panelini kullan. Kod güncellemeleri için dosyaları yedekle!

## 📄 Lisans

Bu proje özel olarak geliştirilmiştir.

---

**Başarılar! 🎉**
