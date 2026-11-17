<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

$account = [
    'id'         => 0,
    'name'       => '',
    'email'      => '',
    'role'       => 'user',
    'phone'      => '',
    'department' => '',
    'status'     => 'active',
];
$errors = [
    'general'    => '',
    'name'       => '',
    'email'      => '',
    'role'       => '',
    'phone'      => '',
    'department' => '',
    'status'     => '',
    'password'   => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $account['name'] = post('name');
    $account['email'] = strtolower(post('email'));
    $account['role'] = post('role', 'user');
    $account['phone'] = post('phone');
    $account['department'] = post('department');
    $account['status'] = post('status', 'active');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    if (mb_strlen($account['name']) < 3 || mb_strlen($account['name']) > 100) {
        $errors['name'] = 'Nama lengkap harus 3–100 karakter.';
    }

    if ($account['email'] === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($account['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        $exists = (int) q('SELECT COUNT(*) FROM users WHERE email = ?', [$account['email']])->fetchColumn();
        if ($exists > 0) {
            $errors['email'] = 'Email tersebut sudah digunakan akun lain.';
        }
    }

    if (!in_array($account['role'], ['admin', 'user'], true)) {
        $errors['role'] = 'Peran pengguna tidak valid.';
    }

    if (!in_array($account['status'], ['active', 'inactive'], true)) {
        $errors['status'] = 'Status akun tidak valid.';
    }

    if (mb_strlen($account['phone']) > 30) {
        $errors['phone'] = 'Nomor telepon maksimal 30 karakter.';
    }

    if (mb_strlen($account['department']) > 120) {
        $errors['department'] = 'Program studi atau unit maksimal 120 karakter.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password minimal 8 karakter.';
    } elseif (strlen($password) > 72) {
        $errors['password'] = 'Password maksimal 72 karakter.';
    }

    $hasErrors = implode('', $errors) !== '';

    if (!$hasErrors) {
        $statement = db()->prepare(
            "INSERT INTO users (name, email, password, role, phone, department, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $statement->execute([
            $account['name'],
            $account['email'],
            password_hash($password, PASSWORD_DEFAULT),
            $account['role'],
            $account['phone'] !== '' ? $account['phone'] : null,
            $account['department'] !== '' ? $account['department'] : null,
            $account['status'],
        ]);

        $newId = (int) db()->lastInsertId();
        log_activity(
            (int) current_user()['id'],
            'user_created',
            'Admin menambahkan pengguna ' . $account['name'] . ' (' . role_label($account['role']) . ').'
        );
        flash('success', 'Akun ' . $account['name'] . ' berhasil ditambahkan ke LAB BOOKING.');
        redirect('users/index.php');
    }
}

$pageTitle = 'Tambah Pengguna';
$active = 'users';
$isEdit = false;
$isSelf = false;
$formAction = url('users/create.php');

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('user-check') ?>Administrasi</span>
        <h1 class="page-title">Tambah Pengguna</h1>
        <p class="page-subtitle">Buat akun baru untuk admin atau pengguna laboratorium.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= e(url('users/index.php')) ?>"><?= icon('arrow-left') ?>Kembali</a>
    </div>
</div>

<div class="booking-layout">
    <section class="card">
        <div class="card-header">
            <div class="card-heading">
                <h2 class="card-title">Informasi Akun</h2>
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
                <div class="form-section-title"><?= icon('shield') ?>Panduan peran</div>
                <div class="steps">
                    <div class="step-item">
                        <span class="step-num">A</span>
                        <div>
                            <strong>Admin</strong>
                            <p>Dapat mengelola laboratorium, menyetujui booking, dan mengelola seluruh pengguna.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">U</span>
                        <div>
                            <strong>Pengguna</strong>
                            <p>Dapat mengajukan booking, melihat jadwal, dan memantau status pengajuannya.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-num">!</span>
                        <div>
                            <strong>Akun nonaktif</strong>
                            <p>Tidak dapat masuk ke LAB BOOKING hingga admin mengaktifkannya kembali.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="alert alert-info">
            <?= icon('key') ?>
            <span>Sampaikan email dan password kepada pengguna secara aman. Pengguna dapat mengganti password melalui halaman profil setelah masuk.</span>
        </div>
    </aside>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
