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

## 👥 Data Source

Data pegawai dan struktur organisasi berasal dari **Inspektorat Kota Banjarbaru** dengan 14 pegawai dari berbagai unit kerja dan tingkat pangkat/golongan.

## 📞 Support

Untuk pertanyaan atau masalah terkait aplikasi ini, silakan buat issue di repository atau hubungi tim development.

---

**Dibuat dengan ❤️ menggunakan Laravel 11 & Tailwind CSS**

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

**Dibuat dengan ❤️ menggunakan Laravel 11 & Tailwind CSS**
