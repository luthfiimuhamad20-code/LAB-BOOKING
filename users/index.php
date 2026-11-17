<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

$currentUser = current_user();
$pageTitle = 'Manajemen Pengguna';
$active = 'users';

$search = get('q');
$roleFilter = get('role');
if (!in_array($roleFilter, ['admin', 'user'], true)) {
    $roleFilter = '';
}
$statusFilter = get('status');
if (!in_array($statusFilter, ['active', 'inactive'], true)) {
    $statusFilter = '';
}

$page = max(1, (int) get('page', '1'));
$perPage = 10;

$conditions = [];
$parameters = [];

if ($search !== '') {
    $conditions[] = '(u.name LIKE ? OR u.email LIKE ? OR u.department LIKE ?)';
    $like = '%' . $search . '%';
    $parameters[] = $like;
    $parameters[] = $like;
    $parameters[] = $like;
}

if ($roleFilter !== '') {
    $conditions[] = 'u.role = ?';
    $parameters[] = $roleFilter;
}

if ($statusFilter !== '') {
    $conditions[] = 'u.status = ?';
    $parameters[] = $statusFilter;
}

$where = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';
$total = (int) q('SELECT COUNT(*) FROM users u ' . $where, $parameters)->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$users = q(
    'SELECT u.*,
            (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id) AS booking_count
     FROM users u ' . $where . '
     ORDER BY FIELD(u.role, \'admin\', \'user\'), u.name ASC
     LIMIT ' . $perPage . ' OFFSET ' . $offset,
    $parameters
)->fetchAll();

$summary = q(
    "SELECT COUNT(*) AS total,
            SUM(role = 'admin') AS admins,
            SUM(status = 'active') AS active_users,
            SUM(status = 'inactive') AS inactive_users
     FROM users"
)->fetch();

$hasFilter = $search !== '' || $roleFilter !== '' || $statusFilter !== '';
$paginationQuery = [];
if ($search !== '') {
    $paginationQuery['q'] = $search;
}
if ($roleFilter !== '') {
    $paginationQuery['role'] = $roleFilter;
}
if ($statusFilter !== '') {
    $paginationQuery['status'] = $statusFilter;
}

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div class="page-head-text">
        <span class="page-eyebrow"><?= icon('users') ?>Administrasi</span>
        <h1 class="page-title">Manajemen Pengguna</h1>
        <p class="page-subtitle">Kelola akun admin dan pengguna yang dapat mengakses LAB BOOKING.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="<?= e(url('users/create.php')) ?>"><?= icon('plus') ?>Tambah Pengguna</a>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card stat-primary">
        <div class="stat-top">
            <span class="stat-icon"><?= icon('users') ?></span>
            <span class="stat-trend">Total</span>
        </div>
        <span class="stat-value"><?= (int) $summary['total'] ?></span>
        <span class="stat-label">Pengguna terdaftar</span>
    </div>
    <div class="stat-card stat-violet">
        <div class="stat-top">
            <span class="stat-icon"><?= icon('shield') ?></span>
            <span class="stat-trend">Admin</span>
        </div>
        <span class="stat-value"><?= (int) $summary['admins'] ?></span>
        <span class="stat-label">Pengelola aplikasi</span>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-top">
            <span class="stat-icon"><?= icon('user-check') ?></span>
            <span class="stat-trend">Aktif</span>
        </div>
        <span class="stat-value"><?= (int) $summary['active_users'] ?></span>
        <span class="stat-label">Akun aktif</span>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-top">
            <span class="stat-icon"><?= icon('slash') ?></span>
            <span class="stat-trend">Nonaktif</span>
        </div>
        <span class="stat-value"><?= (int) $summary['inactive_users'] ?></span>
        <span class="stat-label">Akun nonaktif</span>
    </div>
</div>

<div class="filter-bar">
    <form class="filter-form" method="get" action="<?= e(url('users/index.php')) ?>">
        <div class="filter-search">
            <?= icon('search') ?>
            <input class="input" type="search" name="q" value="<?= e($search) ?>" placeholder="Cari nama, email, atau unit…" aria-label="Cari pengguna">
        </div>
        <select class="select" name="role" aria-label="Filter peran" data-auto-submit>
            <option value="">Semua peran</option>
            <option value="admin"<?= selected($roleFilter === 'admin') ?>>Admin</option>
            <option value="user"<?= selected($roleFilter === 'user') ?>>Pengguna</option>
        </select>
        <select class="select" name="status" aria-label="Filter status" data-auto-submit>
            <option value="">Semua status</option>
            <option value="active"<?= selected($statusFilter === 'active') ?>>Aktif</option>
            <option value="inactive"<?= selected($statusFilter === 'inactive') ?>>Nonaktif</option>
        </select>
        <button class="btn btn-secondary" type="submit"><?= icon('filter') ?>Terapkan</button>
        <?php if ($hasFilter): ?>
            <a class="filter-reset" href="<?= e(url('users/index.php')) ?>"><?= icon('x') ?>Reset</a>
        <?php endif; ?>
    </form>
</div>

<section class="card">
    <?php if ($users === []): ?>
        <div class="empty-state">
            <div class="empty-icon"><?= icon('users') ?></div>
            <h3>Pengguna tidak ditemukan</h3>
            <p>Tidak ada akun yang cocok dengan pencarian atau filter yang Anda gunakan.</p>
            <?php if ($hasFilter): ?>
                <a class="btn btn-secondary btn-sm" href="<?= e(url('users/index.php')) ?>">Tampilkan semua pengguna</a>
            <?php else: ?>
                <a class="btn btn-primary btn-sm" href="<?= e(url('users/create.php')) ?>"><?= icon('plus') ?>Tambah Pengguna</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Unit / Departemen</th>
                        <th>Kontak</th>
                        <th>Peran</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $account): ?>
                        <tr>
                            <td>
                                <div class="table-user">
                                    <?= avatar($account['name'], 'avatar-sm') ?>
                                    <div class="table-user-info">
                                        <strong>
                                            <?= e($account['name']) ?>
                                            <?php if ((int) $account['id'] === (int) $currentUser['id']): ?>
                                                <small class="badge badge-primary" style="margin-left:4px">Anda</small>
                                            <?php endif; ?>
                                        </strong>
                                        <span><?= e($account['email']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="cell-main"><?= e($account['department'] ?: '-') ?></span>
                                <span class="cell-sub"><?= (int) $account['booking_count'] ?> booking</span>
                            </td>
                            <td><?= e($account['phone'] ?: '-') ?></td>
                            <td><?= role_badge($account['role']) ?></td>
                            <td><?= user_status_badge($account['status']) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-icon btn-sm btn-secondary" href="<?= e(url('users/edit.php?id=' . (int) $account['id'])) ?>" title="Edit pengguna" aria-label="Edit <?= e($account['name']) ?>">
                                        <?= icon('edit') ?>
                                    </a>
                                    <?php if ((int) $account['id'] !== (int) $currentUser['id']): ?>
                                        <?php if ((int) $account['booking_count'] > 0): ?>
                                            <button class="btn btn-icon btn-sm btn-danger-soft is-disabled" type="button" title="Tidak dapat dihapus karena memiliki riwayat booking" aria-label="Tidak dapat dihapus" disabled>
                                                <?= icon('trash') ?>
                                            </button>
                                        <?php else: ?>
                                            <form method="post" action="<?= e(url('users/delete.php')) ?>" data-confirm="Hapus akun <?= e($account['name']) ?>? Tindakan ini tidak dapat dibatalkan.">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $account['id'] ?>">
                                                <button class="btn btn-icon btn-sm btn-danger-soft" type="submit" title="Hapus pengguna" aria-label="Hapus <?= e($account['name']) ?>">
                                                    <?= icon('trash') ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span><?= e(pagination_info($total, $page, $perPage)) ?></span>
            <?= pagination($total, $page, $perPage, 'users/index.php', $paginationQuery) ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
