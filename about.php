<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

require_login();

$pageTitle = 'Informasi Aplikasi';
$active = 'about';

require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('info') ?>Tentang aplikasi</span>
        <h1 class="page-title">Informasi Aplikasi</h1>
        <p class="page-subtitle">Mengenal LAB BOOKING, fitur utama, dan teknologi yang digunakan.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('dashboard.php')) ?>"><?= icon('dashboard') ?>Ke Dashboard</a>
        <a class="btn btn-primary" href="<?= e(url('bookings/create.php')) ?>"><?= icon('plus') ?>Buat Booking</a>
    </div>
</div>

<section class="about-hero">
    <div class="about-mark"><?= icon('flask') ?></div>
    <div class="about-hero-copy">
        <span class="about-version"><?= icon('sparkles') ?>Versi <?= e(APP_VERSION) ?></span>
        <h2 class="about-title">LAB <span>BOOKING</span></h2>
        <p class="about-desc">
            Aplikasi pemesanan dan pengelolaan jadwal laboratorium untuk lingkungan <?= e(APP_ORGANIZATION) ?>.
            Satu tempat untuk mengajukan booking, memantau persetujuan, dan melihat agenda laboratorium.
        </p>
        <div class="about-meta">
            <span><?= icon('building') ?><?= e(APP_ORGANIZATION) ?></span>
            <span><?= icon('code') ?>PHP Native + MySQL</span>
            <span><?= icon('layers') ?>Laragon</span>
            <span><?= icon('calendar') ?><?= e(APP_YEAR) ?></span>
        </div>
    </div>
</section>

<div class="about-grid">
    <div class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Tentang LAB BOOKING</h2>
                    <p class="card-subtitle">Manfaat dan cakupan aplikasi.</p>
                </div>
            </div>
            <div class="card-body">
                <p class="prose-block">
                    LAB BOOKING adalah aplikasi berbasis web yang membantu pengguna laboratorium mengajukan jadwal
                    pemakaian secara tertib, sekaligus memudahkan admin dalam meninjau dan mengelola setiap pengajuan.
                    Ketersediaan waktu ditampilkan langsung pada formulir booking, sehingga pengguna dapat memilih
                    jadwal yang tidak bertabrakan dengan pemakaian lain.
                </p>
                <p class="prose-block" style="margin-top:12px">
                    Seluruh data laboratorium, pengguna, booking, dan riwayat status tersimpan dalam satu basis data
                    <strong>lab_booking</strong>. Aplikasi dibangun menggunakan PHP Native dan MySQL sehingga ringan,
                    mudah dipelajari, dan siap dijalankan pada lingkungan Laragon.
                </p>

                <div class="form-section" style="margin-top:20px">
                    <div class="form-section-title"><?= icon('sparkles') ?>Fitur utama</div>
                    <div class="feature-grid">
                        <div class="feature-item">
                            <span class="feature-icon"><?= icon('calendar-check') ?></span>
                            <strong>Booking terpusat</strong>
                            <p>Ajukan pemakaian laboratorium dengan detail kegiatan yang jelas.</p>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><?= icon('shield') ?></span>
                            <strong>Persetujuan admin</strong>
                            <p>Setiap pengajuan ditinjau sebelum masuk ke agenda laboratorium.</p>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><?= icon('clock') ?></span>
                            <strong>Deteksi jadwal bentrok</strong>
                            <p>Waktu yang bertabrakan dengan booking lain otomatis ditolak.</p>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><?= icon('calendar') ?></span>
                            <strong>Jadwal transparan</strong>
                            <p>Lihat agenda harian dan ketersediaan laboratorium secara real-time.</p>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><?= icon('flask') ?></span>
                            <strong>Manajemen laboratorium</strong>
                            <p>Kelola data, fasilitas, kapasitas, dan status setiap lab.</p>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"><?= icon('users') ?></span>
                            <strong>Manajemen pengguna</strong>
                            <p>Atur akun admin dan pengguna beserta hak aksesnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Panduan Singkat</h2>
                    <p class="card-subtitle">Empat langkah untuk menggunakan LAB BOOKING.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="steps">
                    <div class="step-item">
                        <span class="step-num">1</span>
                        <div>
                            <strong>Masuk ke akun Anda</strong>
                            <p>Gunakan email dan password yang terdaftar. Hubungi admin jika akun belum tersedia.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">2</span>
                        <div>
                            <strong>Pilih laboratorium dan waktu</strong>
                            <p>Periksa kapasitas, fasilitas, serta jadwal yang masih tersedia pada tanggal yang diinginkan.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">3</span>
                        <div>
                            <strong>Ajukan booking</strong>
                            <p>Lengkapi tujuan kegiatan dan jumlah peserta, lalu kirim pengajuan kepada admin.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">4</span>
                        <div>
                            <strong>Pantau status pengajuan</strong>
                            <p>Status booking dapat dilihat pada halaman Booking Saya hingga kegiatan selesai.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <aside class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Informasi Sistem</h2>
                    <p class="card-subtitle">Detail teknis aplikasi.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-table">
                    <div class="info-row">
                        <span>Nama aplikasi</span>
                        <strong>LAB BOOKING</strong>
                    </div>
                    <div class="info-row">
                        <span>Versi</span>
                        <strong><?= e(APP_VERSION) ?></strong>
                    </div>
                    <div class="info-row">
                        <span>Basis data</span>
                        <strong>lab_booking</strong>
                    </div>
                    <div class="info-row">
                        <span>Teknologi</span>
                        <strong>PHP Native + MySQL</strong>
                    </div>
                    <div class="info-row">
                        <span>Lingkungan</span>
                        <strong>Laragon</strong>
                    </div>
                    <div class="info-row">
                        <span>Folder proyek</span>
                        <strong>pkk_upi</strong>
                    </div>
                    <div class="info-row">
                        <span>Penyelenggara</span>
                        <strong><?= e(APP_ORGANIZATION) ?></strong>
                    </div>
                    <div class="info-row">
                        <span>Tahun</span>
                        <strong><?= e(APP_YEAR) ?></strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Teknologi</h2>
                    <p class="card-subtitle">Komponen yang digunakan.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="tech-chips" style="margin-top:0;padding-top:0;border-top:0">
                    <span class="tech-chip"><?= icon('code') ?>PHP Native</span>
                    <span class="tech-chip"><?= icon('database') ?>MySQL</span>
                    <span class="tech-chip"><?= icon('database') ?>PDO</span>
                    <span class="tech-chip"><?= icon('monitor') ?>HTML5</span>
                    <span class="tech-chip"><?= icon('layers') ?>CSS3</span>
                    <span class="tech-chip"><?= icon('code') ?>JavaScript</span>
                    <span class="tech-chip"><?= icon('flask') ?>Laragon</span>
                </div>
            </div>
        </section>

        <div class="alert alert-info">
            <?= icon('info') ?>
            <span>Nama resmi aplikasi adalah <strong>LAB BOOKING</strong>. Gunakan nama tersebut secara konsisten pada seluruh dokumen dan komunikasi.</span>
        </div>
    </aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
