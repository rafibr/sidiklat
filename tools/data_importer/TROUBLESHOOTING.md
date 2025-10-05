# Troubleshooting Guide - Data Importer

## Masalah: Sertifikat Tidak Terdownload

### Penyebab Umum:

1. **URL Tidak Valid**
   - URL sertifikat kosong atau null
   - Format URL salah (tidak ada scheme/protocol)
   - URL mengarah ke halaman yang tidak ada (404)

2. **Masalah Koneksi**
   - Timeout koneksi (lebih dari 60 detik)
   - Server tidak dapat diakses
   - Firewall memblok akses

3. **Masalah Autentikasi**
   - URL memerlukan login/autentikasi
   - Token/session sudah expired
   - Akses ditolak (403 Forbidden)

4. **Masalah Permission/Storage**
   - Direktori tujuan tidak dapat ditulis
   - Disk penuh
   - Path tidak valid

### Cara Debug:

1. **Cek Log Detail**
   
   Log sekarang lebih detail dan menampilkan:
   - URL yang sedang didownload
   - Status HTTP response
   - Headers response
   - Ukuran file yang terdownload
   - Path lengkap file tujuan
   
   Contoh log sukses:
   ```
   [2025-10-05 10:30:00] [data_importer.certificate_downloader] [INFO] Starting download from URL: https://example.com/cert.pdf
   [2025-10-05 10:30:00] [data_importer.certificate_downloader] [DEBUG] Response status code: 200
   [2025-10-05 10:30:01] [data_importer.certificate_downloader] [INFO] Successfully downloaded certificate: /path/to/cert.pdf (152340 bytes)
   ```

2. **Cek Error Message**
   
   Error log akan menampilkan tipe error spesifik:
   
   - **HTTP Error**: Status code tidak 2xx (404, 403, 500, dll)
     ```
     [ERROR] HTTP Error downloading certificate: Status 404 - Not Found
     ```
   
   - **Timeout Error**: Download melebihi 60 detik
     ```
     [ERROR] Timeout downloading certificate (waited 60 seconds)
     ```
   
   - **Connection Error**: Tidak bisa terhubung ke server
     ```
     [ERROR] Connection error downloading certificate: Connection refused
     ```
   
   - **Invalid URL**: Format URL salah
     ```
     [ERROR] Invalid URL format: not-a-valid-url
     ```

3. **Verifikasi Konfigurasi**
   
   Cek file `.env` atau environment variables:
   ```env
   SIDIKLAT_CERTIFICATE_ROOT=storage/app/public/sertifikat
   ```
   
   Path ini harus:
   - Dapat ditulis (writeable)
   - Ada permission yang tepat
   - Tidak melebihi quota disk

4. **Test URL Manual**
   
   Gunakan curl atau browser untuk test URL:
   ```bash
   curl -I "URL_SERTIFIKAT"
   ```
   
   Cek response header dan status code.

5. **Cek Path Output**
   
   Log akan menampilkan absolute path tujuan file:
   ```
   [INFO] Saving certificate to: C:\full\path\to\storage\sertifikat\nama-pelatihan.pdf
   ```
   
   Pastikan:
   - Directory tersebut ada atau dapat dibuat
   - Ada write permission
   - Tidak ada file dengan nama sama yang read-only

### Solusi Quick Fix:

1. **Enable DEBUG logging** untuk detail lebih:
   ```python
   logging.basicConfig(level=logging.DEBUG)
   ```

2. **Cek manual** apakah URL sertifikat valid:
   - Buka di browser
   - Pastikan tidak redirect ke halaman login
   - Pastikan file benar-benar bisa didownload

3. **Periksa permission** direktori storage:
   ```bash
   # Linux/Mac
   chmod -R 755 storage/app/public/sertifikat
   
   # Windows - pastikan folder tidak read-only
   ```

4. **Cek space disk** yang tersedia

5. **Test dengan sample URL** yang pasti work:
   ```python
   from data_importer.certificate_downloader import CertificateDownloader
   from pathlib import Path
   
   downloader = CertificateDownloader(Path("./test_downloads"))
   result = downloader.download(
       "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
       "Test User",
       "Test Training"
   )
   print(f"Downloaded to: {result}")
   ```

### Google Drive URLs

Google Drive URLs **tidak didownload** melainkan disimpan langsung sebagai URL:
```
https://drive.google.com/file/d/xxxxx/view
```

Ini by design karena Google Drive memerlukan autentikasi khusus.

### Kontribusi Log

Jika masih bermasalah, silakan share:
1. Full log output
2. URL yang bermasalah (jika tidak sensitif)
3. Error message lengkap
4. Environment (OS, Python version, dll)
