# LAB BOOKING

LAB BOOKING adalah aplikasi web untuk mengajukan, menyetujui, dan memantau jadwal pemakaian laboratorium di lingkungan PKK UPI. Aplikasi dibangun dengan **PHP Native + MySQL** dan dijalankan menggunakan **Laragon**.

Nama resmi aplikasi adalah **LAB BOOKING**. Nama ini digunakan secara konsisten pada logo, halaman login, dashboard admin dan pengguna, sidebar, navbar, judul browser, metadata halaman, serta halaman informasi aplikasi.

## Fitur

- Autentikasi login, registrasi pengguna, dan logout dengan proteksi CSRF.
- Dashboard terpisah untuk **Admin** dan **Pengguna**.
- Katalog laboratorium lengkap dengan lokasi, kapasitas, fasilitas, dan status.
- Pengajuan booking dengan validasi tanggal, jam operasional, kapasitas, dan deteksi jadwal bentrok.
- Persetujuan atau penolakan booking oleh admin disertai catatan.
- Pembatalan booking oleh pemohon atau admin.
- Agenda harian laboratorium dan filter berdasarkan tanggal serta lab.
- Riwayat status booking dengan tampilan timeline.
- Manajemen pengguna, peran, status akun, dan reset password oleh admin.
- Profil pengguna dan perubahan password.
- Log aktivitas dan ringkasan statistik pada dashboard admin.
- Antarmuka responsif untuk desktop, tablet, dan ponsel.

## Teknologi

| Komponen | Keterangan |
| --- | --- |
| Bahasa | PHP Native 8.1+ |
| Basis data | MySQL 8 (nama database: `lab_booking`) |
| Akses data | PDO prepared statements |
| Frontend | HTML5, CSS3, JavaScript |
| Lingkungan | Laragon (Apache/Nginx + MySQL) |
| Folder proyek | `pkk_upi` |

## Kebutuhan Sistem

- Laragon dengan PHP 8.1 atau lebih baru.
- MySQL 8.x yang berjalan.
- Ekstensi PHP `pdo_mysql` dan `mbstring` aktif.
- Peramban modern (Chrome, Edge, Firefox, atau Safari).

## Instalasi di Laragon

1. Pastikan folder proyek berada di:

   ```text
   C:\laragon\www\pkk_upi
   ```

2. Jalankan **Apache** (atau Nginx) dan **MySQL** melalui Laragon.

3. Impor basis data:

   - Buka `http://localhost/phpmyadmin`.
   - Pilih menu **Import**.
   - Pilih berkas `database/lab_booking.sql`.
   - Jalankan impor.

   Alternatif melalui terminal:

   ```bash
   mysql -u root -p < database/lab_booking.sql
   ```

   Berkas SQL akan membuat database `lab_booking`, seluruh tabel, data lab, pengguna demo, dan contoh booking. **Mengimpor ulang berkas ini akan mengatur ulang data.**

4. Sesuaikan kredensial database pada `config/config.php` jika diperlukan:

   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', '3306');
   define('DB_NAME', 'lab_booking');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

5. Buka aplikasi:

   - Virtual host Laragon: `http://pkk_upi.test`
   - Atau melalui subfolder: `http://localhost/pkk_upi`

## Akun Demo

| Peran | Email | Password |
| --- | --- | --- |
| Admin | `admin@labbooking.test` | `Admin@123` |
| Pengguna | `user@labbooking.test` | `User@123` |

Akun pengguna tambahan (`siti@labbooking.test`, `nadia@labbooking.test`, `dimas@labbooking.test`) menggunakan password `Budi@123`.

> Segera ganti password akun demo setelah aplikasi digunakan untuk data nyata.

## Alur Status Booking

```text
Menunggu → Disetujui → Selesai
    ↓
 Ditolak

Menunggu / Disetujui → Dibatalkan
```

- **Menunggu**: pengajuan baru yang belum ditinjau admin.
- **Disetujui**: jadwal disetujui dan masuk ke agenda laboratorium.
- **Ditolak**: pengajuan tidak disetujui, disertai alasan dari admin.
- **Dibatalkan**: booking dihentikan oleh pemohon atau admin.
- **Selesai**: waktu pemakaian telah berakhir; status diperbarui otomatis.

## Struktur Proyek

```text
pkk_upi/
├─ api/
│  └─ slots.php               # API ketersediaan jadwal untuk formulir booking
├─ assets/
│  ├─ css/app.css             # Seluruh tampilan LAB BOOKING
│  ├─ img/favicon.svg         # Ikon aplikasi
│  └─ js/app.js               # Interaksi antarmuka
├─ bookings/                  # Daftar, detail, pembuatan, dan aksi booking
├─ config/
│  ├─ config.php              # Identitas aplikasi, sesi, dan konfigurasi database
│  └─ database.php            # Koneksi PDO
├─ database/
│  └─ lab_booking.sql         # Skema dan data demo
├─ includes/
│  ├─ auth-header.php         # Kerangka halaman autentikasi
│  ├─ auth-footer.php
│  ├─ footer.php              # Penutup layout dashboard
│  ├─ functions.php           # Helper autentikasi, validasi, format, dan booking
│  ├─ header.php              # Sidebar, navbar, dan layout dashboard
│  └─ icons.php               # Kumpulan ikon SVG
├─ labs/                      # Katalog dan manajemen laboratorium
├─ users/                     # Manajemen pengguna (admin)
├─ about.php                  # Informasi aplikasi
├─ dashboard.php              # Dashboard admin dan pengguna
├─ index.php                  # Pengalihan halaman awal
├─ login.php
├─ logout.php
├─ register.php
├─ profile.php
└─ schedule.php               # Agenda laboratorium
```

## Keamanan

- Password disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.
- Seluruh query database menggunakan prepared statement PDO.
- Formulir POST dilindungi token CSRF.
- Akses halaman dibatasi berdasarkan sesi dan peran (`admin` / `user`).
- Percobaan login dibatasi maksimal 5 kali dalam 5 menit.
- Input ditampilkan dengan escaping `htmlspecialchars()`.
- Berkas SQL, konfigurasi, dan direktori internal dilindungi melalui `.htaccess` pada Apache.

## Catatan Penamaan

- Nama aplikasi: **LAB BOOKING**
- Nama database: `lab_booking`
- Folder proyek: `pkk_upi`
- Basis teknologi: PHP Native + MySQL dengan Laragon

Gunakan **LAB BOOKING** pada seluruh antarmuka, dokumentasi, dan komunikasi terkait aplikasi ini.

## Lisensi

Dibuat untuk kebutuhan pengelolaan laboratorium PKK UPI. Hak penggunaan dan pengembangan mengikuti ketentuan institusi terkait.

---

**LAB BOOKING** · Versi 1.0.0 · PKK UPI · 2026
