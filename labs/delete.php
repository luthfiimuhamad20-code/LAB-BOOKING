<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('labs/index.php');
}

verify_csrf();

$labId = (int) post('id');

if ($labId <= 0) {
    flash('danger', 'Laboratorium yang ingin dihapus tidak ditemukan.');
    redirect('labs/index.php');
}

$lab = q('SELECT * FROM labs WHERE id = ? LIMIT 1', [$labId])->fetch();

if (!$lab) {
    flash('danger', 'Laboratorium yang ingin dihapus tidak ditemukan.');
    redirect('labs/index.php');
}

$bookingCount = (int) q('SELECT COUNT(*) FROM bookings WHERE lab_id = ?', [$labId])->fetchColumn();

if ($bookingCount > 0) {
    flash('danger', 'Lab ' . $lab['name'] . ' memiliki ' . $bookingCount . ' riwayat booking sehingga tidak dapat dihapus. Nonaktifkan lab tersebut sebagai gantinya.');
    redirect('labs/index.php');
}

q('DELETE FROM labs WHERE id = ?', [$labId]);
log_activity((int) current_user()['id'], 'lab_deleted', 'Admin menghapus laboratorium ' . $lab['name'] . '.');
flash('success', 'Laboratorium ' . $lab['name'] . ' berhasil dihapus.');
redirect('labs/index.php');
