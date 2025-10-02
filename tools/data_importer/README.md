# SIDIKLAT Data Importer

Toolkit untuk mengimpor data pelatihan ke aplikasi SIDIKLAT dari dua sumber utama:

1. **Berkas Excel** yang berisi daftar pelatihan beserta tautan sertifikat.
2. **Portal SIMPEG BKPP Kota Banjarbaru** yang memerlukan proses login untuk mengambil riwayat diklat.

## Struktur

```
tools/data_importer/
├── data_importer/
│   ├── __init__.py
│   ├── __main__.py
│   ├── certificate_downloader.py
│   ├── cli.py
│   ├── config.py
│   ├── db.py
│   ├── excel_loader.py
│   ├── gui.py
│   ├── models.py
│   ├── simpeg_scraper.py
│   └── utils.py
├── README.md
└── requirements.txt
```

## Instalasi

1. Buat dan aktifkan virtual environment Python 3.10+.
2. Install dependensi:

   ```bash
   pip install -r tools/data_importer/requirements.txt
   playwright install chromium
   ```

3. Salin variabel koneksi database dan kredensial SIMPEG ke environment. Contoh menggunakan file `.env` pada root proyek Laravel. Variabel database hanya diperlukan jika Anda ingin langsung menulis ke database, bukan ketika menghasilkan berkas `.sql`:

   ```dotenv
   SIDIKLAT_DB_HOST=127.0.0.1
   SIDIKLAT_DB_PORT=3306
   SIDIKLAT_DB_NAME=sidiklat
   SIDIKLAT_DB_USER=root
   SIDIKLAT_DB_PASSWORD=secret

   SIMPEG_BASE_URL=https://simpeg.bkpp.banjarbarukota.go.id
   SIMPEG_USERNAME=isi_username
   SIMPEG_PASSWORD=isi_password
   SIMPEG_HEADLESS=true

   SIDIKLAT_CERTIFICATE_ROOT=storage/app/public/sertifikat
   ```

   Secara default berkas sertifikat akan ditempatkan di `storage/app/public/sertifikat` sehingga Laravel dapat melayaninya melalui disk `public`.

## Penggunaan

Jalankan CLI dengan perintah berikut dari root repository:

```bash
python -m data_importer --help
```

### Mode GUI (Excel)

Untuk antarmuka sederhana yang memungkinkan pemilihan berkas Excel lewat dialog file dan menjalankan impor tanpa terminal, gunakan:

```bash
python -m data_importer.gui
```

GUI menyediakan dua tombol:

- **Import ke Database** – menjalankan proses langsung ke database menggunakan konfigurasi pada environment.
- **Generate SQL…** – membuka dialog penyimpanan dan menghasilkan skrip `.sql` yang siap dijalankan di MySQL.

### Impor dari Excel

```bash
python -m data_importer import-excel data/daftar_pelatihan.xlsx --sheet "Sheet1"
```

Kolom yang dikenali secara default:

- `nama`
- `nip`
- `jabatan`
- `unit_kerja`
- `nama_pelatihan`
- `jenis_pelatihan`
- `penyelenggara`
- `tempat`
- `tanggal_mulai`
- `tanggal_selesai`
- `jp`
- `sertifikat_url`
- `keterangan`

Sertifikat akan otomatis diunduh dan dinamai ulang secara ringkas.

Untuk menghasilkan skrip SQL tanpa menyentuh database:

```bash
python -m data_importer import-excel data/daftar_pelatihan.xlsx --sheet "Sheet1" --sql-output output/pelatihan_excel.sql
```

### Impor dari SIMPEG

```bash
python -m data_importer import-simpeg --categories kepemimpinan fungsional teknis
```

Tambahkan `--sql-output` agar seluruh hasil scraping disusun sebagai pernyataan SQL:

```bash
python -m data_importer import-simpeg --categories kepemimpinan fungsional teknis --sql-output output/pelatihan_simpeg.sql
```

Perintah di atas akan:

1. Login ke SIMPEG menggunakan kredensial environment.
2. Mengambil daftar pegawai pada halaman pencarian.
3. Membuka halaman detail masing-masing pegawai.
4. Men-scrape tabel diklat kepemimpinan, fungsional, dan teknis.
5. Mengunduh berkas sertifikat dan menyimpannya di `storage/app/public/sertifikat`.
6. Menyimpan/menyinkronkan data ke tabel `pegawais`, `jenis_pelatihans`, dan `pelatihans`.

## Catatan Teknis

- Script menyamakan jenis pelatihan berdasarkan kategori tab di SIMPEG. Nama jenis dapat diubah melalui konstanta `CATEGORY_LABELS` jika diperlukan.
- Data JP (`jp_tercapai`) pegawai akan disesuaikan secara otomatis setiap kali pelatihan baru ditambahkan atau diperbarui.
- Untuk keamanan, kredensial SIMPEG tidak disimpan di dalam kode dan harus disediakan melalui environment.
- Jika situs SIMPEG mengubah struktur HTML, update selektor pada `simpeg_scraper.py` mungkin diperlukan.
