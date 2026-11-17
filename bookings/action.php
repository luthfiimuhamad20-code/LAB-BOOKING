<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('bookings/index.php');
}

verify_csrf();

$user = current_user();
$isAdmin = $user['role'] === 'admin';
$action = post('action');
$bookingId = (int) post('id');

if ($bookingId <= 0) {
    flash('danger', 'Booking yang dimaksud tidak ditemukan.');
    redirect('bookings/index.php');
}

$booking = q(
    'SELECT b.*, l.name AS lab_name, u.name AS user_name
     FROM bookings b
     JOIN labs l ON l.id = b.lab_id
     JOIN users u ON u.id = b.user_id
     WHERE b.id = ?
     LIMIT 1',
    [$bookingId]
)->fetch();

if (!$booking) {
    flash('danger', 'Booking yang dimaksud tidak ditemukan.');
    redirect('bookings/index.php');
}

$backUrl = 'bookings/view.php?id=' . $bookingId;
$isOwner = (int) $booking['user_id'] === (int) $user['id'];
$endTimestamp = strtotime($booking['booking_date'] . ' ' . $booking['end_time']);

if (!in_array($action, ['approve', 'reject', 'cancel'], true)) {
    flash('danger', 'Tindakan booking tidak dikenali.');
    redirect($backUrl);
}

if (($action === 'approve' || $action === 'reject') && !$isAdmin) {
    flash('danger', 'Hanya admin yang dapat menyetujui atau menolak booking.');
    redirect($backUrl);
}

if ($action === 'cancel' && !$isAdmin && !$isOwner) {
    flash('danger', 'Anda tidak memiliki akses untuk membatalkan booking ini.');
    redirect($backUrl);
}

if ($action === 'approve') {
    if ($booking['status'] !== 'pending') {
        flash('warning', 'Booking ini sudah tidak berstatus menunggu sehingga tidak dapat disetujui.');
        redirect($backUrl);
    }

    if ($endTimestamp !== false && $endTimestamp <= time()) {
        flash('danger', 'Waktu booking sudah berlalu sehingga tidak dapat disetujui.');
        redirect($backUrl);
    }

    if (has_booking_conflict(
        (int) $booking['lab_id'],
        $booking['booking_date'],
        $booking['start_time'],
        $booking['end_time'],
        ['approved'],
        $bookingId
    )) {
        flash('danger', 'Booking tidak dapat disetujui karena jadwalnya bertabrakan dengan booking lain yang sudah disetujui.');
        redirect($backUrl);
    }

    q(
        "UPDATE bookings
         SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
         WHERE id = ? AND status = 'pending'",
        [(int) $user['id'], $bookingId]
    );

    log_activity(
        (int) $user['id'],
        'booking_approved',
        $user['name'] . ' menyetujui booking ' . $booking['booking_code'] . ' di ' . $booking['lab_name'] . '.'
    );
    flash('success', 'Booking ' . $booking['booking_code'] . ' berhasil disetujui.');
    redirect(previous_url('bookings/index.php'));
}

if ($action === 'reject') {
    if ($booking['status'] !== 'pending') {
        flash('warning', 'Hanya booking berstatus menunggu yang dapat ditolak.');
        redirect($backUrl);
    }

    $note = post('admin_note');

    if (mb_strlen($note) < 5) {
        flash('danger', 'Tuliskan alasan penolakan minimal 5 karakter.');
        redirect($backUrl);
    }

    if (mb_strlen($note) > 1000) {
        flash('danger', 'Alasan penolakan maksimal 1000 karakter.');
        redirect($backUrl);
    }

    q(
        "UPDATE bookings
         SET status = 'rejected', admin_note = ?, reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
         WHERE id = ? AND status = 'pending'",
        [$note, (int) $user['id'], $bookingId]
    );

    log_activity(
        (int) $user['id'],
        'booking_rejected',
        $user['name'] . ' menolak booking ' . $booking['booking_code'] . ' di ' . $booking['lab_name'] . '.'
    );
    flash('success', 'Booking ' . $booking['booking_code'] . ' telah ditolak dan pemohon dapat melihat alasannya.');
    redirect(previous_url('bookings/index.php'));
}

// Aksi pembatalan
if (!in_array($booking['status'], ['pending', 'approved'], true)) {
    flash('warning', 'Booking dengan status tersebut tidak dapat dibatalkan.');
    redirect($backUrl);
}

if ($endTimestamp !== false && $endTimestamp <= time()) {
    flash('danger', 'Booking yang sudah lewat tidak dapat dibatalkan.');
    redirect($backUrl);
}

q(
    "UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND status IN ('pending', 'approved')",
    [$bookingId]
);

log_activity(
    (int) $user['id'],
    'booking_cancelled',
    $user['name'] . ' membatalkan booking ' . $booking['booking_code'] . ' di ' . $booking['lab_name'] . '.'
);
flash('success', 'Booking ' . $booking['booking_code'] . ' berhasil dibatalkan.');
redirect(previous_url('bookings/index.php'));
