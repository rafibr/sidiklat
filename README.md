# SIDIKLAT - Sistem Informasi Data Pelatihan

Aplikasi web untuk mengelola data pelatihan pegawai berbasis Laravel 11 dengan Tailwind CSS. Proyek ini adalah konversi dari prototype HTML statis menjadi aplikasi web dinamis dengan database dan fitur CRUD lengkap.

## 🚀 Fitur Utama

### 1. Dashboard Analytics
- **Statistik Real-time**: Total pegawai, pelatihan selesai, sedang berjalan, dan akan datang
- **Visualisasi Data**: Chart.js untuk grafik pelatihan per jenis dan tren bulanan
- **Progress Tracking**: Tabel progress pelatihan pegawai dengan persentase pencapaian
- **Responsive Design**: Tampilan optimal di desktop dan mobile

### 2. Manajemen Data Pelatihan
- **CRUD Pelatihan**: Create, Read, Update, Delete data pelatihan
- **Filter & Search**: Pencarian berdasarkan nama dan filter jenis pelatihan
- **Upload Sertifikat**: Dukungan upload file PDF, JPG, JPEG, PNG (max 2MB)
- **Status Tracking**: Selesai, Sedang Berjalan, Akan Datang
- **Validasi Form**: Validasi input dengan pesan error yang user-friendly

### 3. Data Progress Pegawai
- **Progress Individual**: Tracking progress pelatihan per pegawai
- **Target JP**: Sistem target Jam Pelajaran (JP) dengan indikator visual
- **Status Completion**: Progress bar dan badge status
- **Export Ready**: Struktur data siap untuk export

## 🛠 Teknologi yang Digunakan

### Backend
- **Laravel 11**: Framework PHP modern dengan Eloquent ORM
- **SQLite**: Database lightweight untuk development
- **PHP 8.2+**: Bahasa pemrograman server-side

### Frontend
- **Tailwind CSS 3.4**: Utility-first CSS framework
- **Blade Templates**: Laravel templating engine
- **Chart.js**: Library visualisasi data interaktif
- **Font Awesome 6**: Icon library

### Build Tools
- **Vite**: Modern build tool untuk asset compilation
- **PostCSS**: CSS post-processor
- **npm**: Package manager untuk dependencies

## 📁 Struktur Database

### Tabel Pegawai
```sql
- id (Primary Key)
- nip (Nullable untuk pegawai kontrak)
- nama_lengkap
- pangkat_golongan
- unit_kerja
- status (PNS/Kontrak/Magang)
- tempat_lahir
- tanggal_lahir (String format)
- jenis_kelamin
- agama
- alamat
- created_at, updated_at
```

### Tabel Pelatihan
```sql
- id (Primary Key)
- pegawai_id (Foreign Key)
- nama_pelatihan
- jenis_pelatihan
- penyelenggara
- tempat (Nullable)
- tanggal_mulai (String format)
- tanggal_selesai (String format)
- jp (Jam Pelajaran)
- status
- deskripsi (Nullable)
- sertifikat_path (Nullable)
- created_at, updated_at
```

## 🎯 Model & Relationships

### Model Pegawai
```php
- hasMany(Pelatihan::class)
- Accessor: getProgressAttribute() - Menghitung progress pelatihan
- Fillable: nip, nama_lengkap, pangkat_golongan, unit_kerja, status, dll
```

### Model Pelatihan
```php
- belongsTo(Pegawai::class)
- Fillable: pegawai_id, nama_pelatihan, jenis_pelatihan, penyelenggara, dll
- File Storage: Sertifikat disimpan di storage/app/public/sertifikat
```

## 🌐 Routing & Controllers

### Web Routes
```php
- / → Dashboard (dashboard.index)
- /progress → Progress Pegawai (progress.index)
- /pelatihan → Resource Controller (pelatihan.*)
```

### Controllers
1. **DashboardController**: Statistik dan chart data
2. **ProgressController**: Progress tracking pegawai
3. **PelatihanController**: CRUD operations pelatihan

## 🎨 UI/UX Design

### Layout Design
- **Modern Gradient**: Background gradient blue-to-purple
- **Card-based**: Clean card layouts dengan shadow
- **Navigation Tabs**: Tab navigation dengan active state
- **Responsive Grid**: CSS Grid dan Flexbox untuk layout

### Color Scheme
- **Primary**: Blue (#3B82F6)
- **Secondary**: Purple (#8B5CF6)
- **Success**: Green (#10B981)
- **Warning**: Yellow (#F59E0B)
- **Danger**: Red (#EF4444)

### Typography
- **Font**: Inter (System fonts fallback)
- **Hierarchy**: Consistent font sizes dan weights
- **Readability**: High contrast dan proper line-height

## 📊 Data Seeding

### Sample Data
- **14 Pegawai**: Data real dari Inspektorat Kota Banjarbaru
- **5 Pelatihan**: Sample pelatihan dengan berbagai jenis
- **Relasi Lengkap**: Setiap pelatihan terhubung dengan pegawai

### Jenis Pelatihan
1. Diklat Struktural
2. Diklat Fungsional  
3. Diklat Teknis
4. Workshop
5. Seminar
6. Pelatihan Jarak Jauh
7. E-Learning

## 🔧 Instalasi & Setup

### Prerequisites
```bash
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite extension
```

### Installation Steps
```bash
# Clone repository
git clone <repository-url>
cd sidiklat

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Compile assets
npm run build

# Start development server
php artisan serve
```

## 📂 File Structure

```
sidiklat/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── PelatihanController.php
│   │   └── ProgressController.php
│   └── Models/
│       ├── Pegawai.php
│       └── Pelatihan.php
├── database/
│   ├── migrations/
│   │   ├── create_pegawais_table.php
│   │   └── create_pelatihans_table.php
│   └── seeders/
│       ├── PegawaiSeeder.php
│       └── PelatihanSeeder.php
├── resources/
│   ├── css/app.css (Tailwind imports)
│   ├── js/app.js (Chart.js setup)
│   └── views/
│       ├── layout/app.blade.php
│       ├── dashboard/index.blade.php
│       ├── progress/index.blade.php
│       └── pelatihan/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── show.blade.php
└── storage/
    └── app/public/sertifikat/ (Upload directory)
```

## 🔒 Security Features

### Input Validation
- **Form Validation**: Laravel validation rules untuk semua input
- **File Upload**: Validasi tipe dan ukuran file sertifikat
- **XSS Protection**: Blade templating otomatis escape HTML
- **CSRF Protection**: Token CSRF pada semua form

### Data Protection
- **Gitignore Seeders**: File seeder dengan data real tidak di-commit
- **Environment Variables**: Konfigurasi sensitif di .env
- **File Storage**: Sertifikat disimpan dengan path yang aman

## 📈 Performance Optimizations

### Database
- **Eager Loading**: with() untuk menghindari N+1 queries
- **Indexing**: Foreign keys dan search fields
- **Pagination**: Paginate untuk data besar

### Frontend
- **Asset Optimization**: Vite untuk bundling dan minification
- **CSS Purging**: Tailwind CSS purge untuk file size optimal
- **Image Optimization**: Lazy loading untuk images (siap implementasi)

## 🎯 Conversion Details

### Dari Prototype HTML ke Laravel
1. **Static → Dynamic**: HTML statis dikonversi ke Blade templates
2. **Dummy → Real**: Data dummy diganti dengan database real
3. **CSS → Tailwind**: Styling dikonversi ke Tailwind utilities
4. **Mock → Functional**: Function mockup diimplementasi dengan Laravel

### UI Consistency
- **Layout Matching**: UI identik dengan prototype original
- **Color Preservation**: Warna dan styling tetap konsisten
- **Responsive Behavior**: Behavior responsive dipertahankan
- **Interactive Elements**: Semua interaksi berfungsi sepenuhnya

## 🚦 Development Status

### ✅ Completed
- [x] Database schema dan migrations
- [x] Models dengan relationships
- [x] Seeders dengan data real
- [x] Controllers dengan CRUD operations  
- [x] Views dengan Tailwind styling
- [x] File upload functionality
- [x] Form validation
- [x] Dashboard analytics
- [x] Progress tracking
- [x] Responsive design

### 🔄 Ready for Enhancement
- [ ] User authentication & authorization
- [ ] Export data (Excel/PDF)
- [ ] Email notifications
- [ ] Advanced reporting
- [ ] API endpoints
- [ ] Multi-language support

### TODO: Migrasi ke SPA (Inertia + Vue 3) — untuk dilanjutkan besok
Berikut langkah terstruktur yang bisa dikerjakan bertahap untuk mengubah aplikasi menjadi SPA menggunakan Inertia (Vue 3). Simpan ini di README supaya tim bisa melanjutkan besok.

1. Persiapan paket
    - composer require inertiajs/inertia-laravel
    - npm install vue@3 @inertiajs/inertia @inertiajs/inertia-vue3 @inertiajs/progress

2. Middleware & konfigurasi server
    - Buat/registrasi `HandleInertiaRequests` (inertia-laravel menyediakan stub).
    - Tambahkan middleware ke grup web di `app/Http/Kernel.php`.

3. Root view & entry JS
    - Tambah `resources/views/app.blade.php` dengan `@inertiaHead` dan `@inertia`.
    - Ubah `resources/js/app.js` menjadi entry Inertia + Vue (createInertiaApp).

4. Migrasi halaman secara bertahap (recommended)
    # SIDIKLAT — Analisis & Panduan Singkat

    README ini berisi ringkasan analisis aplikasi, arsitektur, cara menjalankan, instruksi seeding, dan catatan pengembangan (termasuk status migrasi ke Inertia/Vue).

    ## Checklist singkat (apa yang ada di repo saat ini)
    - Backend: Laravel 12 (kode menargetkan PHP 8.2+)
    - Frontend: Vite + Tailwind + Inertia + Vue 3 (migrasi Inertia sedang berlangsung)
    - Database: SQLite (default dev), migrations dan seeders tersedia
    - Assets: `resources/js` berisi bootstrap Inertia/Vue dan `resources/js/Pages` untuk halaman Vue

    ## Ringkasan arsitektur
    - Server: Laravel meng-handle routing dan controller; banyak controller mereturn Inertia::render saat ini (migrasi ke SPA sebagian sudah diterapkan).
    - Frontend: Inertia.js + Vue 3 single-page pages di `resources/js/Pages/*` dengan `resources/js/Layouts/AppLayout.vue` sebagai shared layout.
    - Build: Vite digunakan untuk bundling; plugin Vue sudah dikonfigurasi di `vite.config.js`.
    - Routes: didefinisikan di `routes/web.php` (dashboard, progress, pelatihan resource).
    - Models: `Pegawai` (hasMany Pelatihan) dan `Pelatihan` (belongsTo Pegawai). JP (Jam Pelajaran) tercatat di masing-masing pelatihan dan dijumlahkan ke `pegawai.jp_tercapai`.

    ## Lokasi file penting
    - Entry view server-side: `resources/views/app.blade.php` (Inertia root, berisi `@routes` untuk Ziggy dan `@inertiaHead`).
    - JS entry: `resources/js/app.js` (createInertiaApp, global route helper menggunakan Ziggy jika tersedia).
    - Vue pages: `resources/js/Pages/` (Dashboard, Pelatihan, Progress, dsb.)
    - Layouts/Components: `resources/js/Layouts/` dan `resources/js/Components/`.
    - Seeders: `database/seeders/` (PegawaiSeeder, PelatihanSeeder, MultiPelatihanSeeder).

    ## Cara menjalankan (development, Windows cmd.exe)
    1) Pasang dependensi PHP & JS jika belum:
    ```cmd
    composer install
    npm install
    ```

    2) Setup environment dan database (contoh SQLite):
    ```cmd
    copy .env.example .env
    php artisan key:generate
    type NUL > database\database.sqlite
    ```

    3) Migrate + seed (non-destruktif):
    ```cmd
    php artisan migrate
    php artisan db:seed
    ```

    Jika ingin reset penuh (HATI‑HATI — menghapus semua data):
    ```cmd
    php artisan migrate:fresh --seed
    ```

    4) Jalankan dev servers (Vite + Laravel). Di Windows gunakan dua terminal atau tool seperti `concurrently`:
    ```cmd
    npm run dev
    php artisan serve
    ```

    ## Seeder & troubleshooting cepat
    - Jika Anda menambahkan file seeder baru, jalankan:
    ```cmd
    composer dump-autoload
    ```
    Kemudian jalankan seeder tertentu:
    ```cmd
    php artisan db:seed --class=Database\\Seeders\\MultiPelatihanSeeder
    ```
    Catatan Windows: bila terjadi masalah escaping di cmd, bungkus argumen class dengan tanda kutip seperti di atas. Jika muncul error "Target class ... does not exist", pastikan `composer dump-autoload` dieksekusi dan path namespace di file seeder sesuai (`namespace Database\Seeders;`).

    Verifikasi cepat lewat Tinker:
    ```cmd
    php artisan tinker
    >>> App\\Models\\Pegawai::where('nama_lengkap','LIKE','%Muhammad Rafi%')->first()->pelatihans()->count()
    >>> App\\Models\\Pegawai::where('nama_lengkap','LIKE','%Muhammad Rafi%')->first()->jp_tercapai
    ```

    ## Catatan pengembangan / temuan penting
    - Inertia migration: beberapa controller sudah mereturn Inertia::render — frontend sudah berisi `resources/js/Pages/*` dan `createInertiaApp` bootstrap.
    - Ziggy digunakan untuk menyediakan helper `route()` di JS; pastikan `@routes` ada di `resources/views/app.blade.php`.
    - Vite membutuhkan `@vite` di Blade untuk memuat assets di server-side render.
    - Teleport/tag `<style>` harus berada di blok `<style>` SFC — ada perbaikan di `AppLayout.vue` sebelumnya untuk menghindari error build.
    - Saat menambah seeder baru di Windows, perhatikan escaping backslash di argumen `--class`.

    ## Rekomendasi pekerjaan lanjutan (prioritas)
    1. Lengkapi migrasi halaman kunci ke Inertia/Vue (mulai dari `pelatihan.index`).
    2. Tambah otentikasi dan otorisasi (Laravel Breeze / Sanctum) jika aplikasi akan multi-user.
    3. Tambah test otomatis untuk controller & seeder critical flows.
    4. Pertimbangkan normalisasi tanggal (`tanggal_mulai`/`tanggal_selesai`) ke format date untuk kemudahan filter dan chart.
    5. Implementasi export (CSV/Excel) dan API endpoint jika diperlukan.

    ## Quick links untuk developer
    - Routes: `routes/web.php`
    - JS entry: `resources/js/app.js`
    - Vue pages: `resources/js/Pages/`
    - Layouts: `resources/js/Layouts/`
    - Seeders: `database/seeders/`

    ---

    Dokumentasi terakhir diperbarui otomatis berdasarkan struktur kode di branch `migrate-to-inertia`.
