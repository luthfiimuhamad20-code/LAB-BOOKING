# Voice Over (ElevenLabs)

Satu file teks per scene. Copy-paste isinya ke ElevenLabs, lalu simpan hasilnya
sebagai MP3 dengan nama yang sama di folder ini.

| Scene | File teks | File audio | Timecode | Durasi | Kata |
|---|---|---|---|---|---|
| 1. Hook | `01-hook.txt` | `01-hook.mp3` | 0:00–0:02,5 | 2,5 dtk | 6 |
| 2. Masalah | `02-masalah.txt` | `02-masalah.mp3` | 0:02,5–0:08,5 | 6 dtk | 12 |
| 3. Solusi | `03-solusi.txt` | `03-solusi.mp3` | 0:08,5–0:12,7 | 4,2 dtk | 10 |
| 4a. Katalog | `04a-katalog.txt` | `04a-katalog.mp3` | 0:12,7–0:16,9 | 4,1 dtk | 8 |
| 4b. Booking | `04b-booking.txt` | `04b-booking.mp3` | 0:16,9–0:20,9 | 4 dtk | 9 |
| 4c. Persetujuan | `04c-persetujuan.txt` | `04c-persetujuan.mp3` | 0:20,9–0:24,6 | 3,7 dtk | 7 |
| 4d. Agenda | `04d-agenda.txt` | `04d-agenda.mp3` | 0:24,6–0:27,5 | 2,9 dtk | 7 |
| 5. Manfaat | `05-manfaat.txt` | `05-manfaat.mp3` | 0:27,5–0:33,0 | 5,5 dtk | 10 |
| 6. CTA | `06-cta.txt` | `06-cta.mp3` | 0:33,0–0:40,0 | 7 dtk | 11 |

Total VO: 80 kata.

File 04c, 05, dan 06 di repo ini sudah dipercepat dengan `atempo`
(1,20x / 1,12x / 1,14x) agar pas dengan timeline. Kalau generate ulang,
kirim file asli—nanti saya proses ulang.

## Setting ElevenLabs yang disarankan

- **Model:** Eleven Multilingual v2 (bukan Flash/Turbo).
- **Voice:** filter Voice Library → bahasa Indonesia.
- **Stability:** 45–55
- **Similarity:** 75
- **Style:** 0–10
- **Speed:** 1.0 (maksimal 1.05 kalau kepanjangan)
- **Format export:** MP3 128 kbps atau WAV 44.1 kHz.

## Catatan

- Pakai paket **Starter ke atas** untuk lisensi komersial. Free plan tidak boleh
  dipakai untuk konten promosi yang dipublikasikan.
- Generate **per scene**, jangan satu take 40 detik, supaya timing mudah diatur.
- Trim silence di awal/akhir file:
  `ffmpeg -i input.mp3 -af silenceremove=start_periods=1:start_silence=0.05:start_threshold=-50dB output.mp3`
- Normalisasi akhir ke **-14 LUFS integrated**, true peak **-1 dB**.
- Kalau file MP3 belum ada, komposisi tetap bisa dirender (audio otomatis dilewati).
