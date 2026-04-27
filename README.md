# Management Stok Opname (CodeIgniter 4)

Aplikasi manajemen stok barang berbasis CodeIgniter 4 yang mendukung stok masuk, distribusi, opname, serta sistem role & permission.

---

## ✨ Fitur Utama

- Manajemen Master Barang
- Stok Masuk (Barang Masuk)
- Distribusi Barang (Barang Keluar)
- Stok Opname (Draft & Finalisasi)
- Mutasi Stok Otomatis
- Manajemen User & Role
- Sistem Permission (RBAC)
- Dashboard Monitoring

---

## Requirements

Pastikan perangkat sudah memenuhi kebutuhan berikut:

### Server & Tools

- XAMPP
- PHP \*\*>= 8.2.12
- Composer \*\*>= 2.8.12
- MySQL / MariaDB

---

### PHP Extension Wajib

Pastikan extension berikut aktif di `php.ini`:

```
intl
mbstring
json
mysqli
curl
```

---

### Cek Versi

Buka shell di XAMPP dan ketikan ini:

```
php -v
composer -V
```

---

## Cara Install Project ini

### 1. Clone via Git (Direkomendasikan)

```
git clone https://github.com/username/nama-project.git
cd nama-project
```

---

### 2. Download ZIP

1. Klik tombol **Code → Download ZIP**
2. Extract file
3. Buka folder project

---

## Setup Aplikasi

### 1. Install Dependency

```
composer install
```

---

### 2. Setup File Environment

Copy file `.env.example` menjadi `.env`:

```
cp .env.example .env
```

Edit bagian berikut di `.env`:

```
app.baseURL = 'http://localhost:8080'

database.default.hostname = localhost
database.default.database = stock_management
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

---

## Setup Database (Import SQL)

Project ini sudah menyediakan database siap pakai.

📁 Lokasi file:

```
Database/Migration/database.sql
```

---

### Langkah Import

1. Buka **phpMyAdmin**
2. Klik **New → Buat database**

```
CREATE DATABASE stock_management;
```

3. Pilih database yang sudah dibuat
4. Klik menu **Import**
5. Upload file:

```
Database/Migration/database.sql
```

6. Klik **Go**

---

### Setelah Import

Pastikan `.env` sesuai:

```
database.default.database = stock_management
```

---

### Jalankan Seeder (Data Awal) Seeder user admin:

```
php spark db:seed SeederUser
```

---

## ▶ Jalankan Aplikasi

```
php spark serve
```

Buka di browser:

```
http://localhost:8080
```

---

## Login Default

```
Email    : admin@gmail.com
Password : admin123
```

---

## 📁 Struktur Project

```
app/
public/
writable/
.env (tidak diupload)
.env.example
Database/Migration/database.sql
```

---

## ⚠️ Catatan Penting

- Folder `writable/` harus memiliki izin write
- File `.env` tidak diupload ke GitHub (keamanan)
- Pastikan:
  - Composer sudah terinstall
  - Database sudah dibuat
  - Konfigurasi `.env` benar

---

## Teknologi

- CodeIgniter 4
- MySQL
- jQuery
- DataTables
- Select2
- SweetAlert2
- Bootstrap

---

## Sistem Keamanan

- Validasi stok (tidak bisa minus)
- Lock barang saat opname draft
- RBAC (Role Based Access Control)
- Logging mutasi stok

---

## Alur Sistem

```
Stok Masuk → Tambah Stok
Distribusi → Kurangi Stok
Opname     → Sinkronisasi stok fisik
Mutasi     → Tracking perubahan stok
```

---

## 📌 Catatan Developer

Project ini menggunakan:

- Modular Controller
- AJAX + DataTables
- Transaction Database (ACID)
- Locking stok (`FOR UPDATE`)
- Clean separation (Model / Controller / View / Script)

---

## 📸 Preview (Optional)

Tambahkan screenshot di sini:

```
/docs/dashboard.png
```

---

## Deployment (Production)

- Gunakan Apache / Nginx
- Point ke folder `/public`
- Gunakan `.env` production
- Nonaktifkan debug mode

---

## Author

Developed by:
**[Nama Kamu]**

---

## Support

Jika ada kendala, silakan hubungi developer.

---

## Notes

Project ini dibuat untuk:

- Portfolio
- Pembelajaran
- Sistem internal perusahaan

---
