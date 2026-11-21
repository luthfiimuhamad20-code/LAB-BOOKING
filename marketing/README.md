# LAB BOOKING — Video Marketing (Remotion)

Video promosi 40 detik untuk aplikasi **LAB BOOKING** (PKK UPI), dibuat dengan
[Remotion](https://www.remotion.dev/).

- **Vertikal:** 1080 × 1920, 30 fps, 1.200 frame (40 detik) → Reels/TikTok/Shorts
- **Landscape:** 1920 × 1080 (versi pillarbox dari komposisi vertikal)
- **Font:** Plus Jakarta Sans
- **Warna brand:** `#3B5BFD`, navy `#0B1533`, gradient `#5B7FFB → #38BDF8 → #2DD4BF`

## Mulai

```bash
cd marketing
npm install
npm run studio
```

Studio akan terbuka di browser. Pilih komposisi `MarketingVertical`.

## Render

```bash
# MP4 vertikal
npm run render:vertical

# MP4 landscape
npm run render:landscape

# Satu frame untuk preview
npm run still
```

Hasil ada di folder `out/`.

## Struktur

```text
marketing/
├─ public/
│  ├─ assets/          # screenshot aplikasi (opsional)
│  ├─ audio/           # music.mp3 (opsional)
│  └─ vo/              # teks + MP3 voice over per scene
├─ src/
│  ├─ components/      # PhoneFrame, Logo, heading, audio, dll.
│  ├─ scenes/          # 9 scene sesuai naskah
│  ├─ lib/             # helper animasi & static file
│  ├─ Root.tsx         # definisi composition
│  ├─ Video.tsx        # Sequence + audio
│  ├─ script.ts        # timecode, teks VO, URL
│  └─ theme.ts         # warna, font, safe area
├─ remotion.config.ts
└─ package.json
```

## Timeline (30 fps)

| Scene | Komponen | Frame | Durasi |
|---|---|---|---|
| 1. Hook | `Scene01Hook` | 0–76 | 76 |
| 2. Masalah | `Scene02Problem` | 76–248 | 172 |
| 3. Solusi | `Scene03Solution` | 248–374 | 126 |
| 4a. Katalog | `Scene04Katalog` | 374–498 | 124 |
| 4b. Booking | `Scene04Booking` | 498–618 | 120 |
| 4c. Persetujuan | `Scene04Approval` | 618–734 | 116 |
| 4d. Agenda | `Scene04Agenda` | 734–820 | 86 |
| 5. Manfaat | `Scene05Benefits` | 820–990 | 170 |
| 6. CTA | `Scene06CTA` | 990–1200 | 210 |

Timecode terpusat di `src/script.ts`. Kalau durasi scene diubah, sesuaikan juga
`from` scene berikutnya dan `TOTAL_FRAMES`. Scene 1, 3, 4a, dan 4d dipadatkan;
scene 4c, 5, dan 6 diberi waktu lebih. Audio 04c, 05, dan 06 sudah dipercepat
tipis (1,20x / 1,12x / 1,14x) agar semua VO utuh dalam 40 detik.

## Menambahkan aset

### Voice over
Lihat `public/vo/README.md`. Simpan MP3 dengan nama yang sudah ditentukan
(`01-hook.mp3`, dst.). File audio otomatis terpakai; kalau belum ada, render
tetap jalan tanpa VO.

### Screenshot aplikasi
Lihat `public/assets/README.md`. Simpan sebagai `katalog.png`, `booking.png`,
`approval.png`, `agenda.png`. Screenshot akan otomatis menggantikan mock UI di
dalam frame ponsel.

### QR code outro
Simpan QR code Anda sebagai `public/assets/qr.png` (persegi, minimal 600×600).
Scene 6 otomatis menampilkannya di bawah tombol CTA. Tanpa file, akan tampil
placeholder "QR Anda". QR yang terpasang saat ini mengarah ke
`https://q.me-qr.com/0djlfwzf` (redirect ke repo GitHub). Kalau tujuan QR
diubah, sesuaikan juga `APP_URL` di `src/script.ts` lalu render ulang.

### Musik
Taruh `music.mp3` di `public/audio/`. Volume diatur di `src/Video.tsx`.

## Mengubah CTA / URL

Buka `src/script.ts`, ubah:

```ts
export const APP_URL = "https://q.me-qr.com/0djlfwzf"; // tujuan QR saat ini
```

URL ini tampil kecil di bawah QR outro. Kalau QR diganti, ganti juga nilainya.

## Catatan desain

- Safe area vertikal: atas 220 px, bawah 420 px, kiri 64 px, kanan 140 px.
  Teks utama berada di dalam area aman agar tidak tertutup UI TikTok/Reels.
- Semua scene memakai mock UI. Begitu screenshot asli ditambahkan, tampilan
  ponsel otomatis berganti.
- Versi landscape saat ini adalah komposisi vertikal yang di-scale dan diberi
  pillarbox. Kalau butuh layout 16:9 native, scene perlu disusun ulang.

## Lisensi aset

- Font Plus Jakarta Sans: SIL Open Font License.
- Musik dan SFX: gunakan sumber yang lisensinya jelas (Pixabay, YouTube Audio
  Library, Uppbeat, Epidemic Sound).
- ElevenLabs: butuh paket Starter ke atas untuk lisensi komersial.
