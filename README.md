# Sistem Absensi Magang

Aplikasi absensi peserta magang berbasis **Laravel 10**. Sistem mendukung absensi berbasis lokasi dan foto, pengajuan izin, pengelolaan pengguna berdasarkan peran, rekap data, unduhan Excel, serta pemasangan website sebagai aplikasi melalui PWA.

## Fitur Utama

### Siswa

- Absen masuk dan keluar menggunakan foto kamera.
- Kamera depan menjadi kamera bawaan dan dapat dialihkan ke kamera belakang.
- Foto dikompres otomatis menjadi JPEG dengan ukuran maksimal 200 KB.
- Pengecekan posisi terhadap radius gedung menggunakan GPS.
- Status kerja WFO atau WFH berdasarkan jarak dari gedung.
- Status ketepatan waktu: Tepat Waktu atau Terlambat.
- Pengajuan izin beserta keterangan dan dokumen pendukung.
- Riwayat absensi bulanan.

### Guru

- Melihat rekap siswa yang ditetapkan sebagai siswa bimbingan.
- Memfilter data berdasarkan siswa, tanggal, bulan, dan tahun.
- Mengunduh rekap absensi sebagai file Excel `.xlsx`.

### Pegawai

- Melihat rekap siswa berdasarkan gedung.
- Memfilter data berdasarkan siswa, tanggal, bulan, dan tahun.
- Melihat foto dan lokasi absensi.
- Mengunduh rekap absensi sebagai file Excel `.xlsx`.

### Admin

- Mengelola akun admin, siswa, pegawai, dan guru.
- Menetapkan siswa bimbingan kepada guru.
- Mengelola gedung, koordinat, dan radius absensi.
- Mengatur batas jam keterlambatan.
- Mengelola jenis izin.
- Melihat log aktivitas pengguna.

### PWA

- Website dapat dipasang ke layar utama Android dan iPhone.
- Berjalan dalam mode standalone seperti aplikasi.
- Menggunakan ikon MagangHub Bunder.
- Mendukung fitur **Ingat saya** agar pengguna tidak perlu login setiap kali aplikasi ditutup.

## Teknologi

- PHP 8.1 atau lebih baru
- Laravel 10
- MySQL atau MariaDB
- Laravel Blade
- Tailwind CSS melalui CDN
- Maatwebsite Laravel Excel
- PHP GD untuk kompresi foto
- JavaScript MediaDevices API untuk kamera
- Geolocation API untuk lokasi
- PWA manifest dan service worker

## Persyaratan Server

Pastikan server mempunyai:

```text
PHP >= 8.1
Composer
MySQL/MariaDB
PHP extensions: bcmath, ctype, fileinfo, gd, json, mbstring,
openssl, pdo_mysql, tokenizer, xml, zip
HTTPS
```

HTTPS wajib digunakan agar kamera, GPS, service worker, dan PWA dapat bekerja pada perangkat seluler.

## Instalasi Lokal

1. Masuk ke direktori proyek:

   ```bash
   cd /path/ke/absen
   ```

2. Instal dependency PHP:

   ```bash
   composer install
   ```

3. Buat file `.env`:

   ```bash
   cp .env.example .env
   ```

   Jika `.env.example` belum tersedia, buat `.env` dengan konfigurasi minimal berikut:

   ```env
   APP_NAME="Absensi"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   LOG_CHANNEL=stack
   LOG_LEVEL=debug

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username_database
   DB_PASSWORD=password_database

   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   ```

4. Buat application key:

   ```bash
   php artisan key:generate
   ```

5. Jalankan migration untuk database baru:

   ```bash
   php artisan migrate
   ```

6. Buat symbolic link storage:

   ```bash
   php artisan storage:link
   ```

7. Pastikan direktori runtime dapat ditulis:

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

8. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

9. Buka:

   ```text
   http://127.0.0.1:8000
   ```

## Konfigurasi Hosting

Gunakan konfigurasi production berikut pada `.env`:

```env
APP_NAME="Absensi"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://absensi.example.com

DB_CONNECTION=mysql
DB_HOST=host_database
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_database
DB_PASSWORD=password_database

SESSION_DRIVER=file
SESSION_LIFETIME=43200
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

`SESSION_LIFETIME=43200` setara dengan 30 hari untuk sesi biasa. Fitur **Ingat saya** tetap memakai cookie autentikasi Laravel yang berumur panjang.

Sesudah mengunggah perubahan, jalankan:

```bash
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Pastikan permission benar:

```bash
mkdir -p storage/app/public/absen
chmod -R 775 storage bootstrap/cache
```

## Database Baru dan Database Lama

### Database baru

Jalankan seluruh migration:

```bash
php artisan migrate --force
```

### Database lama yang tabelnya sudah tersedia

Jika tabel sudah ada tetapi tabel `migrations` belum mencatat migration lama, jangan langsung menjalankan seluruh migration karena Laravel akan mencoba membuat ulang tabel seperti `users`.

Untuk menambahkan kolom fitur **Ingat saya**, jalankan migration secara spesifik:

```bash
php artisan migrate --force \
  --path=database/migrations/2026_08_11_000000_add_remember_token_to_users_table.php
```

Verifikasi:

```bash
php artisan tinker --execute="dump(Schema::hasColumn('users', 'remember_token'));"
```

Hasil yang benar adalah `true`.

## Penyimpanan Foto

Foto absensi disimpan secara fisik di:

```text
storage/app/public/absen/YYYY-MM/nama-file.jpg
```

Database hanya menyimpan path relatif:

```text
YYYY-MM/nama-file.jpg
```

URL publik yang digunakan aplikasi:

```text
/storage/absen/YYYY-MM/nama-file.jpg
```

Aplikasi menyediakan route fallback untuk shared hosting yang tidak dapat membaca symbolic link `public/storage`.

Tes kemampuan menulis storage:

```bash
php artisan tinker --execute='$disk=Storage::disk("public"); $ok=$disk->put("absen/storage-test.txt", "ok"); dump($ok, $disk->exists("absen/storage-test.txt")); $disk->delete("absen/storage-test.txt");'
```

Hasil yang benar:

```text
true
true
```

Foto baru tidak akan dicatat ke database apabila proses penyimpanan file gagal.

## Konfigurasi Kamera

- Kamera depan digunakan secara default.
- Tombol **Gunakan Kamera Belakang/Depan** tersedia di modal kamera.
- Preview kamera depan menggunakan efek cermin.
- Hasil kamera belakang tidak dibalik.
- Foto dikompres di browser dan diperiksa ulang di server.
- Server harus mengaktifkan ekstensi PHP GD.

Jika kamera tidak muncul di iPhone:

1. Pastikan website menggunakan HTTPS.
2. Buka **Settings → Safari → Camera** dan izinkan akses.
3. Jika memakai PWA, hapus lalu pasang ulang pintasan setelah pembaruan besar.

## Fitur Ingat Saya

Fitur ini membutuhkan:

- Kolom `remember_token` pada tabel `users`.
- Checkbox **Ingat saya di perangkat ini** dicentang saat login.
- Cookie browser tidak dihapus.
- Middleware redirect sesuai dengan peran pengguna.

Menutup PWA dari recent apps tidak menghapus login. Pengguna tetap diminta login ulang apabila:

- Menekan tombol Logout.
- Menghapus data Safari/Chrome.
- Menggunakan mode private/incognito.
- Menghapus aplikasi beserta data situs.
- Administrator mengubah token atau password akun.

## Pemasangan sebagai Aplikasi

### Android

1. Buka website menggunakan Chrome.
2. Tekan tombol **Pasang Aplikasi**, atau buka menu Chrome.
3. Pilih **Install app** atau **Tambahkan ke layar utama**.

### iPhone

1. Buka website menggunakan Safari.
2. Tekan tombol **Share**.
3. Pilih **Add to Home Screen**.

File PWA utama:

```text
manifest.webmanifest
service-worker.js
icons/
resources/views/partials/pwa.blade.php
```

Setelah memperbarui PWA, ubah nama cache di `service-worker.js` jika aset lama masih tersimpan, lalu jalankan:

```bash
php artisan optimize:clear
```

## Ekspor Excel

Guru dan pegawai dapat mengunduh rekap berdasarkan filter yang aktif. File yang dihasilkan berformat `.xlsx` dan berisi:

- Nama siswa
- Tanggal
- Jam masuk dan pulang
- Status kehadiran
- Status ketepatan
- WFO/WFH
- Durasi kerja
- Keterangan izin
- URL foto masuk dan keluar
- URL lokasi Google Maps

Implementasi ekspor berada di:

```text
app/Exports/RekapAbsensiExport.php
```

## Penetapan Siswa Bimbingan

Dropdown siswa pada akun guru hanya menampilkan siswa yang telah ditetapkan oleh admin.

Alurnya:

1. Login sebagai admin.
2. Tambah atau edit akun guru.
3. Pilih satu atau beberapa siswa yang diawasi.
4. Simpan perubahan.
5. Login ulang sebagai guru.

Relasi disimpan di tabel pivot `guru_siswa`.

## Peran dan Halaman Utama

| Peran | Halaman utama |
|---|---|
| Admin | `/admin/dashboard` |
| Pegawai | `/pegawai/rekap-absensi` |
| Siswa | `/siswa/absen` |
| Guru | `/guru/rekap-absensi` |

Middleware akan mengarahkan pengguna ke halaman sesuai perannya tanpa melakukan logout ketika pengguna membuka URL milik peran lain.

## Perintah Operasional

Membersihkan seluruh cache Laravel:

```bash
php artisan optimize:clear
```

Melihat daftar route:

```bash
php artisan route:list
```

Memeriksa route foto:

```bash
php artisan route:list --path=storage/absen
```

Memeriksa log aplikasi:

```bash
tail -f storage/logs/laravel.log
```

Memeriksa apakah file foto tersedia:

```bash
php artisan tinker --execute="dump(Storage::disk('public')->exists('absen/YYYY-MM/nama-file.jpg'));"
```

## Troubleshooting

### `ERR_NAME_NOT_RESOLVED`

Record DNS subdomain belum ada. Tambahkan record `A` atau `CNAME` pada penyedia DNS yang menjadi nameserver aktif.

### Foto menghasilkan 404

Periksa file fisik dan route:

```bash
php artisan route:list --path=storage/absen
php artisan tinker --execute="dump(Storage::disk('public')->exists('absen/YYYY-MM/nama-file.jpg'));"
```

Jika hasil `exists` adalah `false`, database mempunyai nama file tetapi file tidak tersedia pada storage server.

### `Table 'users' already exists` ketika migrate

Database lama belum mempunyai riwayat migration yang sesuai. Jangan menghapus tabel. Jalankan hanya migration baru menggunakan opsi `--path` seperti pada bagian **Database Lama**.

### Guru tidak dapat memilih siswa

Pastikan akun mempunyai role `guru` dan relasi pada `guru_siswa` sudah dibuat melalui halaman admin.

### Riwayat menampilkan Izin meskipun sudah absen

Status kehadiran memprioritaskan `jam_masuk`. Absen masuk juga membersihkan flag dan data izin untuk tanggal yang sama.

### PWA masih memakai tampilan lama

```bash
php artisan optimize:clear
```

Kemudian tutup aplikasi, hapus cache situs, atau hapus dan pasang ulang pintasan PWA.

## Keamanan Production

- Gunakan `APP_DEBUG=false`.
- Jangan commit atau membagikan file `.env`.
- Gunakan password database yang kuat.
- Batasi Remote MySQL hanya untuk IP server yang diperlukan.
- Jangan membuka port MySQL ke seluruh internet menggunakan wildcard `%` jika tidak diperlukan.
- Gunakan HTTPS dan cookie secure.
- Pastikan `storage` dan `bootstrap/cache` dapat ditulis, tetapi jangan memberikan permission `777` kecuali hanya untuk diagnosis sementara.
- Lakukan backup database dan folder `storage/app/public` secara berkala.

## Struktur Direktori Penting

```text
app/Http/Controllers/   Controller aplikasi
app/Models/             Model Eloquent
app/Exports/            Generator laporan Excel
database/migrations/    Struktur dan perubahan database
resources/views/        Tampilan Blade
routes/web.php          Route aplikasi
storage/app/public/     File upload pengguna
icons/                  Ikon PWA
manifest.webmanifest    Konfigurasi PWA
service-worker.js       Service worker PWA
```

## Lisensi

Proyek ini menggunakan Laravel yang dilisensikan di bawah lisensi MIT. Sesuaikan ketentuan distribusi aplikasi internal dengan kebijakan organisasi.
