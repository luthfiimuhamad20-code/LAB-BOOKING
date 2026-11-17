<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users/index.php');
}

verify_csrf();

$currentUser = current_user();
$accountId = (int) post('id');

if ($accountId <= 0) {
    flash('danger', 'Akun yang ingin dihapus tidak ditemukan.');
    redirect('users/index.php');
}

if ($accountId === (int) $currentUser['id']) {
    flash('danger', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
    redirect('users/index.php');
}

$account = q('SELECT * FROM users WHERE id = ? LIMIT 1', [$accountId])->fetch();

if (!$account) {
    flash('danger', 'Akun yang ingin dihapus tidak ditemukan.');
    redirect('users/index.php');
}

$bookingCount = (int) q('SELECT COUNT(*) FROM bookings WHERE user_id = ?', [$accountId])->fetchColumn();

if ($bookingCount > 0) {
    flash('danger', 'Akun ' . $account['name'] . ' memiliki ' . $bookingCount . ' riwayat booking sehingga tidak dapat dihapus. Nonaktifkan akun tersebut sebagai gantinya.');
    redirect('users/index.php');
}

q('DELETE FROM users WHERE id = ?', [$accountId]);
log_activity(
    (int) $currentUser['id'],
    'user_deleted',
    'Admin menghapus akun ' . $account['name'] . ' (' . $account['email'] . ').'
);
flash('success', 'Akun ' . $account['name'] . ' berhasil dihapus.');
redirect('users/index.php');
