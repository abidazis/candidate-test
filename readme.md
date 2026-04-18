# CLT Toolbox - Candidate Test Assignment
Sistem Manajemen Supplier dengan fitur hierarki Layup & Layer, serta sistem Resolusi Konflik berbasis UI dan Backend. Proyek ini dibangun untuk memenuhi persyaratan Technical Test di CLT Toolbox.

# Fitur Utama
CRUD Supplier: Manajemen data supplier secara reaktif.

Hierarchical Data: Relasi One-to-Many (Supplier -> Layup -> Layer).

JSON Import/Export: Pertukaran data antar sistem melalui format JSON.

Backend Conflict Resolution: Menggunakan strategi Overwrite Existing dengan metode updateOrCreate.

Bonus: UI-Based Conflict Resolution: Pop-up Modal reaktif untuk membandingkan data Existing vs Incoming secara visual sebelum proses import selesai.

# Tech Stack
Backend: Laravel 11 (PHP 8.x)

Frontend: Vue.js 3 (via CDN) & Tailwind CSS

Database: MySQL

Testing: PHPUnit (Feature Testing)

# Prasyarat Sistem
PHP >= 8.2

Composer

MySQL / XAMPP

Git

# Instruksi Instalasi (Local Environment)
Clone Repository & Pindah Branch

Bash
git clone https://github.com/abidazis/candidate-test.git
cd candidate-test
git checkout abidathanandaazis-assignment
Instalasi Dependency

Bash
composer install
Konfigurasi Environment

Salin file .env.example menjadi .env:

Bash
copy .env.example .env
Buat database baru di MySQL (misal: clt_toolbox_db).

Sesuaikan konfigurasi database di file .env:

Cuplikan kode
DB_DATABASE=clt_toolbox_db
DB_USERNAME=root
DB_PASSWORD=
Generate Application Key

Bash
php artisan key:generate
Jalankan Migration

Bash
php artisan migrate
Jalankan Server

Bash
php artisan serve
Akses aplikasi di: http://localhost:8000

# Menjalankan Automated Test
Untuk memverifikasi fungsionalitas API dan integrasi database, jalankan perintah berikut:

Bash
php artisan test --filter SupplierImportTest

# Cara Penggunaan Fitur Import & Conflict Resolution
Tambahkan Supplier baru melalui tombol + Add New Supplier.

Klik Import JSON dan pilih file .json dengan struktur yang sesuai.

Jika terdapat data dengan Nama Layup dan Order Layer yang sama namun memiliki nilai yang berbeda, sistem akan secara otomatis memunculkan Conflict Resolution Modal.

Pilih "Accept Incoming Data" untuk memperbarui data lama, atau "Keep Existing Data" untuk membatalkan perubahan pada baris tersebut.