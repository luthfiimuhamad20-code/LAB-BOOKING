<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

require_guest();

$errors = [
    'general'    => '',
    'name'       => '',
    'email'      => '',
    'department' => '',
    'password'   => '',
    'confirm'    => '',
];
$name = '';
$email = '';
$department = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = post('name');
    $email = strtolower(post('email'));
    $department = post('department');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['password_confirmation']) && is_string($_POST['password_confirmation'])
        ? $_POST['password_confirmation']
        : '';

    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama lengkap minimal 3 karakter.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Nama lengkap maksimal 100 karakter.';
    }

    if ($email === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        $exists = (int) q('SELECT COUNT(*) FROM users WHERE email = ?', [$email])->fetchColumn();
        if ($exists > 0) {
            $errors['email'] = 'Email tersebut sudah terdaftar. Silakan masuk atau gunakan email lain.';
        }
    }

    if (mb_strlen($department) > 120) {
        $errors['department'] = 'Program studi atau unit maksimal 120 karakter.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password minimal 8 karakter.';
    }

    if ($confirm === '') {
        $errors['confirm'] = 'Konfirmasi password wajib diisi.';
    } elseif ($password !== $confirm) {
        $errors['confirm'] = 'Konfirmasi password tidak sama.';
    }

    $hasErrors = implode('', $errors) !== '';

    if (!$hasErrors) {
        $statement = db()->prepare(
            "INSERT INTO users (name, email, password, role, status, department, created_at, updated_at)
             VALUES (?, ?, ?, 'user', 'active', ?, NOW(), NOW())"
        );
        $statement->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $department !== '' ? $department : null,
        ]);

        $newUserId = (int) db()->lastInsertId();
        log_activity($newUserId, 'register', $name . ' mendaftar sebagai pengguna LAB BOOKING.');
        flash('success', 'Akun LAB BOOKING berhasil dibuat. Silakan masuk dengan email dan password Anda.');
        redirect('login.php');
    }
}

$authTitle = 'Daftar Akun — ' . APP_NAME;
$authDescription = 'Buat akun LAB BOOKING untuk mengajukan peminjaman laboratorium.';
require __DIR__ . '/includes/auth-header.php';
?>
            <span class="auth-head-eyebrow"><?= icon('user-check') ?>Akun baru</span>
            <h1 class="auth-heading">Daftar akun LAB BOOKING</h1>
            <p class="auth-subtitle">Lengkapi data berikut untuk mulai mengajukan booking laboratorium.</p>

            <?php if ($errors['general'] !== ''): ?>
                <div class="alert alert-danger" style="margin-top:16px">
                    <?= icon('alert-circle') ?>
                    <span><?= e($errors['general']) ?></span>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(url('register.php')) ?>" data-submit-once>
                <?= csrf_field() ?>

                <div class="field">
                    <label class="field-label" for="name">Nama lengkap</label>
                    <div class="input-icon">
                        <input
                            class="input<?= $errors['name'] !== '' ? ' has-error' : '' ?>"
                            type="text"
                            id="name"
                            name="name"
                            value="<?= e($name) ?>"
                            placeholder="Nama lengkap Anda"
                            autocomplete="name"
                            maxlength="100"
                            required
                            autofocus
                        >
                        <?= icon('user') ?>
                    </div>
                    <?php if ($errors['name'] !== ''): ?>
                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['name']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="field-label" for="email">Alamat email</label>
                    <div class="input-icon">
                        <input
                            class="input<?= $errors['email'] !== '' ? ' has-error' : '' ?>"
                            type="email"
                            id="email"
                            name="email"
                            value="<?= e($email) ?>"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            inputmode="email"
                            required
                        >
                        <?= icon('mail') ?>
                    </div>
                    <?php if ($errors['email'] !== ''): ?>
                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="field-label" for="department">Program studi / unit <span class="text-muted">(opsional)</span></label>
                    <div class="input-icon">
                        <input
                            class="input<?= $errors['department'] !== '' ? ' has-error' : '' ?>"
                            type="text"
                            id="department"
                            name="department"
                            value="<?= e($department) ?>"
                            placeholder="Contoh: Teknik Informatika"
                            autocomplete="organization"
                            maxlength="120"
                        >
                        <?= icon('building') ?>
                    </div>
                    <?php if ($errors['department'] !== ''): ?>
                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['department']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-grid cols-2">
                    <div class="field">
                        <label class="field-label" for="password">Password</label>
                        <div class="password-field">
                            <input
                                class="input<?= $errors['password'] !== '' ? ' has-error' : '' ?>"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >
                            <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Tampilkan password" aria-pressed="false">
                                <span class="icon-eye"><?= icon('eye') ?></span>
                                <span class="icon-eye-off"><?= icon('eye-off') ?></span>
                            </button>
                        </div>
                        <?php if ($errors['password'] !== ''): ?>
                            <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['password']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field-label" for="password_confirmation">Konfirmasi password</label>
                        <div class="password-field">
                            <input
                                class="input<?= $errors['confirm'] !== '' ? ' has-error' : '' ?>"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >
                            <button class="password-toggle" type="button" data-toggle-password="password_confirmation" aria-label="Tampilkan password" aria-pressed="false">
                                <span class="icon-eye"><?= icon('eye') ?></span>
                                <span class="icon-eye-off"><?= icon('eye-off') ?></span>
                            </button>
                        </div>
                        <?php if ($errors['confirm'] !== ''): ?>
                            <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['confirm']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <button class="btn btn-primary btn-block auth-submit" type="submit">
                    Buat akun LAB BOOKING
                    <?= icon('arrow-right') ?>
                </button>
            </form>

            <div class="auth-divider">atau</div>

            <p class="auth-switch">
                Sudah punya akun?
                <a href="<?= e(url('login.php')) ?>">Masuk di sini</a>
            </p>
<?php require __DIR__ . '/includes/auth-footer.php'; ?>
