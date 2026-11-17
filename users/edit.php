<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

$currentUser = current_user();
$accountId = (int) (get('id') !== '' ? get('id') : post('id'));

if ($accountId <= 0) {
    flash('danger', 'Akun yang ingin diedit tidak ditemukan.');
    redirect('users/index.php');
}

$existing = q('SELECT * FROM users WHERE id = ? LIMIT 1', [$accountId])->fetch();

if (!$existing) {
    flash('danger', 'Akun yang ingin diedit tidak ditemukan.');
    redirect('users/index.php');
}

$account = $existing;
$isSelf = (int) $account['id'] === (int) $currentUser['id'];
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
    $account['role'] = post('role', 'admin');
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
        $exists = (int) q(
            'SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?',
            [$account['email'], $accountId]
        )->fetchColumn();
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

    if ($isSelf && $account['role'] !== 'admin') {
        $errors['role'] = 'Anda tidak dapat menurunkan peran akun sendiri.';
    }

    if ($isSelf && $account['status'] !== 'active') {
        $errors['status'] = 'Akun yang sedang digunakan tidak dapat dinonaktifkan.';
    }

    if (mb_strlen($account['phone']) > 30) {
        $errors['phone'] = 'Nomor telepon maksimal 30 karakter.';
    }

    if (mb_strlen($account['department']) > 120) {
        $errors['department'] = 'Program studi atau unit maksimal 120 karakter.';
    }

    if ($password !== '') {
        if (strlen($password) < 8) {
            $errors['password'] = 'Password minimal 8 karakter.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'Password maksimal 72 karakter.';
        }
    }

    $hasErrors = implode('', $errors) !== '';

    if (!$hasErrors) {
        if ($password !== '') {
            $statement = db()->prepare(
                'UPDATE users
                 SET name = ?, email = ?, password = ?, role = ?, phone = ?, department = ?, status = ?, updated_at = NOW()
                 WHERE id = ?'
            );
            $statement->execute([
                $account['name'],
                $account['email'],
                password_hash($password, PASSWORD_DEFAULT),
                $account['role'],
                $account['phone'] !== '' ? $account['phone'] : null,
                $account['department'] !== '' ? $account['department'] : null,
                $account['status'],
                $accountId,
            ]);
        } else {
            $statement = db()->prepare(
                'UPDATE users
                 SET name = ?, email = ?, role = ?, phone = ?, department = ?, status = ?, updated_at = NOW()
                 WHERE id = ?'
            );
            $statement->execute([
                $account['name'],
                $account['email'],
                $account['role'],
                $account['phone'] !== '' ? $account['phone'] : null,
                $account['department'] !== '' ? $account['department'] : null,
                $account['status'],
                $accountId,
            ]);
        }

        log_activity(
            (int) $currentUser['id'],
            'user_updated',
            'Admin memperbarui akun ' . $account['name'] . '.'
        );
        flash('success', 'Data akun ' . $account['name'] . ' berhasil diperbarui.');
        redirect('users/index.php');
    }
}

$bookingCount = (int) q('SELECT COUNT(*) FROM bookings WHERE user_id = ?', [$accountId])->fetchColumn();
$pageTitle = 'Edit Pengguna';
$active = 'users';
$isEdit = true;
$formAction = url('users/edit.php?id=' . $accountId);

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('edit') ?>Administrasi</span>
        <h1 class="page-title">Edit Pengguna</h1>
        <p class="page-subtitle">Perbarui data akun, peran, dan status akses LAB BOOKING.</p>
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
                <p class="card-subtitle">Perubahan langsung berlaku setelah disimpan.</p>
            </div>
            <?= user_status_badge($account['status']) ?>
        </div>
        <div class="card-body">
            <?php require __DIR__ . '/_form.php'; ?>
        </div>
    </section>

    <aside class="dash-stack">
        <section class="card">
            <div class="card-body">
                <div class="summary-lab">
                    <?= avatar($account['name']) ?>
                    <div>
                        <strong><?= e($account['name']) ?></strong>
                        <span><?= e($account['email']) ?></span>
                    </div>
                </div>
                <div class="summary-list">
                    <div class="summary-row">
                        <span><?= icon('shield') ?>Peran</span>
                        <strong><?= e(role_label($account['role'])) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span><?= icon('calendar-check') ?>Total booking</span>
                        <strong><?= $bookingCount ?> booking</strong>
                    </div>
                    <div class="summary-row">
                        <span><?= icon('calendar') ?>Terdaftar</span>
                        <strong><?= e(format_date($account['created_at'])) ?></strong>
                    </div>
                    <?php if ($isSelf): ?>
                        <div class="summary-row">
                            <span><?= icon('user') ?>Status akun</span>
                            <strong>Akun Anda</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if ($isSelf): ?>
            <div class="alert alert-info">
                <?= icon('info') ?>
                <span>Anda mengedit akun yang sedang digunakan. Peran admin dan status aktif dikunci demi keamanan akses.</span>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <?= icon('alert-triangle') ?>
                <span>Menonaktifkan akun akan langsung mencegah pengguna masuk ke LAB BOOKING. Booking lama tetap tersimpan.</span>
            </div>
        <?php endif; ?>
    </aside>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
