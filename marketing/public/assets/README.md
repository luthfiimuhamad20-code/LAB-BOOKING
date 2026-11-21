# Screenshot aplikasi

Taruh screenshot di folder ini. Nama file harus persis seperti daftar di bawah.
Selama file belum ada, komposisi memakai mock UI bawaan sehingga tetap bisa
dirender.

| File | Halaman aplikasi | Dipakai di |
|---|---|---|
| `login.png` | `login.php` | Scene 3 (opsional) |
| `katalog.png` | `labs/index.php` | Scene 4a |
| `booking.png` | `bookings/create.php` | Scene 4b |
| `approval.png` | `bookings/view.php` (tampilan admin) | Scene 4c |
| `agenda.png` | `schedule.php` | Scene 4d |
| `qr.png` | QR code tujuan CTA (scan) | Scene 6 |
| `dashboard-user.png` | `dashboard.php` (user) | Cadangan |

## Cara mengambil screenshot

1. Jalankan aplikasi: `http://localhost:8000`
2. Buka DevTools → Toggle device toolbar → viewport **390 × 844**.
3. Ambil screenshot halaman (bukan full-page scroll, cukup satu layar).
4. Simpan sebagai PNG, taruh di folder ini dengan nama di atas.
5. Ganti data demo supaya konsisten dan tidak menampilkan kredensial asli.

Rasio layar ponsel di video adalah 436 × 940 (≈ 0.464). Screenshot 390 × 844
(≈ 0.462) akan pas dengan `object-fit: cover`.

## Catatan privasi

- Jangan tampilkan email/password asli.
- Blur atau ganti nama pengguna dengan data dummy.
- Pastikan tidak ada token/session yang terlihat.

## QR code outro

Simpan QR code Anda sebagai `qr.png` di folder ini (persegi, minimal 600×600 px,
latar putih). Scene 6 akan otomatis menampilkannya. Selama file belum ada,
video memakai placeholder "QR Anda".
