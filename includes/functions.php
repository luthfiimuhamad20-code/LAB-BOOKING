<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/icons.php';

/*
|--------------------------------------------------------------------------
| Helper umum
|--------------------------------------------------------------------------
*/

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    if (!preg_match('~^https?://~i', $path)) {
        $path = url($path);
    }

    header('Location: ' . $path);
    exit;
}

function previous_url(string $fallback = 'dashboard.php'): string
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if ($referer !== '' && $host !== '') {
        $refererHost = parse_url($referer, PHP_URL_HOST);
        $refererPort = (int) (parse_url($referer, PHP_URL_PORT) ?? 0);
        $currentHost = parse_url('http://' . $host, PHP_URL_HOST);
        $currentPort = (int) (parse_url('http://' . $host, PHP_URL_PORT) ?? 0);

        if ($refererHost && $currentHost && strcasecmp((string) $refererHost, (string) $currentHost) === 0
            && $refererPort === $currentPort) {
            return $referer;
        }
    }

    return url($fallback);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function get_flashes(): array
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);

    return is_array($messages) ? $messages : [];
}

function post(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    return is_scalar($value) ? trim((string) $value) : $default;
}

function get(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;

    return is_scalar($value) ? trim((string) $value) : $default;
}

function old(string $key, string $default = ''): string
{
    return post($key, $default);
}

function checked(bool $condition): string
{
    return $condition ? ' checked' : '';
}

function selected(bool $condition): string
{
    return $condition ? ' selected' : '';
}

function excerpt(?string $text, int $length = 120): string
{
    $text = trim((string) $text);

    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $length, '…', 'UTF-8');
    }

    return strlen($text) > $length ? substr($text, 0, $length) . '…' : $text;
}

/*
|--------------------------------------------------------------------------
| Autentikasi & otorisasi
|--------------------------------------------------------------------------
*/

function current_user(): ?array
{
    static $resolved = false;
    static $user = null;

    if ($resolved) {
        return $user;
    }

    $resolved = true;
    $userId = (int) ($_SESSION['user_id'] ?? 0);

    if ($userId <= 0) {
        return $user = null;
    }

    $statement = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $statement->execute([$userId]);
    $found = $statement->fetch();

    if (!$found || $found['status'] !== 'active') {
        $_SESSION = [];
        return $user = null;
    }

    return $user = $found;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();

    return $user !== null && $user['role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Silakan masuk terlebih dahulu untuk mengakses halaman tersebut.');
        redirect('login.php');
    }

    sync_completed_bookings();
}

function require_guest(): void
{
    if (is_logged_in()) {
        redirect('dashboard.php');
    }
}

function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        flash('danger', 'Halaman tersebut hanya dapat diakses oleh admin.');
        redirect('dashboard.php');
    }
}

/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        flash('danger', 'Sesi keamanan tidak valid atau telah berakhir. Silakan coba lagi.');
        redirect(previous_url('dashboard.php'));
    }
}

/*
|--------------------------------------------------------------------------
| Query singkat
|--------------------------------------------------------------------------
*/

function q(string $sql, array $parameters = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($parameters);

    return $statement;
}

/*
|--------------------------------------------------------------------------
| Label & tampilan
|--------------------------------------------------------------------------
*/

function booking_statuses(): array
{
    return [
        'pending'   => ['label' => 'Menunggu', 'class' => 'warning'],
        'approved'  => ['label' => 'Disetujui', 'class' => 'success'],
        'rejected'  => ['label' => 'Ditolak', 'class' => 'danger'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'neutral'],
        'completed' => ['label' => 'Selesai', 'class' => 'info'],
    ];
}

function booking_status_label(string $status): string
{
    return booking_statuses()[$status]['label'] ?? ucfirst($status);
}

function booking_status_badge(string $status, bool $dot = true): string
{
    $meta = booking_statuses()[$status] ?? ['label' => ucfirst($status), 'class' => 'neutral'];
    $dotHtml = $dot ? '<span class="badge-dot"></span>' : '';

    return '<span class="badge badge-' . e($meta['class']) . '">' . $dotHtml . e($meta['label']) . '</span>';
}

function role_label(string $role): string
{
    return $role === 'admin' ? 'Admin' : 'Pengguna';
}

function role_badge(string $role): string
{
    $class = $role === 'admin' ? 'primary' : 'neutral';

    return '<span class="badge badge-' . $class . '">' . e(role_label($role)) . '</span>';
}

function user_status_badge(string $status): string
{
    $active = $status === 'active';

    return '<span class="badge badge-' . ($active ? 'success' : 'neutral') . '"><span class="badge-dot"></span>'
        . ($active ? 'Aktif' : 'Nonaktif') . '</span>';
}

function lab_status_badge(string $status): string
{
    $active = $status === 'active';

    return '<span class="badge badge-' . ($active ? 'success' : 'neutral') . '"><span class="badge-dot"></span>'
        . ($active ? 'Tersedia' : 'Nonaktif') . '</span>';
}

function initials(string $name): string
{
    $name = trim($name);

    if ($name === '') {
        return 'LB';
    }

    $parts = preg_split('/\s+/', $name) ?: [$name];

    if (count($parts) === 1) {
        return mb_strtoupper(mb_substr($parts[0], 0, 2));
    }

    return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
}

function avatar(string $name, string $class = ''): string
{
    $safeClass = $class !== '' ? ' ' . e($class) : '';

    return '<span class="avatar' . $safeClass . '" aria-hidden="true">' . e(initials($name)) . '</span>';
}

function greeting(): string
{
    $hour = (int) date('G');

    if ($hour < 11) {
        return 'Selamat pagi';
    }

    if ($hour < 15) {
        return 'Selamat siang';
    }

    if ($hour < 18) {
        return 'Selamat sore';
    }

    return 'Selamat malam';
}

function lab_icon_name(array $lab): string
{
    $code = strtoupper((string) ($lab['code'] ?? ''));
    $name = strtolower((string) ($lab['name'] ?? ''));

    if (str_starts_with($code, 'LM') || str_contains($name, 'multimedia')) {
        return 'video';
    }

    if (str_starts_with($code, 'LJ') || str_contains($name, 'jaringan')) {
        return 'network';
    }

    if (str_starts_with($code, 'LR') || str_contains($name, 'robot')) {
        return 'bot';
    }

    return 'monitor';
}

function facility_list(?string $facilities): array
{
    if ($facilities === null || trim($facilities) === '') {
        return [];
    }

    $items = preg_split('/\r\n|\r|\n|,/', $facilities) ?: [];

    return array_values(array_filter(array_map('trim', $items), static fn ($item) => $item !== ''));
}

/*
|--------------------------------------------------------------------------
| Tanggal & waktu
|--------------------------------------------------------------------------
*/

function format_date(?string $date, string $style = 'short', bool $withDay = false): string
{
    if ($date === null || $date === '' || $date === '0000-00-00') {
        return '-';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return '-';
    }

    $months = [
        1  => 'Januari', 2  => 'Februari', 3 => 'Maret', 4 => 'April',
        5  => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9  => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $monthsShort = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    $monthName = $style === 'long'
        ? $months[(int) date('n', $timestamp)]
        : $monthsShort[(int) date('n', $timestamp)];

    $formatted = date('j', $timestamp) . ' ' . $monthName . ' ' . date('Y', $timestamp);

    if ($withDay) {
        $formatted = $days[(int) date('w', $timestamp)] . ', ' . $formatted;
    }

    return $formatted;
}

function format_datetime(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '-';
    }

    $timestamp = strtotime($datetime);

    if ($timestamp === false) {
        return '-';
    }

    return format_date(date('Y-m-d', $timestamp)) . ' · ' . date('H:i', $timestamp);
}

function format_time(?string $time): string
{
    if ($time === null || $time === '') {
        return '-';
    }

    return substr($time, 0, 5);
}

function format_time_range(?string $start, ?string $end): string
{
    return format_time($start) . ' – ' . format_time($end);
}

function time_options(): array
{
    $options = [];

    for ($minutes = 7 * 60; $minutes <= 22 * 60; $minutes += 30) {
        $options[] = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    return $options;
}

function minutes_between(string $start, string $end): int
{
    $startTimestamp = strtotime('1970-01-01 ' . $start);
    $endTimestamp = strtotime('1970-01-01 ' . $end);

    if ($startTimestamp === false || $endTimestamp === false) {
        return 0;
    }

    return max(0, (int) round(($endTimestamp - $startTimestamp) / 60));
}

function format_duration(int $minutes): string
{
    if ($minutes <= 0) {
        return '0 menit';
    }

    $hours = intdiv($minutes, 60);
    $remaining = $minutes % 60;

    if ($hours > 0 && $remaining > 0) {
        return $hours . ' jam ' . $remaining . ' menit';
    }

    if ($hours > 0) {
        return $hours . ' jam';
    }

    return $remaining . ' menit';
}

function is_valid_date(string $date): bool
{
    $parsed = DateTime::createFromFormat('Y-m-d', $date);

    return $parsed !== false && $parsed->format('Y-m-d') === $date;
}

function is_valid_time(string $time): bool
{
    return (bool) preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time);
}

/*
|--------------------------------------------------------------------------
| Booking
|--------------------------------------------------------------------------
*/

function has_booking_conflict(
    int $labId,
    string $date,
    string $start,
    string $end,
    array $statuses = ['pending', 'approved'],
    ?int $excludeId = null
): bool {
    $statuses = array_values(array_filter($statuses));
    $placeholders = implode(',', array_fill(0, count($statuses), '?'));

    $sql = "SELECT COUNT(*) FROM bookings
            WHERE lab_id = ?
              AND booking_date = ?
              AND status IN ($placeholders)
              AND start_time < ?
              AND end_time > ?";
    $parameters = array_merge([$labId, $date], $statuses, [$end, $start]);

    if ($excludeId !== null) {
        $sql .= ' AND id <> ?';
        $parameters[] = $excludeId;
    }

    return (int) q($sql, $parameters)->fetchColumn() > 0;
}

function generate_booking_code(): string
{
    do {
        $code = 'BK-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $exists = (int) q('SELECT COUNT(*) FROM bookings WHERE booking_code = ?', [$code])->fetchColumn() > 0;
    } while ($exists);

    return $code;
}

function sync_completed_bookings(): void
{
    static $synced = false;

    if ($synced) {
        return;
    }

    $synced = true;

    try {
        db()->exec("UPDATE bookings
                    SET status = 'completed'
                    WHERE status = 'approved'
                      AND TIMESTAMP(booking_date, end_time) < NOW()");
    } catch (Throwable $exception) {
        // Pembaruan status otomatis tidak boleh mengganggu halaman.
    }
}

function log_activity(?int $userId, string $action, string $description): void
{
    try {
        q(
            'INSERT INTO activity_logs (user_id, action, description, created_at) VALUES (?, ?, ?, NOW())',
            [$userId, $action, $description]
        );
    } catch (Throwable $exception) {
        // Aktivitas tidak kritis; jangan hentikan alur utama.
    }
}

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

function pagination(int $total, int $page, int $perPage, string $basePath, array $query = []): string
{
    $pages = (int) ceil($total / max(1, $perPage));

    if ($pages <= 1) {
        return '';
    }

    $page = max(1, min($page, $pages));
    $buildUrl = static function (int $target) use ($basePath, $query): string {
        $query['page'] = $target;

        return url($basePath) . '?' . http_build_query($query);
    };

    $windowStart = max(1, $page - 2);
    $windowEnd = min($pages, $page + 2);

    $html = '<nav class="pagination" aria-label="Navigasi halaman">';
    $html .= '<a class="page-btn' . ($page <= 1 ? ' is-disabled' : '') . '" href="'
        . ($page > 1 ? e($buildUrl($page - 1)) : '#') . '" aria-label="Halaman sebelumnya">'
        . icon('chevron-left') . '</a>';

    if ($windowStart > 1) {
        $html .= '<a class="page-btn" href="' . e($buildUrl(1)) . '">1</a>';
        if ($windowStart > 2) {
            $html .= '<span class="page-ellipsis">…</span>';
        }
    }

    for ($index = $windowStart; $index <= $windowEnd; $index++) {
        $html .= '<a class="page-btn' . ($index === $page ? ' is-active' : '') . '" href="'
            . e($buildUrl($index)) . '"' . ($index === $page ? ' aria-current="page"' : '') . '>'
            . $index . '</a>';
    }

    if ($windowEnd < $pages) {
        if ($windowEnd < $pages - 1) {
            $html .= '<span class="page-ellipsis">…</span>';
        }
        $html .= '<a class="page-btn" href="' . e($buildUrl($pages)) . '">' . $pages . '</a>';
    }

    $html .= '<a class="page-btn' . ($page >= $pages ? ' is-disabled' : '') . '" href="'
        . ($page < $pages ? e($buildUrl($page + 1)) : '#') . '" aria-label="Halaman berikutnya">'
        . icon('chevron-right') . '</a>';
    $html .= '</nav>';

    return $html;
}

function pagination_info(int $total, int $page, int $perPage): string
{
    if ($total <= 0) {
        return 'Tidak ada data';
    }

    $page = max(1, $page);
    $start = ($page - 1) * $perPage + 1;
    $end = min($total, $page * $perPage);

    return 'Menampilkan ' . $start . '–' . $end . ' dari ' . $total . ' data';
}
