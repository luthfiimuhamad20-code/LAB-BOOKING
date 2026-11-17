<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();

$profile = [
    'name'       => $user['name'],
    'email'      => $user['email'],
    'phone'      => $user['phone'] ?? '',
    'department' => $user['department'] ?? '',
];
$profileErrors = [
    'name'       => '',
    'email'      => '',
    'phone'      => '',
    'department' => '',
];
$passwordErrors = [
    'current' => '',
    'new'     => '',
    'confirm' => '',
];
$activeForm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $activeForm = post('form');

    if ($activeForm === 'profile') {
        $profile['name'] = post('name');
        $profile['email'] = strtolower(post('email'));
        $profile['phone'] = post('phone');
        $profile['department'] = post('department');

        if (mb_strlen($profile['name']) < 3 || mb_strlen($profile['name']) > 100) {
            $profileErrors['name'] = 'Nama lengkap harus 3–100 karakter.';
        }

        if ($profile['email'] === '') {
            $profileErrors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($profile['email'], FILTER_VALIDATE_EMAIL)) {
            $profileErrors['email'] = 'Format email tidak valid.';
        } else {
            $exists = (int) q(
                'SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?',
                [$profile['email'], (int) $user['id']]
            )->fetchColumn();
            if ($exists > 0) {
                $profileErrors['email'] = 'Email tersebut sudah digunakan akun lain.';
            }
        }

        if (mb_strlen($profile['phone']) > 30) {
            $profileErrors['phone'] = 'Nomor telepon maksimal 30 karakter.';
        }

        if (mb_strlen($profile['department']) > 120) {
            $profileErrors['department'] = 'Program studi atau unit maksimal 120 karakter.';
        }

        if (implode('', $profileErrors) === '') {
            q(
                'UPDATE users SET name = ?, email = ?, phone = ?, department = ?, updated_at = NOW() WHERE id = ?',
                [
                    $profile['name'],
                    $profile['email'],
                    $profile['phone'] !== '' ? $profile['phone'] : null,
                    $profile['department'] !== '' ? $profile['department'] : null,
                    (int) $user['id'],
                ]
            );

            log_activity((int) $user['id'], 'profile_updated', $user['name'] . ' memperbarui profil akun.');
            flash('success', 'Profil Anda berhasil diperbarui.');
            redirect('profile.php');
        }
    }

    if ($activeForm === 'password') {
        $currentPassword = isset($_POST['current_password']) && is_string($_POST['current_password'])
            ? $_POST['current_password']
            : '';
        $newPassword = isset($_POST['new_password']) && is_string($_POST['new_password'])
            ? $_POST['new_password']
            : '';
        $confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])
            ? $_POST['confirm_password']
            : '';

        if ($currentPassword === '') {
            $passwordErrors['current'] = 'Masukkan password saat ini.';
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $passwordErrors['current'] = 'Password saat ini tidak sesuai.';
        }

        if (strlen($newPassword) < 8) {
            $passwordErrors['new'] = 'Password baru minimal 8 karakter.';
        } elseif (strlen($newPassword) > 72) {
            $passwordErrors['new'] = 'Password baru maksimal 72 karakter.';
        } elseif ($currentPassword !== '' && password_verify($newPassword, $user['password'])) {
            $passwordErrors['new'] = 'Password baru harus berbeda dari password saat ini.';
        }

        if ($confirmPassword === '') {
            $passwordErrors['confirm'] = 'Konfirmasi password baru wajib diisi.';
        } elseif ($newPassword !== $confirmPassword) {
            $passwordErrors['confirm'] = 'Konfirmasi password tidak sama.';
        }

        if (implode('', $passwordErrors) === '') {
            q(
                'UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?',
                [password_hash($newPassword, PASSWORD_DEFAULT), (int) $user['id']]
            );

            log_activity((int) $user['id'], 'password_changed', $user['name'] . ' mengganti password akun.');
            flash('success', 'Password Anda berhasil diperbarui. Gunakan password baru pada sesi masuk berikutnya.');
            redirect('profile.php');
        }
    }
}

$bookingStats = q(
    "SELECT COUNT(*) AS total,
            SUM(status = 'pending') AS pending,
            SUM(status = 'approved') AS approved,
            SUM(status = 'completed') AS completed
     FROM bookings WHERE user_id = ?",
    [(int) $user['id']]
)->fetch();

$pageTitle = 'Profil Saya';
$active = 'profile';

require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('user') ?>Akun</span>
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Kelola informasi akun dan keamanan akses LAB BOOKING Anda.</p>
    </div>
</div>

<div class="profile-layout">
    <div class="dash-stack">
        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Informasi Profil</h2>
                    <p class="card-subtitle">Pastikan data akun Anda selalu up to date.</p>
                </div>
            </div>
            <div class="card-body">
                <?php if (implode('', $profileErrors) !== ''): ?>
                    <div class="alert alert-danger" style="margin-bottom:16px">
                        <?= icon('alert-circle') ?>
                        <span>Periksa kembali data profil yang Anda masukkan.</span>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('profile.php')) ?>" data-submit-once>
                    <?= csrf_field() ?>
                    <input type="hidden" name="form" value="profile">

                    <div class="form-grid">
                        <div class="form-grid cols-2">
                            <div class="field">
                                <label class="field-label" for="name">Nama lengkap <span class="req">*</span></label>
                                <input class="input<?= $profileErrors['name'] !== '' ? ' has-error' : '' ?>" type="text" id="name" name="name" value="<?= e($profile['name']) ?>" maxlength="100" required>
                                <?php if ($profileErrors['name'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($profileErrors['name']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field-label" for="email">Alamat email <span class="req">*</span></label>
                                <input class="input<?= $profileErrors['email'] !== '' ? ' has-error' : '' ?>" type="email" id="email" name="email" value="<?= e($profile['email']) ?>" maxlength="150" required>
                                <?php if ($profileErrors['email'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($profileErrors['email']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-grid cols-2">
                            <div class="field">
                                <label class="field-label" for="phone">Nomor telepon</label>
                                <input class="input<?= $profileErrors['phone'] !== '' ? ' has-error' : '' ?>" type="text" id="phone" name="phone" value="<?= e($profile['phone']) ?>" maxlength="30" inputmode="tel" placeholder="08xxxxxxxxxx">
                                <?php if ($profileErrors['phone'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($profileErrors['phone']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field-label" for="department">Program studi / unit</label>
                                <input class="input<?= $profileErrors['department'] !== '' ? ' has-error' : '' ?>" type="text" id="department" name="department" value="<?= e($profile['department']) ?>" maxlength="120" placeholder="Contoh: Teknik Informatika">
                                <?php if ($profileErrors['department'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($profileErrors['department']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field-label">Peran akun</label>
                            <div class="flex items-center gap-8">
                                <?= role_badge($user['role']) ?>
                                <span class="field-hint">Peran hanya dapat diubah oleh admin.</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit"><?= icon('check') ?>Simpan Profil</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <div class="card-heading">
                    <h2 class="card-title">Ubah Password</h2>
                    <p class="card-subtitle">Gunakan kombinasi password yang kuat dan mudah Anda ingat.</p>
                </div>
                <span class="badge badge-neutral"><?= icon('lock') ?>Keamanan</span>
            </div>
            <div class="card-body">
                <?php if (implode('', $passwordErrors) !== ''): ?>
                    <div class="alert alert-danger" style="margin-bottom:16px">
                        <?= icon('alert-circle') ?>
                        <span>Password belum dapat diperbarui. Periksa kembali isian Anda.</span>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('profile.php')) ?>" data-submit-once>
                    <?= csrf_field() ?>
                    <input type="hidden" name="form" value="password">

                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label" for="current_password">Password saat ini <span class="req">*</span></label>
                            <div class="password-field">
                                <input class="input<?= $passwordErrors['current'] !== '' ? ' has-error' : '' ?>" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                                <button class="password-toggle" type="button" data-toggle-password="current_password" aria-label="Tampilkan password" aria-pressed="false">
                                    <span class="icon-eye"><?= icon('eye') ?></span>
                                    <span class="icon-eye-off"><?= icon('eye-off') ?></span>
                                </button>
                            </div>
                            <?php if ($passwordErrors['current'] !== ''): ?>
                                <span class="field-error"><?= icon('alert-circle') ?><?= e($passwordErrors['current']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-grid cols-2">
                            <div class="field">
                                <label class="field-label" for="new_password">Password baru <span class="req">*</span></label>
                                <div class="password-field">
                                    <input class="input<?= $passwordErrors['new'] !== '' ? ' has-error' : '' ?>" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8" required>
                                    <button class="password-toggle" type="button" data-toggle-password="new_password" aria-label="Tampilkan password" aria-pressed="false">
                                        <span class="icon-eye"><?= icon('eye') ?></span>
                                        <span class="icon-eye-off"><?= icon('eye-off') ?></span>
                                    </button>
                                </div>
                                <?php if ($passwordErrors['new'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($passwordErrors['new']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field-label" for="confirm_password">Konfirmasi password <span class="req">*</span></label>
                                <div class="password-field">
                                    <input class="input<?= $passwordErrors['confirm'] !== '' ? ' has-error' : '' ?>" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required>
                                    <button class="password-toggle" type="button" data-toggle-password="confirm_password" aria-label="Tampilkan password" aria-pressed="false">
                                        <span class="icon-eye"><?= icon('eye') ?></span>
                                        <span class="icon-eye-off"><?= icon('eye-off') ?></span>
                                    </button>
                                </div>
                                <?php if ($passwordErrors['confirm'] !== ''): ?>
                                    <span class="field-error"><?= icon('alert-circle') ?><?= e($passwordErrors['confirm']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit"><?= icon('lock') ?>Perbarui Password</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <aside class="sticky-panel">
        <section class="card">
            <div class="profile-card">
                <?= avatar($user['name'], 'avatar-lg') ?>
                <h2 class="profile-name"><?= e($user['name']) ?></h2>
                <p class="profile-email"><?= e($user['email']) ?></p>
                <div class="profile-role"><?= role_badge($user['role']) ?></div>

                <div class="profile-stats">
                    <div class="profile-stat">
                        <strong><?= (int) $bookingStats['total'] ?></strong>
                        <span>Total booking</span>
                    </div>
                    <div class="profile-stat">
                        <strong><?= (int) $bookingStats['pending'] ?></strong>
                        <span>Menunggu</span>
                    </div>
                    <div class="profile-stat">
                        <strong><?= (int) $bookingStats['completed'] ?></strong>
                        <span>Selesai</span>
                    </div>
                    <div class="profile-stat">
                        <strong><?= (int) $bookingStats['approved'] ?></strong>
                        <span>Disetujui</span>
                    </div>
                </div>

                <div class="profile-meta">
                    <div class="profile-meta-row">
                        <?= icon('building') ?>
                        <div>
                            <span>Program studi / unit</span>
                            <strong><?= e($user['department'] ?: 'Belum diisi') ?></strong>
                        </div>
                    </div>
                    <div class="profile-meta-row">
                        <?= icon('phone') ?>
                        <div>
                            <span>Nomor telepon</span>
                            <strong><?= e($user['phone'] ?: 'Belum diisi') ?></strong>
                        </div>
                    </div>
                    <div class="profile-meta-row">
                        <?= icon('calendar') ?>
                        <div>
                            <span>Bergabung sejak</span>
                            <strong><?= e(format_date($user['created_at'])) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="alert alert-info" style="margin-top:14px">
            <?= icon('shield') ?>
            <span>Jaga kerahasiaan password Anda. Admin LAB BOOKING tidak akan pernah meminta password melalui pesan pribadi.</span>
        </div>
    </aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
