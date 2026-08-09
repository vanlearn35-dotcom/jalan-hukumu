# 🎧 TOEFL CBT – Computer Based Test Platform

Aplikasi **TOEFL CBT (Computer Based Test)** berbasis **Laravel**, dirancang untuk kebutuhan **simulasi TOEFL ITP** di sekolah, kampus, maupun lembaga pelatihan.

🔗 **Repository:**  
https://github.com/hazira-rozi/toefl-cbt

---

## 🎯 Fokus Proyek

- Perilaku CBT TOEFL yang **akurat**
- Manajemen **audio listening profesional**
- Struktur kode **mudah dikembangkan & didebug**

---

## 🚀 Fitur Utama

### 📝 Modul Ujian TOEFL
- Listening
- Structure
- Reading
- Timer global per section (sesuai TOEFL ITP)
- Auto submit jawaban
- Mark for review (Structure & Reading)

---

### 🔊 Manajemen Audio Listening
- Upload audio per paket soal
- Upload manual atau via **Laravel File Manager (LFM)**
- Multi versi audio (`v1`, `v2`, `v3`, …)
- Auto select audio **terbaru sebagai aktif**
- **Hanya 1 audio aktif** per paket

**Struktur penyimpanan:**
```
storage/app/public/audios/{package_id}/file.mp3
```

---

### ▶️ Audio Player Profesional
- Play / Pause
- Seek slider
- Volume slider
- Current time / total duration
- Tombol Play/Stop dinamis pada tabel audio
- Player utama khusus untuk **audio aktif paket**

---

### 📦 Manajemen Paket Soal
- Audio disimpan sebagai `audio_path` di tabel `packages`
- Yang ditampilkan ke user adalah **nama file audio**
- Audio paket dapat diputar langsung dari halaman paket

---

### 📁 Laravel File Manager (UniSharp)
- Upload & pilih audio melalui UI File Manager
- Perilaku sama dengan upload manual
- Terintegrasi dengan versioning audio
- Aman dan mudah digunakan

---

### 🛠 Debug Friendly
- Validasi upload jelas
- Log error lengkap
- Mudah tracking path audio & database
- Cocok untuk pengembangan jangka panjang

---

## 🧰 Teknologi yang Digunakan
- Laravel
- PHP 8.1+
- MySQL / MariaDB
- Laravel File Manager (UniSharp)
- SB Admin 2
- SweetAlert
- Howler.js Audio

---

## ⚙️ Instalasi & Setup

### 1️⃣ Clone Repository
```
git clone https://github.com/hazira-rozi/toefl-cbt.git
cd toefl-cbt
```

### 2️⃣ Install Dependency
```
composer install
npm install
npm run build
```

### 3️⃣ Konfigurasi Environment
```
cp .env.example .env
php artisan key:generate
```

**Contoh `.env`:**
```
APP_URL=http://localhost/toefl-cbt/public

DB_DATABASE=toefl_cbt
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

### 4️⃣ Migrasi Database
```
php artisan migrate
```

### 5️⃣ Storage Link (**WAJIB**)
```
php artisan storage:link
```

Pastikan folder berikut dapat ditulis:
- `storage/`
- `bootstrap/cache/`

---

## 📁 Setup Laravel File Manager (LFM)

### Install
```
composer require unisharp/laravel-filemanager
```

### Publish Config & Asset
```
php artisan vendor:publish --tag=lfm_config
php artisan vendor:publish --tag=lfm_public
```

### Tambahkan Route (`web.php`)
```
Route::group(['prefix' => 'laravel-filemanager'], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
```

### Akses
```
http://localhost/toefl-cbt/public/laravel-filemanager
```

---

## 🧪 Mode Debug
Aktifkan debug:
```
APP_DEBUG=true
```

Log error tersedia di:
```
storage/logs/laravel.log
```

---

## ☕ Dukung Proyek Ini
Jika proyek ini bermanfaat, Anda bisa mendukung pengembangannya 🙏

**📱 GoPay**  
- Nomor: `+62 822-8514-9846`  
- Nama: **Hazira**

---

## 📜 Lisensi
**MIT License**  
Bebas digunakan dan dikembangkan untuk keperluan pendidikan.

---

## 🙌 Author
**Hazira Fakhrurrozi Amir**  
SMKN 1 Singkarak – Teacher  
Indonesia 🇮🇩
