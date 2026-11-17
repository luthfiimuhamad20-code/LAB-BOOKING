<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

require_guest();

$errors = [
    'general'  => '',
    'email'    => '',
    'password' => '',
];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = strtolower(post('email'));
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'last' => 0];
    if ((int) $attempts['count'] > 0 && (time() - (int) $attempts['last']) > 300) {
        $attempts = ['count' => 0, 'last' => 0];
    }

    $locked = (int) $attempts['count'] >= 5 && (time() - (int) $attempts['last']) < 300;

    if ($locked) {
        $remaining = max(1, (int) ceil((300 - (time() - (int) $attempts['last'])) / 60));
        $errors['general'] = 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam ' . $remaining . ' menit.';
    } else {
        if ($email === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if ($password === '') {
            $errors['password'] = 'Password wajib diisi.';
        }

        if ($errors['email'] === '' && $errors['password'] === '') {
            $statement = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $statement->execute([$email]);
            $found = $statement->fetch();

            if (!$found || !password_verify($password, $found['password'])) {
                $attempts['count'] = (int) $attempts['count'] + 1;
                $attempts['last'] = time();
                $_SESSION['login_attempts'] = $attempts;
                $errors['general'] = 'Email atau password yang Anda masukkan tidak sesuai.';
            } elseif ($found['status'] !== 'active') {
                $errors['general'] = 'Akun Anda sedang nonaktif. Silakan hubungi admin laboratorium.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $found['id'];
                unset($_SESSION['login_attempts']);

                log_activity((int) $found['id'], 'login', $found['name'] . ' masuk ke LAB BOOKING.');
                flash('success', 'Selamat datang, ' . $found['name'] . '. Senang melihat Anda kembali.');
                redirect('dashboard.php');
            }
        }
    }
}

$authTitle = 'Masuk — ' . APP_NAME;
$authDescription = 'Masuk ke LAB BOOKING untuk mengajukan dan memantau booking laboratorium.';
require __DIR__ . '/includes/auth-header.php';
?>
            <span class="auth-head-eyebrow"><?= icon('shield') ?>Akses akun</span>
            <h1 class="auth-heading">Selamat datang kembali</h1>
            <p class="auth-subtitle">Masuk untuk mengajukan booking dan memantau jadwal laboratorium Anda.</p>

            <?php if (get('logout') === '1'): ?>
                <div class="alert alert-success" style="margin-top:16px">
                    <?= icon('check-circle') ?>
                    <span>Anda telah keluar dari LAB BOOKING dengan aman.</span>
                </div>
            <?php endif; ?>

            <?php if ($errors['general'] !== ''): ?>
                <div class="alert alert-danger" style="margin-top:16px">
                    <?= icon('alert-circle') ?>
                    <span><?= e($errors['general']) ?></span>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(url('login.php')) ?>" data-submit-once>
                <?= csrf_field() ?>

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
                            autocomplete="username"
                            inputmode="email"
                            required
                            autofocus
                        >
                        <?= icon('mail') ?>
                    </div>
                    <?php if ($errors['email'] !== ''): ?>
                        <span class="field-error"><?= icon('alert-circle') ?><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <div class="password-field">
                        <input
                            class="input<?= $errors['password'] !== '' ? ' has-error' : '' ?>"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
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

                <button class="btn btn-primary btn-block auth-submit" type="submit">
                    Masuk ke LAB BOOKING
                    <?= icon('arrow-right') ?>
                </button>
            </form>

            <div class="auth-divider">atau</div>

            <p class="auth-switch">
                Belum punya akun?
                <a href="<?= e(url('register.php')) ?>">Daftar sekarang</a>
            </p>

            <div class="demo-box">
                <span class="demo-box-title"><?= icon('key') ?>Akun demo</span>
                <div class="demo-accounts">
                    <button class="demo-account" type="button" data-demo-email="admin@labbooking.test" data-demo-password="Admin@123">
                        <strong>Admin LAB BOOKING</strong>
                        <span>admin@labbooking.test</span>
                    </button>
                    <button class="demo-account" type="button" data-demo-email="user@labbooking.test" data-demo-password="User@123">
                        <strong>Pengguna</strong>
                        <span>user@labbooking.test</span>
                    </button>
                </div>
                <p class="field-hint" style="margin-top:7px">Klik kartu akun untuk mengisi formulir secara otomatis.</p>
            </div>
<?php require __DIR__ . '/includes/auth-footer.php'; ?>
