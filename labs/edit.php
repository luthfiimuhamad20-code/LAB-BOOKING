<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

$labId = (int) (get('id') !== '' ? get('id') : post('id'));

if ($labId <= 0) {
    flash('danger', 'Laboratorium yang ingin diedit tidak ditemukan.');
    redirect('labs/index.php');
}

$existing = q('SELECT * FROM labs WHERE id = ? LIMIT 1', [$labId])->fetch();

if (!$existing) {
    flash('danger', 'Laboratorium yang ingin diedit tidak ditemukan.');
    redirect('labs/index.php');
}

$lab = $existing;
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
        $exists = (int) q('SELECT COUNT(*) FROM labs WHERE code = ? AND id <> ?', [$lab['code'], $labId])->fetchColumn();
        if ($exists > 0) {
            $errors['code'] = 'Kode lab tersebut sudah digunakan oleh laboratorium lain.';
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
            'UPDATE labs
             SET name = ?, code = ?, description = ?, location = ?, capacity = ?, facilities = ?, status = ?, updated_at = NOW()
             WHERE id = ?'
        );
        $statement->execute([
            $lab['name'],
            $lab['code'],
            $lab['description'] !== '' ? $lab['description'] : null,
            $lab['location'],
            (int) $lab['capacity'],
            $lab['facilities'] !== '' ? $lab['facilities'] : null,
            $lab['status'],
            $labId,
        ]);

        log_activity((int) current_user()['id'], 'lab_updated', 'Admin memperbarui laboratorium ' . $lab['name'] . '.');
        flash('success', 'Data laboratorium ' . $lab['name'] . ' berhasil diperbarui.');
        redirect('labs/view.php?id=' . $labId);
    }
}

$pageTitle = 'Edit Lab';
$active = 'labs';
$isEdit = true;
$formAction = url('labs/edit.php?id=' . $labId);

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('edit') ?>Manajemen Lab</span>
        <h1 class="page-title">Edit Laboratorium</h1>
        <p class="page-subtitle">Perbarui informasi <?= e($lab['name']) ?> pada katalog LAB BOOKING.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('labs/view.php?id=' . $labId)) ?>"><?= icon('arrow-left') ?>Kembali ke detail</a>
    </div>
</div>

<div class="booking-layout">
    <section class="card">
        <div class="card-header">
            <div class="card-heading">
                <h2 class="card-title">Informasi Laboratorium</h2>
                <p class="card-subtitle">Perubahan akan langsung tampil pada halaman pengguna.</p>
            </div>
            <?= lab_status_badge($lab['status']) ?>
        </div>
        <div class="card-body">
            <?php require __DIR__ . '/_form.php'; ?>
        </div>
    </section>

    <aside class="dash-stack">
        <section class="card">
            <div class="card-body">
                <div class="summary-lab">
                    <span class="summary-lab-mark"><?= icon(lab_icon_name($lab)) ?></span>
                    <div>
                        <strong><?= e($lab['name']) ?></strong>
                        <span><?= e($lab['code']) ?> · <?= e($lab['location']) ?></span>
                    </div>
                </div>
                <div class="summary-list">
                    <div class="summary-row">
                        <span><?= icon('users') ?>Kapasitas</span>
                        <strong><?= (int) $lab['capacity'] ?> orang</strong>
                    </div>
                    <div class="summary-row">
                        <span><?= icon('calendar-check') ?>Total booking</span>
                        <strong><?= (int) q('SELECT COUNT(*) FROM bookings WHERE lab_id = ?', [$labId])->fetchColumn() ?> booking</strong>
                    </div>
                    <div class="summary-row">
                        <span><?= icon('clock') ?>Terakhir diperbarui</span>
                        <strong><?= e(format_date($lab['updated_at'])) ?></strong>
                    </div>
                </div>
            </div>
        </section>

        <div class="alert alert-warning">
            <?= icon('alert-triangle') ?>
            <span>Mengubah status menjadi nonaktif akan mencegah pengguna mengajukan booking baru, tetapi data booking lama tetap tersimpan.</span>
        </div>
    </aside>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
