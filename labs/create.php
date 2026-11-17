<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

$lab = [
    'id'          => 0,
    'name'        => '',
    'code'        => '',
    'description' => '',
    'location'    => '',
    'capacity'    => '20',
    'facilities'  => '',
    'status'      => 'active',
];
$errors = [
    'name'        => '',
    'code'        => '',
    'description' => '',
    'location'    => '',
    'capacity'    => '',
    'facilities'  => '',
    'status'      => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $lab['name'] = post('name');
    $lab['code'] = strtoupper(post('code'));
    $lab['description'] = post('description');
    $lab['location'] = post('location');
    $lab['capacity'] = post('capacity');
    $lab['facilities'] = post('facilities');
    $lab['status'] = post('status', 'active');

    if (mb_strlen($lab['name']) < 3 || mb_strlen($lab['name']) > 120) {
        $errors['name'] = 'Nama laboratorium harus 3–120 karakter.';
    }

    if (!preg_match('/^[A-Z0-9-]{2,20}$/', $lab['code'])) {
        $errors['code'] = 'Kode lab harus 2–20 karakter berupa huruf kapital, angka, atau tanda hubung.';
    } else {
        $exists = (int) q('SELECT COUNT(*) FROM labs WHERE code = ?', [$lab['code']])->fetchColumn();
        if ($exists > 0) {
            $errors['code'] = 'Kode lab tersebut sudah digunakan.';
        }
    }

    if (mb_strlen($lab['location']) < 3 || mb_strlen($lab['location']) > 150) {
        $errors['location'] = 'Lokasi harus 3–150 karakter.';
    }

    $capacity = filter_var($lab['capacity'], FILTER_VALIDATE_INT);
    if ($capacity === false || $capacity < 1 || $capacity > 500) {
        $errors['capacity'] = 'Kapasitas harus berupa angka antara 1 sampai 500.';
    } else {
        $lab['capacity'] = (string) $capacity;
    }

    if (mb_strlen($lab['description']) > 2000) {
        $errors['description'] = 'Deskripsi maksimal 2000 karakter.';
    }

    if (mb_strlen($lab['facilities']) > 1000) {
        $errors['facilities'] = 'Fasilitas maksimal 1000 karakter.';
    }

    if (!in_array($lab['status'], ['active', 'inactive'], true)) {
        $errors['status'] = 'Status laboratorium tidak valid.';
    }

    $hasErrors = implode('', $errors) !== '';

    if (!$hasErrors) {
        $statement = db()->prepare(
            'INSERT INTO labs (name, code, description, location, capacity, facilities, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $statement->execute([
            $lab['name'],
            $lab['code'],
            $lab['description'] !== '' ? $lab['description'] : null,
            $lab['location'],
            (int) $lab['capacity'],
            $lab['facilities'] !== '' ? $lab['facilities'] : null,
            $lab['status'],
        ]);

        $newId = (int) db()->lastInsertId();
        log_activity((int) current_user()['id'], 'lab_created', 'Admin menambahkan laboratorium ' . $lab['name'] . '.');
        flash('success', 'Laboratorium ' . $lab['name'] . ' berhasil ditambahkan ke LAB BOOKING.');
        redirect('labs/view.php?id=' . $newId);
    }
}

$pageTitle = 'Tambah Lab';
$active = 'labs';
$isEdit = false;
$formAction = url('labs/create.php');

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('flask') ?>Manajemen Lab</span>
        <h1 class="page-title">Tambah Laboratorium</h1>
        <p class="page-subtitle">Lengkapi informasi laboratorium baru untuk ditampilkan pada katalog LAB BOOKING.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('labs/index.php')) ?>"><?= icon('arrow-left') ?>Kembali</a>
    </div>
</div>

<div class="booking-layout">
    <section class="card">
        <div class="card-header">
            <div class="card-heading">
                <h2 class="card-title">Informasi Laboratorium</h2>
                <p class="card-subtitle">Kolom bertanda bintang wajib diisi.</p>
            </div>
        </div>
        <div class="card-body">
            <?php require __DIR__ . '/_form.php'; ?>
        </div>
    </section>

    <aside class="dash-stack">
        <section class="card">
            <div class="card-body">
                <div class="summary-lab">
                    <span class="summary-lab-mark"><?= icon('flask') ?></span>
                    <div>
                        <strong>Panduan singkat</strong>
                        <span>Pengelolaan data lab</span>
                    </div>
                </div>
                <div class="steps">
                    <div class="step-item">
                        <span class="step-num">1</span>
                        <div>
                            <strong>Gunakan kode unik</strong>
                            <p>Kode lab membedakan setiap ruang, misalnya LK-01 atau LM-01.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">2</span>
                        <div>
                            <strong>Catat fasilitas utama</strong>
                            <p>Fasilitas membantu pengguna memilih laboratorium yang sesuai.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">3</span>
                        <div>
                            <strong>Atur status dengan tepat</strong>
                            <p>Nonaktifkan lab saat perawatan agar tidak muncul sebagai pilihan booking.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="alert alert-info">
            <?= icon('info') ?>
            <span>Lab yang masih memiliki riwayat booking tidak dapat dihapus. Nonaktifkan laboratorium tersebut agar data tetap aman.</span>
        </div>
    </aside>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
