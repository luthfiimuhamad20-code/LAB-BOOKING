<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_login();

$user = current_user();
$labs = q("SELECT * FROM labs WHERE status = 'active' ORDER BY name ASC")->fetchAll();

$form = [
    'lab_id'       => (int) (get('lab_id') !== '' ? get('lab_id') : post('lab_id')),
    'booking_date' => date('Y-m-d'),
    'start_time'   => '09:00',
    'end_time'     => '11:00',
    'purpose'      => '',
    'participants' => '',
];

$errors = [
    'general'      => '',
    'lab_id'       => '',
    'booking_date' => '',
    'start_time'   => '',
    'end_time'     => '',
    'purpose'      => '',
    'participants' => '',
];

if ($form['lab_id'] <= 0 && $labs !== []) {
    $form['lab_id'] = (int) $labs[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $form['lab_id'] = (int) post('lab_id');
    $form['booking_date'] = post('booking_date');
    $form['start_time'] = post('start_time');
    $form['end_time'] = post('end_time');
    $form['purpose'] = post('purpose');
    $form['participants'] = post('participants');

    $selectedLab = null;
    foreach ($labs as $labOption) {
        if ((int) $labOption['id'] === $form['lab_id']) {
            $selectedLab = $labOption;
            break;
        }
    }

    if ($selectedLab === null) {
        $errors['lab_id'] = 'Pilih laboratorium yang tersedia.';
    }

    if (!is_valid_date($form['booking_date'])) {
        $errors['booking_date'] = 'Tanggal booking tidak valid.';
    } elseif ($form['booking_date'] < date('Y-m-d')) {
        $errors['booking_date'] = 'Tanggal booking tidak boleh di masa lalu.';
    } elseif ($form['booking_date'] > date('Y-m-d', strtotime('+90 days'))) {
        $errors['booking_date'] = 'Booking hanya dapat diajukan maksimal 90 hari ke depan.';
    }

    $validTimes = time_options();

    if (!in_array($form['start_time'], $validTimes, true)) {
        $errors['start_time'] = 'Pilih jam mulai yang valid.';
    }

    if (!in_array($form['end_time'], $validTimes, true)) {
        $errors['end_time'] = 'Pilih jam selesai yang valid.';
    }

    if ($errors['start_time'] === '' && $errors['end_time'] === '') {
        if (minutes_between($form['start_time'], $form['end_time']) < 30) {
            $errors['end_time'] = 'Jam selesai harus lebih lambat dari jam mulai.';
        } elseif (minutes_between($form['start_time'], $form['end_time']) > 8 * 60) {
            $errors['end_time'] = 'Durasi booking maksimal 8 jam dalam satu pengajuan.';
        }
    }

    if (mb_strlen($form['purpose']) < 10) {
        $errors['purpose'] = 'Jelaskan tujuan booking minimal 10 karakter.';
    } elseif (mb_strlen($form['purpose']) > 255) {
        $errors['purpose'] = 'Tujuan booking maksimal 255 karakter.';
    }

    $participants = filter_var($form['participants'], FILTER_VALIDATE_INT);
    if ($participants === false || $participants < 1) {
        $errors['participants'] = 'Jumlah peserta minimal 1 orang.';
    } elseif ($selectedLab !== null && $participants > (int) $selectedLab['capacity']) {
        $errors['participants'] = 'Jumlah peserta melebihi kapasitas lab (' . (int) $selectedLab['capacity'] . ' orang).';
    } else {
        $form['participants'] = (string) $participants;
    }

    if ($selectedLab !== null
        && $errors['booking_date'] === ''
        && $errors['start_time'] === ''
        && $errors['end_time'] === ''
        && has_booking_conflict($form['lab_id'], $form['booking_date'], $form['start_time'], $form['end_time'])
    ) {
        $errors['general'] = 'Jadwal yang Anda pilih sudah dibooking pada laboratorium ini. Silakan pilih tanggal atau waktu lain.';
    }

    $hasErrors = implode('', $errors) !== '';

    if (!$hasErrors && $selectedLab !== null) {
        $bookingCode = generate_booking_code();
        $statement = db()->prepare(
            "INSERT INTO bookings
                (booking_code, user_id, lab_id, booking_date, start_time, end_time, purpose, participants, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
        );
        $statement->execute([
            $bookingCode,
            (int) $user['id'],
            $form['lab_id'],
            $form['booking_date'],
            $form['start_time'] . ':00',
            $form['end_time'] . ':00',
            $form['purpose'],
            (int) $form['participants'],
        ]);

        $bookingId = (int) db()->lastInsertId();
        log_activity(
            (int) $user['id'],
            'booking_created',
            $user['name'] . ' mengajukan booking ' . $bookingCode . ' di ' . $selectedLab['name'] . '.'
        );
        flash('success', 'Booking ' . $bookingCode . ' berhasil diajukan dan menunggu persetujuan admin.');
        redirect('bookings/view.php?id=' . $bookingId);
    }
}

$selectedLab = null;
foreach ($labs as $labOption) {
    if ((int) $labOption['id'] === (int) $form['lab_id']) {
        $selectedLab = $labOption;
        break;
    }
}
if ($selectedLab === null && $labs !== []) {
    $selectedLab = $labs[0];
}

$pageTitle = 'Buat Booking';
$active = 'bookings-create';

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('calendar-check') ?>Pemesanan laboratorium</span>
        <h1 class="page-title">Buat Booking</h1>
        <p class="page-subtitle">Isi detail kegiatan Anda. Pengajuan akan ditinjau oleh admin laboratorium sebelum disetujui.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('schedule.php')) ?>"><?= icon('calendar') ?>Lihat Jadwal</a>
    </div>
</div>

<?php if ($labs === []): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><?= icon('flask') ?></div>
            <h3>Belum ada laboratorium tersedia</h3>
            <p>Admin belum mengaktifkan laboratorium untuk pemesanan. Silakan hubungi pengelola laboratorium.</p>
            <a class="btn btn-secondary btn-sm" href="<?= e(url('labs/index.php')) ?>">Lihat daftar lab</a>
        </div>
    </div>
<?php else: ?>
    <div class="booking-layout">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Detail Booking</h2>
                    <p class="card-subtitle">Lengkapi data pemakaian laboratorium dengan benar.</p>
                </div>
            </div>
            <div class="card-body">
                <?php if ($errors['general'] !== ''): ?>
                    <div class="alert alert-danger" style="margin-bottom:17px">
                        <?= icon('alert-circle') ?>
                        <span><?= e($errors['general']) ?></span>
                    </div>
                <?php endif; ?>

                <form id="booking-form" method="post" action="<?= e(url('bookings/create.php')) ?>" data-submit-once data-slots-url="<?= e(url('api/slots.php')) ?>">
                    <?= csrf_field() ?>

                    <div class="form-section">
                        <div class="form-section-title"><?= icon('flask') ?>Laboratorium & jadwal</div>

                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="lab_id">Laboratorium <span class="req">*</span></label>
                                <select class="select<?= $errors['lab_id'] !== '' ? ' has-error' : '' ?>" id="lab_id" name="lab_id" required>
                                    <?php foreach ($labs as $labOption): ?>
                                        <option
                                            value="<?= (int) $labOption['id'] ?>"
                                            data-name="<?= e($labOption['name']) ?>"
                                            data-code="<?= e($labOption['code']) ?>"
                                            data-location="<?= e($labOption['location']) ?>"
                                            data-capacity="<?= (int) $labOption['capacity'] ?>"
                                            <?= selected((int) $form['lab_id'] === (int) $labOption['id']) ?>
                                        >
                                            <?= e($labOption['name']) ?> (<?= e($labOption['code']) ?>) — <?= e($labOption['location']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($errors['lab_id'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['lab_id']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-grid cols-2">
                                <div class="field">
                                    <label class="field-label" for="booking_date">Tanggal <span class="req">*</span></label>
                                    <input
                                        class="input<?= $errors['booking_date'] !== '' ? ' has-error' : '' ?>"
                                        type="date"
                                        id="booking_date"
                                        name="booking_date"
                                        value="<?= e($form['booking_date']) ?>"
                                        min="<?= e(date('Y-m-d')) ?>"
                                        max="<?= e(date('Y-m-d', strtotime('+90 days'))) ?>"
                                        required
                                    >
                                    <?php if ($errors['booking_date'] !== ''): ?>
                                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['booking_date']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="field">
                                    <label class="field-label">Waktu pemakaian <span class="req">*</span></label>
                                    <div class="form-grid cols-2" style="gap:8px">
                                        <select class="select<?= $errors['start_time'] !== '' ? ' has-error' : '' ?>" id="start_time" name="start_time" aria-label="Jam mulai" required>
                                            <?php foreach (time_options() as $time): ?>
                                                <option value="<?= e($time) ?>"<?= selected($form['start_time'] === $time) ?>><?= e($time) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select class="select<?= $errors['end_time'] !== '' ? ' has-error' : '' ?>" id="end_time" name="end_time" aria-label="Jam selesai" required>
                                            <?php foreach (time_options() as $time): ?>
                                                <option value="<?= e($time) ?>"<?= selected($form['end_time'] === $time) ?>><?= e($time) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <span class="field-hint">Jam operasional 07.00–22.00 · maksimal 8 jam per sesi.</span>
                                    <?php if ($errors['start_time'] !== ''): ?>
                                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['start_time']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($errors['end_time'] !== ''): ?>
                                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['end_time']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><?= icon('clipboard') ?>Detail kegiatan</div>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="purpose">Tujuan / nama kegiatan <span class="req">*</span></label>
                                <textarea
                                    class="textarea<?= $errors['purpose'] !== '' ? ' has-error' : '' ?>"
                                    id="purpose"
                                    name="purpose"
                                    placeholder="Contoh: Praktikum Pemrograman Web — sesi responsi proyek akhir"
                                    maxlength="255"
                                    minlength="10"
                                    required
                                ><?= e($form['purpose']) ?></textarea>
                                <span class="field-hint">Jelaskan kegiatan secara singkat agar admin dapat meninjau pengajuan.</span>
                                <?php if ($errors['purpose'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['purpose']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field-label" for="participants">Jumlah peserta <span class="req">*</span></label>
                                <input
                                    class="input<?= $errors['participants'] !== '' ? ' has-error' : '' ?>"
                                    type="number"
                                    id="participants"
                                    name="participants"
                                    value="<?= e($form['participants']) ?>"
                                    placeholder="Contoh: 25"
                                    min="1"
                                    max="500"
                                    required
                                >
                                <span class="field-hint">Jumlah peserta tidak boleh melebihi kapasitas laboratorium.</span>
                                <?php if ($errors['participants'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['participants']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a class="btn btn-secondary" href="<?= e(url('bookings/index.php')) ?>">Batal</a>
                        <button class="btn btn-primary" type="submit"><?= icon('send') ?>Ajukan Booking</button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="sticky-panel">
            <section class="card">
                <div class="card-body">
                    <div class="summary-lab">
                        <span class="summary-lab-mark"><?= icon($selectedLab ? lab_icon_name($selectedLab) : 'flask') ?></span>
                        <div>
                            <strong id="summaryLabName"><?= e($selectedLab['name'] ?? '-') ?></strong>
                            <span id="summaryLabMeta"><?= e(($selectedLab['code'] ?? '-') . ' · ' . ($selectedLab['location'] ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="summary-list">
                        <div class="summary-row">
                            <span><?= icon('map-pin') ?>Lokasi</span>
                            <strong id="summaryLocation"><?= e($selectedLab['location'] ?? '-') ?></strong>
                        </div>
                        <div class="summary-row">
                            <span><?= icon('users') ?>Kapasitas</span>
                            <strong id="summaryCapacity"><?= (int) ($selectedLab['capacity'] ?? 0) ?> orang</strong>
                        </div>
                        <div class="summary-row">
                            <span><?= icon('clock') ?>Jam operasional</span>
                            <strong>07.00 – 22.00</strong>
                        </div>
                    </div>

                    <div class="availability-panel">
                        <div class="availability-title">
                            <span>Jadwal pada tanggal</span>
                            <span class="badge badge-neutral" id="summaryLabCode"><?= e($selectedLab['code'] ?? '-') ?></span>
                        </div>
                        <div class="availability-list" id="availabilityList">
                            <div class="slot-item"><span>Memuat jadwal…</span></div>
                        </div>
                        <p class="field-hint" id="availabilityEmpty" style="display:none;margin-top:7px">Belum ada booking lain pada tanggal ini.</p>
                        <div class="legend">
                            <span><i class="dot-approved"></i>Disetujui</span>
                            <span><i class="dot-pending"></i>Menunggu</span>
                        </div>
                        <div class="conflict-note" id="conflictNote">
                            <?= icon('alert-triangle') ?>
                            <span>Waktu yang Anda pilih bertabrakan dengan booking lain. Pilih jam yang berbeda.</span>
                        </div>
                    </div>

                    <div class="booking-note">
                        <?= icon('info') ?>
                        <span>Booking akan berstatus <strong>Menunggu</strong> hingga admin memberikan persetujuan.</span>
                    </div>
                </div>
            </section>
        </aside>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
