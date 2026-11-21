export const FPS = 30;
export const WIDTH = 1080;
export const HEIGHT = 1920;
export const TOTAL_FRAMES = 1200;

export type SceneId =
  | "hook"
  | "problem"
  | "solution"
  | "katalog"
  | "booking"
  | "approval"
  | "agenda"
  | "benefits"
  | "cta";

export type SceneDefinition = {
  id: SceneId;
  from: number;
  duration: number;
  label: string;
  voFile: string;
  vo: string;
};

/**
 * Timecode mengikuti naskah 40 detik @ 30 fps.
 * Total: 1200 frame.
 */
export const SCENES: SceneDefinition[] = [
  {
    id: "hook",
    from: 0,
    duration: 76,
    label: "1. Hook",
    voFile: "vo/01-hook.mp3",
    vo: "Booking lab lewat chat, balasannya kapan?",
  },
  {
    id: "problem",
    from: 76,
    duration: 172,
    label: "2. Masalah",
    voFile: "vo/02-masalah.mp3",
    vo: "Tanya admin, nunggu balasan, eh ternyata jadwalnya bentrok. Ulangi lagi dari awal.",
  },
  {
    id: "solution",
    from: 248,
    duration: 126,
    label: "3. Solusi",
    voFile: "vo/03-solusi.mp3",
    vo: "Kenalin, LAB BOOKING. Satu tempat untuk jadwal lab PKK UPI.",
  },
  {
    id: "katalog",
    from: 374,
    duration: 124,
    label: "4a. Katalog",
    voFile: "vo/04a-katalog.mp3",
    vo: "Lihat daftar lab: lokasi, kapasitas, fasilitas, dan statusnya.",
  },
  {
    id: "booking",
    from: 498,
    duration: 120,
    label: "4b. Booking",
    voFile: "vo/04b-booking.mp3",
    vo: "Pilih tanggal dan jam, sistem langsung cek jadwal bentrok.",
  },
  {
    id: "approval",
    from: 618,
    duration: 116,
    label: "4c. Persetujuan",
    voFile: "vo/04c-persetujuan.mp3",
    vo: "Admin menyetujui atau menolak, lengkap dengan catatan.",
  },
  {
    id: "agenda",
    from: 734,
    duration: 86,
    label: "4d. Agenda",
    voFile: "vo/04d-agenda.mp3",
    vo: "Cek agenda harian lab, tinggal filter tanggal.",
  },
  {
    id: "benefits",
    from: 820,
    duration: 170,
    label: "5. Manfaat",
    voFile: "vo/05-manfaat.mp3",
    vo: "Hemat waktu, status booking transparan, dan jadwal bentrok langsung ketahuan.",
  },
  {
    id: "cta",
    from: 990,
    duration: 210,
    label: "6. CTA",
    voFile: "vo/06-cta.mp3",
    vo: "Ajukan booking lab sekarang di LAB BOOKING. Link-nya ada di bio.",
  },
];

/** Tujuan QR di outro (terbaca dari public/assets/qr.png). */
export const APP_URL = "https://q.me-qr.com/0djlfwzf";
